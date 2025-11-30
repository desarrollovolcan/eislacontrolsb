<?php
include_once "../../assest/config/validarUsuarioFruta.php";
include_once "../../assest/controlador/EXIMATERIAPRIMA_ADO.php";
include_once "../../assest/controlador/PLANTA_ADO.php";

$EXIMATERIAPRIMA_ADO = new EXIMATERIAPRIMA_ADO();
$PLANTA_ADO = new PLANTA_ADO();

$ARRAYTEMPORADA = $TEMPORADA_ADO->listarTemporadaCBX();

$empresaFiltro = $EMPRESAS;
$temporadaFiltro = isset($_REQUEST['TEMPORADA_FILTRO']) ? $_REQUEST['TEMPORADA_FILTRO'] : $TEMPORADAS;

$empresaSeleccionada = $EMPRESA_ADO->verEmpresa($empresaFiltro);
$nombreEmpresa = $empresaSeleccionada ? $empresaSeleccionada[0]['NOMBRE_EMPRESA'] : '';

if (!isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'])) {
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'] = [];
}

$existencias = $EXIMATERIAPRIMA_ADO->listarEximateriaprimaEmpresaTemporada($empresaFiltro, $temporadaFiltro);
$realesPorSemana = [];

foreach ($existencias as $existencia) {
    if (!isset($existencia['ESTADO_REGISTRO']) || $existencia['ESTADO_REGISTRO'] != 1) {
        continue;
    }

    $kgReal = isset($existencia['KILOS_NETO_EXIMATERIAPRIMA']) ? floatval($existencia['KILOS_NETO_EXIMATERIAPRIMA']) : 0;
    $fechaReferencia = !empty($existencia['FECHA_RECEPCION']) ? $existencia['FECHA_RECEPCION'] : $existencia['FECHA_COSECHA_EXIMATERIAPRIMA'];
    $semanaReal = $fechaReferencia ? intval(date('W', strtotime($fechaReferencia))) : null;
    if ($semanaReal) {
        if (!isset($realesPorSemana[$semanaReal])) {
            $realesPorSemana[$semanaReal] = 0;
        }
        $realesPorSemana[$semanaReal] += $kgReal;
    }
}

$proyeccionesFiltradas = array_values(array_filter(
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'],
    function ($proyeccion) use ($empresaFiltro, $temporadaFiltro) {
        $habilitado = !isset($proyeccion['habilitado']) || $proyeccion['habilitado'];
        return $habilitado && $proyeccion['empresa'] == $empresaFiltro && $proyeccion['temporada'] == $temporadaFiltro;
    }
));

$weeklyProjection = [];
$weeklyReal = $realesPorSemana;
$totalProyectado = 0;
$totalReal = array_sum($realesPorSemana);
$totalBulk = 0;
$totalEnvasado = 0;
$detalleProyecciones = [];
$proyeccionesTemporada = [];
$empresasReporte = [];
$proyeccionTotalEmpresa = [];
$kilosRealesPorEmpresaPlanta = [];
$kilosRealesTotales = [];
$empresasNombres = [];
$plantasNombres = [];

foreach ($proyeccionesFiltradas as $proyeccion) {
    $kgProyectado = $proyeccion['kg_proyectado'];
    $semana = $proyeccion['semana'];
    $esBulk = $proyeccion['es_bulk'];

    if (!isset($weeklyProjection[$semana])) {
        $weeklyProjection[$semana] = 0;
    }
    if (!isset($weeklyReal[$semana])) {
        $weeklyReal[$semana] = 0;
    }

    $weeklyProjection[$semana] += $kgProyectado;
    if ($esBulk) {
        $totalBulk += $kgProyectado;
    } else {
        $totalEnvasado += $kgProyectado;
    }

    $detalleProyecciones[] = [
        'semana' => $semana,
        'kg_proyectado' => $kgProyectado,
        'tipo_embalaje' => $proyeccion['tipo_embalaje'],
        'es_bulk' => $esBulk,
        'creado' => $proyeccion['creado']
    ];

    $totalProyectado += $kgProyectado;
}

$proyeccionesTemporada = array_values(array_filter(
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'],
    function ($proyeccion) use ($temporadaFiltro) {
        $habilitado = !isset($proyeccion['habilitado']) || $proyeccion['habilitado'];
        return $habilitado && $proyeccion['temporada'] == $temporadaFiltro;
    }
));

