<?php
include_once "../../assest/config/validarUsuarioFruta.php";
include_once "../../assest/controlador/ERECEPCION_ADO.php";

$ERECEPCION_ADO = new ERECEPCION_ADO();

$ARRAYTEMPORADA = $TEMPORADA_ADO->listarTemporadaCBX();
$empresaFiltro = $EMPRESAS;
$temporadaFiltro = isset($_REQUEST['TEMPORADA_FILTRO']) ? $_REQUEST['TEMPORADA_FILTRO'] : $TEMPORADAS;

$ARRAYESTANDAR = [];
if ($empresaFiltro) {
    $ARRAYESTANDAR = $ERECEPCION_ADO->listarEstandarPorEmpresaCBX($empresaFiltro);
}

if (!isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'])) {
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'] = [];
}

$nombreEstandares = [];
if ($ARRAYESTANDAR) {
    foreach ($ARRAYESTANDAR as $estandar) {
        $nombreEstandares[$estandar['ID_ESTANDAR']] = $estandar['NOMBRE_ESTANDAR'];
    }
}

$mensajeExito = "";
if (isset($_POST['AGREGAR_PROYECCION'])) {
    $temporadaFiltro = isset($_POST['TEMPORADA_FILTRO']) ? $_POST['TEMPORADA_FILTRO'] : $temporadaFiltro;
    $semana = isset($_POST['SEMANA']) ? intval($_POST['SEMANA']) : 0;
    $kgProyectado = isset($_POST['KG_PROYECTADO']) ? floatval(str_replace([",", " "], [".", ""], $_POST['KG_PROYECTADO'])) : 0;
    $estandarSeleccionado = isset($_POST['ESTANDAR']) ? $_POST['ESTANDAR'] : '';
    $descripcionEstandar = $estandarSeleccionado && isset($nombreEstandares[$estandarSeleccionado]) ? $nombreEstandares[$estandarSeleccionado] : '';
    $tipoEmbalaje = $descripcionEstandar;

    if ($semana > 0 && $kgProyectado > 0) {
        $esBulk = stripos($tipoEmbalaje, 'bulk') !== false || stripos($descripcionEstandar, 'bulk') !== false;

        $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][] = [
            'empresa' => $empresaFiltro,
            'temporada' => $temporadaFiltro,
            'semana' => $semana,
            'kg_proyectado' => $kgProyectado,
            'tipo_embalaje' => $tipoEmbalaje,
            'descripcion_estandar' => $descripcionEstandar,
            'es_bulk' => $esBulk,
            'creado' => date('Y-m-d H:i')
        ];

        $mensajeExito = "Proyección agregada para la semana " . $semana . ".";
    }
}

$proyeccionesFiltradas = array_values(array_filter(
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'],
    function ($proyeccion) use ($empresaFiltro, $temporadaFiltro) {
        return $proyeccion['empresa'] == $empresaFiltro && $proyeccion['temporada'] == $temporadaFiltro;
    }
));

$weeklyProjection = [];
$totalProyectado = 0;
$totalBulk = 0;
$totalEnvasado = 0;

foreach ($proyeccionesFiltradas as $proyeccion) {
    $kgProyectado = $proyeccion['kg_proyectado'];
    $semana = $proyeccion['semana'];
    $esBulk = $proyeccion['es_bulk'];

    if (!isset($weeklyProjection[$semana])) {
        $weeklyProjection[$semana] = 0;
    }

    $weeklyProjection[$semana] += $kgProyectado;
    $totalProyectado += $kgProyectado;

    if ($esBulk) {
        $totalBulk += $kgProyectado;
    } else {
        $totalEnvasado += $kgProyectado;
    }
}

