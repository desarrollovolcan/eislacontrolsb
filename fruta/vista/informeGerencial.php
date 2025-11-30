<?php
include_once "../../assest/config/validarUsuarioFruta.php";
include_once "../../assest/controlador/PRODUCTOR_ADO.php";
include_once "../../assest/controlador/EXIMATERIAPRIMA_ADO.php";
include_once "../../assest/controlador/ERECEPCION_ADO.php";

$PRODUCTOR_ADO = new PRODUCTOR_ADO();
$EXIMATERIAPRIMA_ADO = new EXIMATERIAPRIMA_ADO();
$ERECEPCION_ADO = new ERECEPCION_ADO();

$ARRAYEMPRESA = $EMPRESA_ADO->listarEmpresaCBX();
$ARRAYTEMPORADA = $TEMPORADA_ADO->listarTemporadaCBX();

$empresaFiltro = isset($_REQUEST['EMPRESA_FILTRO']) ? $_REQUEST['EMPRESA_FILTRO'] : $EMPRESAS;
$temporadaFiltro = isset($_REQUEST['TEMPORADA_FILTRO']) ? $_REQUEST['TEMPORADA_FILTRO'] : $TEMPORADAS;

$ARRAYPRODUCTORES = [];
if ($empresaFiltro) {
    $ARRAYPRODUCTORES = $PRODUCTOR_ADO->listarProductorPorEmpresaCBX($empresaFiltro);
}

$ARRAYESTANDAR = [];
if ($empresaFiltro) {
    $ARRAYESTANDAR = $ERECEPCION_ADO->listarEstandarPorEmpresaCBX($empresaFiltro);
}

if (!isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'])) {
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'] = [];
}

$nombreProductores = [];
foreach ($ARRAYPRODUCTORES as $productor) {
    $nombreProductores[$productor['ID_PRODUCTOR']] = $productor['NOMBRE_PRODUCTOR'];
}

$nombreEstandares = [];
foreach ($ARRAYESTANDAR as $estandar) {
    $nombreEstandares[$estandar['ID_ESTANDAR']] = $estandar['NOMBRE_ESTANDAR'];
}

$existencias = $EXIMATERIAPRIMA_ADO->listarEximateriaprimaEmpresaTemporada($empresaFiltro, $temporadaFiltro);
$realesPorProductor = [];
$realesPorSemana = [];
$semanasRealesPorProductor = [];

foreach ($existencias as $existencia) {
    if (!isset($existencia['ESTADO_REGISTRO']) || $existencia['ESTADO_REGISTRO'] != 1) {
        continue;
    }

    $productorReal = $existencia['ID_PRODUCTOR'];
    $kgReal = isset($existencia['KILOS_NETO_EXIMATERIAPRIMA']) ? floatval($existencia['KILOS_NETO_EXIMATERIAPRIMA']) : 0;
    $fechaReferencia = !empty($existencia['FECHA_RECEPCION']) ? $existencia['FECHA_RECEPCION'] : $existencia['FECHA_COSECHA_EXIMATERIAPRIMA'];
    $semanaReal = $fechaReferencia ? intval(date('W', strtotime($fechaReferencia))) : null;
    $estandarReal = isset($nombreEstandares[$existencia['ID_ESTANDAR']]) ? $nombreEstandares[$existencia['ID_ESTANDAR']] : '';
    $esBulkReal = stripos($estandarReal, 'bulk') !== false;

    if ($productorReal && $semanaReal) {
        if (!isset($realesPorProductor[$productorReal])) {
            $realesPorProductor[$productorReal] = [
                'total' => 0,
                'bulk' => 0,
                'envasado' => 0,
            ];
        }

        $realesPorProductor[$productorReal]['total'] += $kgReal;
        if ($esBulkReal) {
            $realesPorProductor[$productorReal]['bulk'] += $kgReal;
        } else {
            $realesPorProductor[$productorReal]['envasado'] += $kgReal;
        }

        if (!isset($realesPorSemana[$semanaReal])) {
            $realesPorSemana[$semanaReal] = 0;
        }
        $realesPorSemana[$semanaReal] += $kgReal;

        if (!isset($semanasRealesPorProductor[$productorReal])) {
            $semanasRealesPorProductor[$productorReal] = [];
        }
        if (!in_array($semanaReal, $semanasRealesPorProductor[$productorReal])) {
            $semanasRealesPorProductor[$productorReal][] = $semanaReal;
        }
    }
}