foreach ($proyeccionesTemporada as $proyeccion) {
    $empresaId = $proyeccion['empresa'];
    $empresasReporte[$empresaId] = true;
    if (!isset($proyeccionTotalEmpresa[$empresaId])) {
        $proyeccionTotalEmpresa[$empresaId] = 0;
    }
    $proyeccionTotalEmpresa[$empresaId] += $proyeccion['kg_proyectado'];
}

if (empty($empresasReporte)) {
    $empresasReporte[$empresaFiltro] = true;
}

foreach (array_keys($empresasReporte) as $empresaId) {
    $empresaInfo = $EMPRESA_ADO->verEmpresa($empresaId);
    $empresasNombres[$empresaId] = $empresaInfo ? $empresaInfo[0]['NOMBRE_EMPRESA'] : ('Empresa ' . $empresaId);

    $existenciasEmpresa = $EXIMATERIAPRIMA_ADO->listarEximateriaprimaEmpresaTemporada($empresaId, $temporadaFiltro);
    foreach ($existenciasEmpresa as $existencia) {
        if (!isset($existencia['ESTADO_REGISTRO']) || $existencia['ESTADO_REGISTRO'] != 1) {
            continue;
        }
        $kgReal = isset($existencia['KILOS_NETO_EXIMATERIAPRIMA']) ? floatval($existencia['KILOS_NETO_EXIMATERIAPRIMA']) : 0;
        $plantaId = isset($existencia['ID_PLANTA']) ? $existencia['ID_PLANTA'] : null;
        if (!$plantaId) {
            continue;
        }
        if (!isset($kilosRealesPorEmpresaPlanta[$empresaId][$plantaId])) {
            $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId] = 0;
        }
        if (!isset($kilosRealesTotales[$empresaId])) {
            $kilosRealesTotales[$empresaId] = 0;
        }

        $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId] += $kgReal;
        $kilosRealesTotales[$empresaId] += $kgReal;

        if (!isset($plantasNombres[$plantaId])) {
            $plantaInfo = $PLANTA_ADO->verPlanta($plantaId);
            $plantasNombres[$plantaId] = $plantaInfo ? $plantaInfo[0]['NOMBRE_PLANTA'] : ('Planta ' . $plantaId);
        }
    }
}

$plantasReporte = array_keys($plantasNombres);

