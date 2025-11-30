<?php
include_once "../../assest/config/validarUsuarioFruta.php";
include_once "../../assest/controlador/EXIMATERIAPRIMA_ADO.php";
include_once "../../assest/controlador/PLANTA_ADO.php";
include_once "../../assest/controlador/ESPECIES_ADO.php";
include_once "../../assest/controlador/VESPECIES_ADO.php";

$EXIMATERIAPRIMA_ADO = new EXIMATERIAPRIMA_ADO();
$PLANTA_ADO = new PLANTA_ADO();
$ESPECIES_ADO = new ESPECIES_ADO();
$VESPECIES_ADO = new VESPECIES_ADO();

$ARRAYTEMPORADA = $TEMPORADA_ADO->listarTemporadaCBX();
$ARRAYESPECIE = $ESPECIES_ADO->listarEspeciesCBX();

$temporadaFiltro = isset($_REQUEST['TEMPORADA_FILTRO']) ? $_REQUEST['TEMPORADA_FILTRO'] : $TEMPORADAS;
$especieFiltro = isset($_REQUEST['ESPECIE_FILTRO']) ? $_REQUEST['ESPECIE_FILTRO'] : '';

$empresaSeleccionada = $EMPRESA_ADO->verEmpresa($EMPRESAS);
$nombreEmpresa = $empresaSeleccionada ? $empresaSeleccionada[0]['NOMBRE_EMPRESA'] : '';

if (!isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'])) {
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'] = [];
}

$mapVespeciesEspecie = [];
$ARRAYVESPECIES = $VESPECIES_ADO->listarVespeciesCBX();
foreach ($ARRAYVESPECIES as $variedad) {
    if (isset($variedad['ID_VESPECIES']) && isset($variedad['ID_ESPECIES'])) {
        $mapVespeciesEspecie[$variedad['ID_VESPECIES']] = $variedad['ID_ESPECIES'];
    }
}

$proyeccionesFiltradas = array_values(array_filter(
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'],
    function ($proyeccion) use ($temporadaFiltro, $especieFiltro) {
        $habilitado = !isset($proyeccion['habilitado']) || $proyeccion['habilitado'];
        $especieProyeccion = isset($proyeccion['especie']) ? $proyeccion['especie'] : '';
        $coincideEspecie = !$especieFiltro || !$especieProyeccion || $especieProyeccion == $especieFiltro;
        return $habilitado && $proyeccion['temporada'] == $temporadaFiltro && $coincideEspecie;
    }
));

$totalProyectado = 0;
$totalReal = 0;
$empresasReporte = [];
$proyeccionTotalEmpresa = [];
$kilosRealesPorEmpresaPlanta = [];
$kilosRealesTotales = [];
$empresasNombres = [];
$plantasNombres = [];

$empresasActivas = $EMPRESA_ADO->listarEmpresaCBX();
foreach ($empresasActivas as $empresaActiva) {
    $empresasReporte[$empresaActiva['ID_EMPRESA']] = true;
    $empresasNombres[$empresaActiva['ID_EMPRESA']] = $empresaActiva['NOMBRE_EMPRESA'];
    $proyeccionTotalEmpresa[$empresaActiva['ID_EMPRESA']] = 0;
    $kilosRealesTotales[$empresaActiva['ID_EMPRESA']] = ['granel' => 0, 'bulk' => 0, 'total' => 0];
}

foreach ($proyeccionesFiltradas as $proyeccion) {
    $kgProyectado = $proyeccion['kg_proyectado'];
    $empresaId = $proyeccion['empresa'];

    if (!isset($proyeccionTotalEmpresa[$empresaId])) {
        $proyeccionTotalEmpresa[$empresaId] = 0;
    }

    $proyeccionTotalEmpresa[$empresaId] += $kgProyectado;
    $totalProyectado += $kgProyectado;
}