ksort($weeklyProjection);
$empresaSeleccionada = $EMPRESA_ADO->verEmpresa($empresaFiltro);
$nombreEmpresa = $empresaSeleccionada ? $empresaSeleccionada[0]['NOMBRE_EMPRESA'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ingresar proyección</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Registro de proyecciones semanales por empresa" />
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
                                <h3 class="page-title">Ingresar proyección</h3>
                                <p class="mb-0">Proyección de kilos netos por empresa y temporada.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Seleccionar temporada</h4>
                                    <p class="mb-0 text-muted">Empresa: <?php echo htmlspecialchars($nombreEmpresa); ?></p>
                                </div>
                                <div class="box-body">
                                    <form method="post" class="row align-items-end">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <label>Temporada</label>
                                            <select name="TEMPORADA_FILTRO" class="form-control" onchange="this.form.submit()">
                                                <?php foreach ($ARRAYTEMPORADA as $TEMP) { ?>
                                                    <option value="<?php echo $TEMP['ID_TEMPORADA']; ?>" <?php echo $TEMP['ID_TEMPORADA'] == $temporadaFiltro ? 'selected' : ''; ?>>
                                                        <?php echo $TEMP['NOMBRE_TEMPORADA']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-8 col-md-6 col-12 d-flex align-items-end">
                                            <div class="alert alert-soft w-100 mb-0">
                                                Ingrese las proyecciones para la temporada seleccionada. Se guardarán solo para la empresa activa.
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-8 col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Registrar proyección semanal</h4>
                                </div>
                                <div class="box-body">
                                    <?php if ($mensajeExito) { ?>
                                        <div class="alert alert-soft"><?php echo $mensajeExito; ?></div>
                                    <?php } ?>
                                    <form method="post" class="row">
                                        <input type="hidden" name="TEMPORADA_FILTRO" value="<?php echo $temporadaFiltro; ?>">
                                        <div class="col-md-3 col-12">
                                            <label>Semana</label>
                                            <input type="number" name="SEMANA" class="form-control" min="1" max="53" required>
                                        </div>
                                        <div class="col-md-5 col-12">
                                            <label>Kg netos proyectados</label>
                                            <input type="number" step="0.01" name="KG_PROYECTADO" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 col-12 mt-10">
                                            <label>Estandar / Tipo embalaje</label>
                                            <select name="ESTANDAR" class="form-control" required>
                                                <option value="">Seleccione un estandar</option>
                                                <?php foreach ($ARRAYESTANDAR as $estandar) { ?>
                                                    <option value="<?php echo $estandar['ID_ESTANDAR']; ?>"><?php echo $estandar['NOMBRE_ESTANDAR']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-12 mt-15">
                                            <button type="submit" name="AGREGAR_PROYECCION" class="btn btn-primary">Agregar proyección</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="box metric-card bg-lightest">
                                <div class="box-body">
                                    <div class="d-flex justify-content-between align-items-center mb-10">
                                        <span class="section-title">Totales de la temporada</span>
                                        <span class="badge badge-primary"><?php echo count($proyeccionesFiltradas); ?> registros</span>
                                    </div>
                                    <div class="mb-10">
                                        <div class="text-muted">Kg proyectados</div>
                                        <h4 class="mb-0"><?php echo number_format($totalProyectado, 0, ',', '.'); ?> kg</h4>
                                    </div>
                                    <div class="d-flex mb-10">
                                        <div class="flex-grow-1">
                                            <div class="text-muted">Proyección en bulk</div>
                                            <div class="font-weight-600"><?php echo number_format($totalBulk, 0, ',', '.'); ?> kg</div>
                                            <span class="tag tag-bulk">Detectado por embalaje / estándar</span>
                                        </div>
                                    </div>
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <div class="text-muted">Proyección envasada</div>
                                            <div class="font-weight-600"><?php echo number_format($totalEnvasado, 0, ',', '.'); ?> kg</div>
                                            <span class="tag tag-envasado">Sin marca de bulk</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Detalle de proyecciones</h4>
                                    <p class="mb-0 text-muted">Desglose semanal para la temporada seleccionada.</p>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered projection-table">
                                        <thead>
                                            <tr>
                                                <th>Semana</th>
                                                <th>Estandar</th>
                                                <th class="text-center">Tipo</th>
                                                <th class="text-right">Kg proyectados</th>
                                                <th class="text-center">Registrado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($proyeccionesFiltradas) { foreach ($proyeccionesFiltradas as $proy) { ?>
                                                <tr>
                                                    <td><?php echo $proy['semana']; ?></td>
                                                    <td><?php echo htmlspecialchars($proy['descripcion_estandar']); ?></td>
                                                    <td class="text-center">
                                                        <?php if ($proy['es_bulk']) { ?>
                                                            <span class="tag tag-bulk">bulk</span>
                                                        <?php } else { ?>
                                                            <span class="tag tag-envasado">envasado</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-right"><?php echo number_format($proy['kg_proyectado'], 0, ',', '.'); ?></td>
                                                    <td class="text-center text-muted"><?php echo $proy['creado']; ?></td>
                                                </tr>
                                            <?php } } else { ?>
                                                <tr><td colspan="5" class="text-center text-muted">Todavía no hay datos guardados para esta temporada.</td></tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
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