$mensajeExito = "";
if (isset($_POST['AGREGAR_PROYECCION'])) {
    $semana = isset($_POST['SEMANA']) ? intval($_POST['SEMANA']) : 0;
    $productor = isset($_POST['PRODUCTOR']) ? $_POST['PRODUCTOR'] : null;
    $kgProyectado = isset($_POST['KG_PROYECTADO']) ? floatval(str_replace([",", " "], [".", ""], $_POST['KG_PROYECTADO'])) : 0;
    $estandarSeleccionado = isset($_POST['ESTANDAR']) ? $_POST['ESTANDAR'] : '';
    $descripcionEstandar = $estandarSeleccionado && isset($nombreEstandares[$estandarSeleccionado]) ? $nombreEstandares[$estandarSeleccionado] : '';
    $tipoEmbalaje = $descripcionEstandar;

    if ($semana > 0 && $productor && $kgProyectado > 0) {
        $esBulk = stripos($tipoEmbalaje, 'bulk') !== false || stripos($descripcionEstandar, 'bulk') !== false;

        $_SESSION['INFORME_GERENCIAL_PROYECCIONES'][] = [
            'empresa' => $empresaFiltro,
            'temporada' => $temporadaFiltro,
            'productor' => $productor,
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

$resumenProductores = [];
$semanasPorProductor = [];
$weeklyProjection = [];
$weeklyReal = $realesPorSemana;
$totalProyectado = 0;
$totalReal = array_sum($realesPorSemana);
$totalBulk = 0;
$totalEnvasado = 0;

foreach ($proyeccionesFiltradas as $proyeccion) {
    $productorId = $proyeccion['productor'];
    $kgProyectado = $proyeccion['kg_proyectado'];
    $semana = $proyeccion['semana'];
    $esBulk = $proyeccion['es_bulk'];
    $kgReal = isset($realesPorProductor[$productorId]['total']) ? $realesPorProductor[$productorId]['total'] : 0;

    if (!isset($resumenProductores[$productorId])) {
        $resumenProductores[$productorId] = [
            'nombre' => isset($nombreProductores[$productorId]) ? $nombreProductores[$productorId] : 'Productor ' . $productorId,
            'proyectado' => 0,
            'real' => 0,
            'bulk' => 0,
            'envasado' => 0,
            'registros' => 0,
        ];
    }

    $resumenProductores[$productorId]['proyectado'] += $kgProyectado;
    $resumenProductores[$productorId]['real'] += $kgReal;
    $resumenProductores[$productorId]['registros'] += 1;

    if (!isset($semanasPorProductor[$productorId])) {
        $semanasPorProductor[$productorId] = [];
    }
    if (!in_array($semana, $semanasPorProductor[$productorId])) {
        $semanasPorProductor[$productorId][] = $semana;
    }

    if (!isset($weeklyProjection[$semana])) {
        $weeklyProjection[$semana] = 0;
    }
    if (!isset($weeklyReal[$semana])) {
        $weeklyReal[$semana] = 0;
    }

    $weeklyProjection[$semana] += $kgProyectado;
    if ($esBulk) {
        $resumenProductores[$productorId]['bulk'] += $kgProyectado;
        $totalBulk += $kgProyectado;
    } else {
        $resumenProductores[$productorId]['envasado'] += $kgProyectado;
        $totalEnvasado += $kgProyectado;
    }

    $totalProyectado += $kgProyectado;
}

foreach ($realesPorProductor as $productorId => $datosReales) {
    if (!isset($resumenProductores[$productorId])) {
        $resumenProductores[$productorId] = [
            'nombre' => isset($nombreProductores[$productorId]) ? $nombreProductores[$productorId] : 'Productor ' . $productorId,
            'proyectado' => 0,
            'real' => 0,
            'bulk' => 0,
            'envasado' => 0,
            'registros' => 0,
        ];
    }

    $resumenProductores[$productorId]['real'] = $datosReales['total'];
    if (isset($semanasRealesPorProductor[$productorId])) {
        if (!isset($semanasPorProductor[$productorId])) {
            $semanasPorProductor[$productorId] = [];
        }
        $semanasPorProductor[$productorId] = array_unique(array_merge($semanasPorProductor[$productorId], $semanasRealesPorProductor[$productorId]));
    }

    if ($datosReales['bulk'] > 0) {
        $resumenProductores[$productorId]['bulk'] = max($resumenProductores[$productorId]['bulk'], $datosReales['bulk']);
    }
    if ($datosReales['envasado'] > 0) {
        $resumenProductores[$productorId]['envasado'] = max($resumenProductores[$productorId]['envasado'], $datosReales['envasado']);
    }
}

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
                                <p class="mb-0">Proyección de kilos netos por productor y semana.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Filtros de empresa y temporada</h4>
                                </div>
                                <div class="box-body">
                                    <form method="post" class="row align-items-end">
                                        <div class="col-lg-4 col-md-6 col-12">
                                            <label>Empresa</label>
                                            <select name="EMPRESA_FILTRO" class="form-control" onchange="this.form.submit()">
                                                <?php foreach ($ARRAYEMPRESA as $EMP) { ?>
                                                    <option value="<?php echo $EMP['ID_EMPRESA']; ?>" <?php echo $EMP['ID_EMPRESA'] == $empresaFiltro ? 'selected' : ''; ?>>
                                                        <?php echo $EMP['NOMBRE_EMPRESA']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
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
                                        <input type="hidden" name="EMPRESA_FILTRO" value="<?php echo $empresaFiltro; ?>">
                                        <input type="hidden" name="TEMPORADA_FILTRO" value="<?php echo $temporadaFiltro; ?>">
                                        <div class="col-md-3 col-12">
                                            <label>Semana</label>
                                            <input type="number" name="SEMANA" class="form-control" min="1" max="53" required>
                                        </div>
                                        <div class="col-md-5 col-12">
                                            <label>Productor</label>
                                            <select name="PRODUCTOR" class="form-control" required>
                                                <option value="">Seleccione un productor</option>
                                                <?php foreach ($ARRAYPRODUCTORES as $productor) { ?>
                                                    <option value="<?php echo $productor['ID_PRODUCTOR']; ?>"><?php echo $productor['NOMBRE_PRODUCTOR']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 col-12">
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
                                                <th>Productor</th>
                                                <th class="text-right">Real</th>
                                                <th class="text-right">Proyectado</th>
                                                <th class="text-center">Cumplimiento</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($resumenProductores) { foreach ($resumenProductores as $prodId => $resumen) {
                                                $cumplimiento = $resumen['proyectado'] > 0 ? ($resumen['real'] / $resumen['proyectado']) * 100 : 0;
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($resumen['nombre']); ?></td>
                                                    <td class="text-right"><?php echo number_format($resumen['real'], 0, ',', '.'); ?></td>
                                                    <td class="text-right"><?php echo number_format($resumen['proyectado'], 0, ',', '.'); ?></td>
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
                                    <h4 class="box-title mb-0">Detalle por productor</h4>
                                    <p class="mb-0 text-muted">Incluye conteo de semanas y desglose de bulk vs envasado.</p>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table table-bordered projection-table">
                                        <thead>
                                            <tr>
                                                <th>Productor</th>
                                                <th class="text-center">Semanas</th>
                                                <th class="text-right">Kg proyectados</th>
                                                <th class="text-right">Bulk</th>
                                                <th class="text-right">Envasado</th>
                                                <th class="text-right">Kg reales</th>
                                                <th class="text-center">Registros</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($resumenProductores) { foreach ($resumenProductores as $prodId => $resumen) { ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($resumen['nombre']); ?></td>
                                                    <td class="text-center">
                                                        <?php echo isset($semanasPorProductor[$prodId]) ? implode(', ', $semanasPorProductor[$prodId]) : '-'; ?>
                                                    </td>
                                                    <td class="text-right"><?php echo number_format($resumen['proyectado'], 0, ',', '.'); ?></td>
                                                    <td class="text-right">
                                                        <?php echo number_format($resumen['bulk'], 0, ',', '.'); ?>
                                                        <span class="tag tag-bulk ml-5">bulk</span>
                                                    </td>
                                                    <td class="text-right">
                                                        <?php echo number_format($resumen['envasado'], 0, ',', '.'); ?>
                                                        <span class="tag tag-envasado ml-5">envasado</span>
                                                    </td>
                                                    <td class="text-right"><?php echo number_format($resumen['real'], 0, ',', '.'); ?></td>
                                                    <td class="text-center"><?php echo $resumen['registros']; ?></td>
                                                </tr>
                                            <?php } } else { ?>
                                                <tr><td colspan="7" class="text-center text-muted">Todavía no hay datos guardados para esta empresa y temporada.</td></tr>
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