foreach (array_keys($empresasReporte) as $empresaId) {
    $existenciasEmpresa = $EXIMATERIAPRIMA_ADO->listarEximateriaprimaEmpresaTemporada($empresaId, $temporadaFiltro);
    foreach ($existenciasEmpresa as $existencia) {
        if (!isset($existencia['ESTADO_REGISTRO']) || $existencia['ESTADO_REGISTRO'] != 1) {
            continue;
        }

        $idVespecies = isset($existencia['ID_VESPECIES']) ? $existencia['ID_VESPECIES'] : null;
        $especieAsociada = $idVespecies && isset($mapVespeciesEspecie[$idVespecies]) ? $mapVespeciesEspecie[$idVespecies] : '';
        if ($especieFiltro && $especieFiltro != $especieAsociada) {
            continue;
        }

        $kgReal = isset($existencia['KILOS_NETO_EXIMATERIAPRIMA']) ? floatval($existencia['KILOS_NETO_EXIMATERIAPRIMA']) : 0;
        $plantaId = isset($existencia['ID_PLANTA']) ? $existencia['ID_PLANTA'] : null;
        $agrupacion = isset($existencia['ID_AGERENCIAL']) ? intval($existencia['ID_AGERENCIAL']) : 0;

        if (!$plantaId) {
            continue;
        }

        if (!isset($kilosRealesPorEmpresaPlanta[$empresaId][$plantaId])) {
            $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId] = ['granel' => 0, 'bulk' => 0];
        }

        if ($agrupacion === 2) {
            $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['bulk'] += $kgReal;
            $kilosRealesTotales[$empresaId]['bulk'] += $kgReal;
        } else {
            $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['granel'] += $kgReal;
            $kilosRealesTotales[$empresaId]['granel'] += $kgReal;
        }

        $kilosRealesTotales[$empresaId]['total'] += $kgReal;
        $totalReal += $kgReal;

        if (!isset($plantasNombres[$plantaId])) {
            $plantaInfo = $PLANTA_ADO->verPlanta($plantaId);
            $plantasNombres[$plantaId] = $plantaInfo ? $plantaInfo[0]['NOMBRE_PLANTA'] : ('Planta ' . $plantaId);
        }
    }
}