ksort($weeklyProjection);
ksort($weeklyReal);
$labelsSemanas = array_keys($weeklyProjection ? $weeklyProjection : $weeklyReal);

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
                                <p class="mb-0">Proyección de kilos netos por empresa y semana.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Filtro de temporada</h4>
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
                                                Use <strong>Ingresar proyección</strong> para cargar semanas; este panel solo muestra el dashboard por temporada.
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-8 col-12">
                            <div class="box metric-card bg-lightest">
                                <div class="box-body">
                                    <h4 class="box-title mb-5">Panel solo lectura</h4>
                                    <p class="mb-0 text-muted">El dashboard refleja las proyecciones ingresadas por temporada. Para actualizar los datos use el menú <strong>Ingresar proyección</strong>.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="box metric-card bg-lightest">
                                <div class="box-body">
                                    <div class="d-flex justify-content-between align-items-center mb-10">
                                        <span class="section-title">Totales del filtro</span>
                                        <span class="badge badge-primary"><?php echo count($proyeccionesFiltradas); ?> registros</span>
                                    </div>
                                    <div class="mb-10">
                                        <div class="text-muted">Kg proyectados</div>
                                        <h4 class="mb-0"><?php echo number_format($totalProyectado, 0, ',', '.'); ?> kg</h4>
                                    </div>
                                    <div class="mb-10">
                                        <div class="text-muted">Kg reales informados</div>
                                        <h4 class="mb-0"><?php echo number_format($totalReal, 0, ',', '.'); ?> kg</h4>
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
                                                        $realEmpresa = isset($kilosRealesTotales[$empresaId]) ? $kilosRealesTotales[$empresaId] : 0;
                                                        $cumplimientoEmpresa = $proyectadoEmpresa > 0 ? ($realEmpresa / $proyectadoEmpresa) * 100 : 0;
                                                    ?>
                                                        <th colspan="2">
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
                                                        <th>Real</th>
                                                        <th>Proyectado</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($plantasReporte as $plantaId) { ?>
                                                    <tr>
                                                        <td class="text-left"><?php echo htmlspecialchars($plantasNombres[$plantaId]); ?></td>
                                                        <?php foreach (array_keys($empresasReporte) as $empresaId) { 
                                                            $realPlanta = isset($kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]) ? $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId] : 0;
                                                            $proyectadoEmpresa = isset($proyeccionTotalEmpresa[$empresaId]) ? $proyeccionTotalEmpresa[$empresaId] : 0;
                                                        ?>
                                                            <td class="text-right"><?php echo $realPlanta ? number_format($realPlanta, 0, ',', '.') : '-'; ?></td>
                                                            <td class="text-right text-muted"><?php echo $proyectadoEmpresa ? number_format($proyectadoEmpresa, 0, ',', '.') : '-'; ?></td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                                <tr class="font-weight-600">
                                                    <td class="text-left">Subtotal</td>
                                                    <?php foreach (array_keys($empresasReporte) as $empresaId) { 
                                                        $proyectadoEmpresa = isset($proyeccionTotalEmpresa[$empresaId]) ? $proyeccionTotalEmpresa[$empresaId] : 0;
                                                        $realEmpresa = isset($kilosRealesTotales[$empresaId]) ? $kilosRealesTotales[$empresaId] : 0;
                                                    ?>
                                                        <td class="text-right"><?php echo number_format($realEmpresa, 0, ',', '.'); ?></td>
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

                    <div class="row">
                        <div class="col-xl-7 col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Real vs Proyectado por semana</h4>
                                    <p class="mb-0 text-muted">Los kilos reales se calculan desde existencia de materia prima habilitada.</p>
                                </div>
                                <div class="box-body">
                                    <canvas id="chartSemanal" height="140"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Recepciones acumuladas vs proyección</h4>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table projection-table">
                                        <thead>
                                            <tr>
                                                <th>Semana</th>
                                                <th class="text-right">Real</th>
                                                <th class="text-right">Proyectado</th>
                                                <th class="text-center">Cumplimiento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($labelsSemanas) { foreach ($labelsSemanas as $semana) {
                                                $proy = isset($weeklyProjection[$semana]) ? $weeklyProjection[$semana] : 0;
                                                $real = isset($weeklyReal[$semana]) ? $weeklyReal[$semana] : 0;
                                                $cumplimiento = $proy > 0 ? ($real / $proy) * 100 : 0;
                                            ?>
                                                <tr>
                                                    <td><?php echo $semana; ?></td>
                                                    <td class="text-right"><?php echo number_format($real, 0, ',', '.'); ?></td>
                                                    <td class="text-right"><?php echo number_format($proy, 0, ',', '.'); ?></td>
                                                    <td class="text-center">
                                                        <span class="badge-soft" style="color: <?php echo $cumplimiento >= 100 ? '#2f855a' : '#c53030'; ?>;">
                                                            <?php echo round($cumplimiento, 1); ?>%
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php } } else { ?>
                                                <tr><td colspan="4" class="text-center text-muted">Sin proyecciones para el filtro seleccionado.</td></tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Detalle de proyecciones</h4>
                                    <p class="mb-0 text-muted">Desglose por semana y tipo de embalaje.</p>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered projection-table">
                                        <thead>
                                            <tr>
                                                <th>Semana</th>
                                                <th class="text-center">Tipo</th>
                                                <th class="text-right">Kg proyectados</th>
                                                <th class="text-center">Registrado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($detalleProyecciones) { foreach ($detalleProyecciones as $proy) { ?>
                                                <tr>
                                                    <td><?php echo $proy['semana']; ?></td>
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
                                                <tr><td colspan="4" class="text-center text-muted">Todavía no hay datos guardados para esta empresa y temporada.</td></tr>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const semanas = <?php echo json_encode(array_values($labelsSemanas)); ?>;
        const dataProyectado = <?php echo json_encode(array_values($weeklyProjection)); ?>;
        const dataReal = <?php echo json_encode(array_values($weeklyReal)); ?>;
        const ctx = document.getElementById('chartSemanal').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: semanas,
                datasets: [
                    {
                        label: 'Proyectado',
                        data: dataProyectado,
                        borderColor: '#1d8cf8',
                        backgroundColor: 'rgba(29,140,248,0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Real',
                        data: dataReal,
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46,204,113,0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: value => value.toLocaleString('es-CL') + ' kg' } },
                    x: { title: { display: true, text: 'Semana' } }
                },
                plugins: {
                    tooltip: { callbacks: { label: ctx => ctx.parsed.y.toLocaleString('es-CL') + ' kg' } }
                }
            }
        });
    </script>
</body>
</html>