$plantasReporte = array_keys($plantasNombres);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Informe gerencial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Informe gerencial de proyecciones" />
    <meta name="author" content="">
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <style>
        .metric-card {
            border: 0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            border-radius: 12px;
        }
        .metric-card .box-body { padding: 16px; }
        .tag { padding: 3px 8px; border-radius: 999px; font-size: 11px; }
        .tag-bulk { background: #fff4e5; color: #d9822b; }
        .tag-envasado { background: #e7f6ef; color: #2f855a; }
        .projection-table th, .projection-table td { font-size: 12px; }
        .section-title { font-weight: 600; font-size: 16px; }
        .badge-soft { padding: 6px 10px; border-radius: 10px; font-size: 12px; background: #f5f7fb; }
        .alert-soft { background: #f3f7ff; border: 1px solid #d4e2ff; color: #2d4b7a; }
    </style>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary" >
    <div class="wrapper">
        <?php include_once "../../assest/config/menuFruta.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <section class="content">
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Informe gerencial</h3>
                                <p class="mb-0">Distribución de kilos netos por empresa y planta.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Filtros</h4>
                                    <p class="mb-0 text-muted">Empresa base: <?php echo htmlspecialchars($nombreEmpresa); ?></p>
                                </div>
                                <div class="box-body">
                                    <form method="post" class="row align-items-end">
                                        <input type="hidden" name="TEMPORADA_FILTRO" value="<?php echo htmlspecialchars($temporadaFiltro); ?>">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <label>Especie</label>
                                            <select name="ESPECIE_FILTRO" class="form-control" onchange="this.form.submit()">
                                                <option value="" <?php echo $especieFiltro ? '' : 'selected'; ?>>Todas</option>
                                                <?php foreach ($ARRAYESPECIE as $ESPECIE) { ?>
                                                    <option value="<?php echo $ESPECIE['ID_ESPECIES']; ?>" <?php echo $ESPECIE['ID_ESPECIES'] == $especieFiltro ? 'selected' : ''; ?>>
                                                        <?php echo $ESPECIE['NOMBRE_ESPECIES']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-8 col-md-6 col-12 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary">Aplicar filtro</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Recepciones acumuladas y cumplimiento de lo proyectado</h4>
                                    <p class="mb-0 text-muted">Distribución por empresa y planta comparando kilos reales vs. proyectados.</p>
                                </div>
                                <div class="box-body table-responsive">
                                    <?php if ($empresasReporte && $plantasReporte) { ?>
                                        <table class="table table-bordered projection-table text-center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="align-middle text-left">Planta</th>
                                                    <?php foreach (array_keys($empresasReporte) as $empresaId) {
                                                        $proyectadoEmpresa = isset($proyeccionTotalEmpresa[$empresaId]) ? $proyeccionTotalEmpresa[$empresaId] : 0;
                                                        $realEmpresa = isset($kilosRealesTotales[$empresaId]['total']) ? $kilosRealesTotales[$empresaId]['total'] : 0;
                                                        $cumplimientoEmpresa = $proyectadoEmpresa > 0 ? ($realEmpresa / $proyectadoEmpresa) * 100 : 0;
                                                    ?>
                                                        <th colspan="3">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <span><?php echo htmlspecialchars($empresasNombres[$empresaId]); ?></span>
                                                                <span class="badge-soft" style="color: <?php echo $cumplimientoEmpresa >= 100 ? '#2f855a' : '#c53030'; ?>;">
                                                                    <?php echo round($cumplimientoEmpresa, 1); ?>%
                                                                </span>
                                                            </div>
                                                        </th>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <?php foreach (array_keys($empresasReporte) as $empresaId) { ?>
                                                        <th>Real granel</th>
                                                        <th>Real bulk</th>
                                                        <th>Proyectado</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($plantasReporte as $plantaId) { ?>
                                                    <tr>
                                                        <td class="text-left"><?php echo htmlspecialchars($plantasNombres[$plantaId]); ?></td>
                                                        <?php foreach (array_keys($empresasReporte) as $empresaId) {
                                                            $realPlantaGranel = isset($kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['granel']) ? $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['granel'] : 0;
                                                            $realPlantaBulk = isset($kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['bulk']) ? $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['bulk'] : 0;
                                                            $proyectadoEmpresa = isset($proyeccionTotalEmpresa[$empresaId]) ? $proyeccionTotalEmpresa[$empresaId] : 0;
                                                        ?>
                                                            <td class="text-right"><?php echo $realPlantaGranel ? number_format($realPlantaGranel, 0, ',', '.') : '-'; ?></td>
                                                            <td class="text-right"><?php echo $realPlantaBulk ? number_format($realPlantaBulk, 0, ',', '.') : '-'; ?></td>
                                                            <td class="text-right text-muted"><?php echo $proyectadoEmpresa ? number_format($proyectadoEmpresa, 0, ',', '.') : '-'; ?></td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                                <tr class="font-weight-600">
                                                    <td class="text-left">Subtotal</td>
                                                    <?php foreach (array_keys($empresasReporte) as $empresaId) {
                                                        $proyectadoEmpresa = isset($proyeccionTotalEmpresa[$empresaId]) ? $proyeccionTotalEmpresa[$empresaId] : 0;
                                                        $realEmpresaGranel = isset($kilosRealesTotales[$empresaId]['granel']) ? $kilosRealesTotales[$empresaId]['granel'] : 0;
                                                        $realEmpresaBulk = isset($kilosRealesTotales[$empresaId]['bulk']) ? $kilosRealesTotales[$empresaId]['bulk'] : 0;
                                                    ?>
                                                        <td class="text-right"><?php echo $realEmpresaGranel ? number_format($realEmpresaGranel, 0, ',', '.') : '-'; ?></td>
                                                        <td class="text-right"><?php echo $realEmpresaBulk ? number_format($realEmpresaBulk, 0, ',', '.') : '-'; ?></td>
                                                        <td class="text-right"><?php echo $proyectadoEmpresa ? number_format($proyectadoEmpresa, 0, ',', '.') : '-'; ?></td>
                                                    <?php } ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <div class="alert alert-soft mb-0">No hay datos de recepciones o proyecciones para construir el resumen por empresa.</div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>
            </div>
        </div>
    </div>

    <?php include_once "../../assest/config/urlBase.php"; ?>
</body>
</html>
