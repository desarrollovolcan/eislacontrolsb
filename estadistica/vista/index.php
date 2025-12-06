<?php
require_once __DIR__ . "/../../assest/config/validarUsuarioOpera.php";

// LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
require_once __DIR__ . "/../../assest/controlador/RECEPCIONIND_ADO.php";
require_once __DIR__ . "/../../assest/controlador/DRECEPCIONIND_ADO.php";
require_once __DIR__ . "/../../assest/controlador/VESPECIES_ADO.php";
require_once __DIR__ . "/../../assest/controlador/PRODUCTOR_ADO.php";
require_once __DIR__ . "/../../assest/controlador/EMPRESAPRODUCTOR_ADO.php";
require_once __DIR__ . "/../../assest/controlador/productor_controller.php";

// INICIALIZAR CONTROLADORES
$RECEPCIONIND_ADO = new RECEPCIONIND_ADO();
$DRECEPCIONIND_ADO = new DRECEPCIONIND_ADO();
$VESPECIES_ADO = new VESPECIES_ADO();
$PRODUCTOR_ADO = new PRODUCTOR_ADO();
$EMPRESAPRODUCTOR_ADO = new EMPRESAPRODUCTOR_ADO();
$productorController = new ProductorController();

// INICIALIZAR ARREGLOS
$PRODUCTORESASOCIADOS = array();
$KILOSVARIEDAD = array();
$KILOSSEMANA = array();
$DETALLEPRODUCTOR = array();
$DETALLECSPVARIEDAD = array();
$DOCUMENTOSPORVENCER = array();

$TOTALNETO = 0;
$TOTALBRUTO = 0;
$TOTALENVASES = 0;
$TOTALKILOSAYER = 0;
$TOTALPRODUCTORECEPCIONES = 0;
$TOTALPRODUCTORKILOS = 0;
$TOTALPRODUCTORENVASES = 0;
$TOTALCSPVARIEDAD = 0;
$TOTALCSPVARIEDADENVASES = 0;
$TOTALDOCUMENTOS = 0;

$fechaAyer = (new DateTime('yesterday'))->format('Y-m-d');
$cacheProductores = [];
$cacheVariedades = [];

$ARRAYEMPRESAPRODUCTOR = $EMPRESAPRODUCTOR_ADO->buscarEmpresaProductorPorUsuarioCBX($IDUSUARIOS);
if ($ARRAYEMPRESAPRODUCTOR) {
    foreach ($ARRAYEMPRESAPRODUCTOR as $registroProductor) {
        $PRODUCTORESASOCIADOS[] = $registroProductor["ID_PRODUCTOR"];
    }
    $PRODUCTORESASOCIADOS = array_unique($PRODUCTORESASOCIADOS);
}

if ($PRODUCTORESASOCIADOS) {
    foreach ($ARRAYEMPRESAPRODUCTOR as $registroProductor) {
        $recepciones = $RECEPCIONIND_ADO->listarRecepcionEmpresaPlantaTemporadaCBXProductor(
            $registroProductor['ID_EMPRESA'],
            $registroProductor['ID_PRODUCTOR'],
            $TEMPORADAS,
            $ESPECIE
        );

        foreach ($recepciones as $recepcion) {
            $detalles = $DRECEPCIONIND_ADO->buscarPorRecepcion($recepcion['ID_RECEPCION']);

            foreach ($detalles as $detalle) {
                $vespecieId = $detalle['ID_VESPECIES'];
                if (!isset($cacheVariedades[$vespecieId])) {
                    $variedad = $VESPECIES_ADO->verVespecies($vespecieId);
                    $cacheVariedades[$vespecieId] = $variedad ? $variedad[0] : null;
                }
                $variedadData = $cacheVariedades[$vespecieId];
                if (!$variedadData || $variedadData['ID_ESPECIES'] != $ESPECIE) {
                    continue;
                }

                $productorId = $detalle['ID_PRODUCTOR'];
                if (!isset($cacheProductores[$productorId])) {
                    $prodData = $PRODUCTOR_ADO->verProductor($productorId);
                    $cacheProductores[$productorId] = $prodData ? $prodData[0] : null;
                }
                $productorData = $cacheProductores[$productorId];

                $neto = (float) $detalle['NETO'];
                $envase = (int) $detalle['ENVASE'];
                $bruto = isset($detalle['BRUTO']) ? (float) $detalle['BRUTO'] : 0;

                $TOTALNETO += $neto;
                $TOTALENVASES += $envase;
                $TOTALBRUTO += $bruto;

                if ($recepcion['FECHA'] === $fechaAyer) {
                    $TOTALKILOSAYER += $neto;
                }

                // Kilos por variedad
                $variedadNombre = $variedadData ? $variedadData['NOMBRE_VESPECIES'] : 'Sin datos';
                if (!isset($KILOSVARIEDAD[$vespecieId])) {
                    $KILOSVARIEDAD[$vespecieId] = [
                        'VARIEDAD' => $variedadNombre,
                        'TOTAL' => 0,
                        'ENVASES' => 0,
                    ];
                }
                $KILOSVARIEDAD[$vespecieId]['TOTAL'] += $neto;
                $KILOSVARIEDAD[$vespecieId]['ENVASES'] += $envase;

                // Kilos por semana
                $semana = $recepcion['SEMANA'];
                if (!isset($KILOSSEMANA[$semana])) {
                    $KILOSSEMANA[$semana] = [
                        'SEMANA' => $semana,
                        'TOTAL' => 0,
                    ];
                }
                $KILOSSEMANA[$semana]['TOTAL'] += $neto;

                // Kilos por productor
                $nombreProductor = $productorData ? $productorData['NOMBRE_PRODUCTOR'] : 'Sin datos';
                $csp = $productorData ? $productorData['CSG_PRODUCTOR'] : null;

                if (!isset($DETALLEPRODUCTOR[$productorId])) {
                    $DETALLEPRODUCTOR[$productorId] = [
                        'ID' => $productorId,
                        'NOMBRE' => $nombreProductor,
                        'CSP' => $csp,
                        'TOTAL' => 0,
                        'ENVASES' => 0,
                        'RECEPCIONES' => array(),
                    ];
                }
                $DETALLEPRODUCTOR[$productorId]['TOTAL'] += $neto;
                $DETALLEPRODUCTOR[$productorId]['ENVASES'] += $envase;
                $DETALLEPRODUCTOR[$productorId]['RECEPCIONES'][$recepcion['ID_RECEPCION']] = true;

                // Kilos por CSP y variedad
                $cspKey = $productorId . '-' . $vespecieId;
                if (!isset($DETALLECSPVARIEDAD[$cspKey])) {
                    $DETALLECSPVARIEDAD[$cspKey] = [
                        'PRODUCTOR' => $nombreProductor,
                        'CSP' => $csp,
                        'VARIEDAD' => $variedadNombre,
                        'TOTAL' => 0,
                        'ENVASES' => 0,
                    ];
                }
                $DETALLECSPVARIEDAD[$cspKey]['TOTAL'] += $neto;
                $DETALLECSPVARIEDAD[$cspKey]['ENVASES'] += $envase;
            }
        }
    }

    // Calcular totales derivados
    $DETALLEPRODUCTOR = array_values(array_map(function ($prod) {
        $prod['RECEPCIONES'] = count($prod['RECEPCIONES']);
        return $prod;
    }, $DETALLEPRODUCTOR));

    $TOTALPRODUCTORKILOS = array_sum(array_column($DETALLEPRODUCTOR, 'TOTAL'));
    $TOTALPRODUCTORECEPCIONES = array_sum(array_column($DETALLEPRODUCTOR, 'RECEPCIONES'));
    $TOTALPRODUCTORENVASES = array_sum(array_column($DETALLEPRODUCTOR, 'ENVASES'));

    $DETALLECSPVARIEDAD = array_values($DETALLECSPVARIEDAD);
    $TOTALCSPVARIEDAD = array_sum(array_column($DETALLECSPVARIEDAD, 'TOTAL'));
    $TOTALCSPVARIEDADENVASES = array_sum(array_column($DETALLECSPVARIEDAD, 'ENVASES'));

    usort($DETALLEPRODUCTOR, function ($a, $b) {
        return $b['TOTAL'] <=> $a['TOTAL'];
    });

    usort($DETALLECSPVARIEDAD, function ($a, $b) {
        return $b['TOTAL'] <=> $a['TOTAL'];
    });

    $KILOSVARIEDAD = array_values($KILOSVARIEDAD);
    usort($KILOSVARIEDAD, function ($a, $b) {
        return $b['TOTAL'] <=> $a['TOTAL'];
    });
    $KILOSVARIEDAD = array_slice($KILOSVARIEDAD, 0, 5);

    $KILOSSEMANA = array_values($KILOSSEMANA);
    usort($KILOSSEMANA, function ($a, $b) {
        return $a['SEMANA'] <=> $b['SEMANA'];
    });

    $DOCUMENTOSPORVENCER = $productorController->documentosPorVencerProductores($PRODUCTORESASOCIADOS, $ESPECIE, 5, 60);
    $TOTALDOCUMENTOS = $DOCUMENTOSPORVENCER ? count($DOCUMENTOSPORVENCER) : 0;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -->
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <link rel="stylesheet" href="../../api/cryptioadmin10/html/assets/vendor_components/c3/c3.min.css">
    <style>
        .dashboard-card {
            color: #fff;
            border: 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            height: 100%;
            min-height: 150px;
        }

        .collage-card {
            background-color: #fff;
        }

        .collage-card .box-body>*:last-child {
            margin-bottom: 0;
        }

        .collage-card,
        .compact-card {
            min-height: 380px;
        }

        @media (max-width: 991px) {
            .collage-card,
            .compact-card {
                min-height: auto;
            }
        }

        .dashboard-row {
            margin-bottom: 15px;
        }

        .dashboard-row>[class*='col-'] {
            margin-bottom: 15px;
        }

        .collage-row {
            margin-left: -8px;
            margin-right: -8px;
        }

        .collage-row>[class*='col-'] {
            padding-left: 8px;
            padding-right: 8px;
        }

        @media (min-width: 1200px) {
            .col-xl-4th {
                flex: 0 0 25%;
                max-width: 25%;
            }
        }

        .row .col-xl-4th {
            display: flex;
        }

        .row-stretch {
            align-items: stretch;
        }

        .row-stretch>[class*='col-'] {
            display: flex;
        }

        .row-stretch>[class*='col-']>.box {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .bg-gradient-sky {
            background: linear-gradient(135deg, #1d8cf8 0%, #5ac8fa 100%);
        }

        .bg-gradient-dusk {
            background: linear-gradient(135deg, #7b42f6 0%, #b06ab3 100%);
        }

        .bg-gradient-emerald {
            background: linear-gradient(135deg, #2ecc71 0%, #58d68d 100%);
        }

        .bg-gradient-amber {
            background: linear-gradient(135deg, #f5a623 0%, #f7c46c 100%);
        }

        .compact-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .compact-card .box-header {
            padding: 10px 12px;
            flex-shrink: 0;
        }

        .compact-card .box-body {
            padding: 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .compact-card .table-responsive {
            flex: 1;
        }

        .compact-table th,
        .compact-table td {
            padding: 8px 6px;
            font-size: 13px;
            vertical-align: middle;
        }

        .compact-table th {
            font-weight: 600;
        }

        .mini-progress {
            height: 6px;
        }

        .badge-slim {
            padding: 2px 6px;
            font-size: 11px;
        }

        section.content {
            padding-top: 10px;
        }

        .content-header {
            margin-bottom: 12px;
        }

        .chart-container {
            min-height: 320px;
        }

        tfoot tr td {
            font-weight: 600;
            background: #f8fafc;
        }

        .main-sidebar {
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-menu>li.active>a,
        .sidebar-menu>li.menu-open>a,
        .sidebar-menu>li:hover>a {
            background: transparent !important;
            color: #0d6efd !important;
        }

        .sidebar-menu>li>a {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .doc-chip {
            padding: 4px 8px;
            border-radius: 10px;
            font-weight: 600;
            background: #eef2ff;
            color: #4338ca;
        }

        .doc-chip.near {
            background: #fff7ed;
            color: #c2410c;
        }
    </style>
    <script type="text/javascript">
        function irPagina(url) {
            location.href = "" + url;
        }
    </script>
</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary">
    <div class="wrapper">
        <?php include_once "../../assest/config/menuOpera.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">Dashboard de productor</h3>
                            <div class="d-inline-block align-items-center">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                        <li class="breadcrumb-item" aria-current="page">Estadísticas</li>
                                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>
                <section class="content">
                    <p class="text-muted mb-15">Datos filtrados por productores asociados, temporada y especie seleccionada. Todas las métricas provienen del listado detallado de recepciones industriales.</p>

                    <div class="row dashboard-row row-stretch">
                        <div class="col-xl-4th col-lg-6 col-12">
                            <div class="box box-body dashboard-card bg-gradient-sky">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Kilos recepcionados acumulados</p>
                                        <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$TOTALNETO, 2, ',', '.'); ?> kg</h3>
                                    </div>
                                    <span class="icon-Add-cart fs-40 text-white"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4th col-lg-6 col-12">
                            <div class="box box-body dashboard-card bg-gradient-dusk">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Kilos recepcionados día anterior</p>
                                        <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$TOTALKILOSAYER, 2, ',', '.'); ?> kg</h3>
                                    </div>
                                    <span class="icon-Alarm-clock fs-40 text-white"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4th col-lg-6 col-12">
                            <div class="box box-body dashboard-card bg-gradient-emerald">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Total envases recepcionados</p>
                                        <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$TOTALENVASES, 0, ',', '.'); ?></h3>
                                    </div>
                                    <span class="icon-Incoming-mail fs-40 text-white"></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4th col-lg-6 col-12">
                            <div class="box box-body dashboard-card bg-gradient-amber">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Kilos brutos acumulados</p>
                                        <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$TOTALBRUTO, 2, ',', '.'); ?> kg</h3>
                                    </div>
                                    <span class="icon-Outcoming-mail fs-40 text-white"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row dashboard-row collage-row align-items-stretch row-stretch">
                        <div class="col-xl-4 col-12">
                            <div class="box compact-card collage-card h-100">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Indicadores operacionales</h4>
                                        <span class="badge badge-outline badge-info">Recepción</span>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-pill badge-info mr-2"><i class="icon-Notes"></i></span>
                                        <div>
                                            <div class="text-muted small">Recepciones registradas</div>
                                            <div class="h5 mb-0"><?php echo number_format((float)$TOTALPRODUCTORECEPCIONES, 0, ',', '.'); ?></div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="badge badge-pill badge-success mr-2"><i class="icon-Gear"></i></span>
                                        <div>
                                            <div class="text-muted small">Kilos netos por productor</div>
                                            <div class="h5 mb-0"><?php echo number_format((float)$TOTALPRODUCTORKILOS, 2, ',', '.'); ?> kg</div>
                                        </div>
                                    </div>
                                    <div class="bg-light p-2 rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Total envases</span>
                                            <span class="badge badge-primary"><?php echo number_format((float)$TOTALPRODUCTORENVASES, 0, ',', '.'); ?></span>
                                        </div>
                                    </div>
                                    <div class="bg-light p-2 rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">Documentos por vencer</span>
                                            <span class="badge badge-info"><?php echo number_format((float)$TOTALDOCUMENTOS, 0, ',', '.'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-12">
                            <div class="box compact-card collage-card h-100">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Kilos por productor (CSP)</h4>
                                        <span class="badge badge-outline badge-success">Materia prima</span>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <?php if ($DETALLEPRODUCTOR) { ?>
                                        <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                            <span>Total productores</span>
                                            <span class="badge badge-secondary"><?php echo number_format((float)$TOTALPRODUCTORKILOS, 2, ',', '.'); ?> kg</span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0 compact-table">
                                                <thead>
                                                    <tr>
                                                        <th>Productor</th>
                                                        <th class="text-right">Kg netos</th>
                                                        <th class="text-right">Envases</th>
                                                        <th class="text-right">Recepciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($DETALLEPRODUCTOR as $productor) {
                                                        $totalProd = round($productor['TOTAL'], 2);
                                                        $porcentaje = $TOTALPRODUCTORKILOS > 0 ? ($totalProd / $TOTALPRODUCTORKILOS) * 100 : 0;
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <div class="font-weight-600"><?php echo htmlspecialchars($productor['NOMBRE']); ?></div>
                                                                <div class="text-muted small">CSP: <?php echo $productor['CSP'] ? $productor['CSP'] : 'Sin dato'; ?></div>
                                                            </td>
                                                            <td class="text-right"><?php echo number_format($totalProd, 2, ',', '.'); ?> kg</td>
                                                            <td class="text-right"><?php echo number_format((float)$productor['ENVASES'], 0, ',', '.'); ?></td>
                                                            <td class="text-right"><?php echo number_format((float)$productor['RECEPCIONES'], 0, ',', '.'); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4">
                                                                <div class="progress progress-xxs mb-0">
                                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $porcentaje; ?>%" aria-valuenow="<?php echo $porcentaje; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td>Totales</td>
                                                        <td class="text-right"><?php echo number_format((float)$TOTALPRODUCTORKILOS, 2, ',', '.'); ?> kg</td>
                                                        <td class="text-right"><?php echo number_format((float)$TOTALPRODUCTORENVASES, 0, ',', '.'); ?></td>
                                                        <td class="text-right"><?php echo number_format((float)$TOTALPRODUCTORECEPCIONES, 0, ',', '.'); ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    <?php } else { ?>
                                        <div class="text-center text-muted">Sin información disponible.</div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-12">
                            <div class="box compact-card collage-card h-100">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Documentos próximos a vencer</h4>
                                        <span class="badge badge-outline badge-warning">Prioridad</span>
                                    </div>
                                </div>
                                <div class="box-body p-10">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0 compact-table">
                                            <thead>
                                                <tr>
                                                    <th>Nombre registrado</th>
                                                    <th class="text-center">Días</th>
                                                    <th class="text-center">Descargar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($DOCUMENTOSPORVENCER) { ?>
                                                    <?php $hoy = new DateTime(); ?>
                                                    <?php foreach ($DOCUMENTOSPORVENCER as $documento) {
                                                        $vigencia = new DateTime($documento->vigencia_documento);
                                                        $diasRestantes = (int) $hoy->diff($vigencia)->format('%r%a');
                                                        $chipClass = $diasRestantes <= 15 ? 'doc-chip near' : 'doc-chip';
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <div class="font-weight-600 mb-0"><?php echo htmlspecialchars($documento->nombre_documento); ?></div>
                                                                <div class="text-muted small">Vence: <?php echo $documento->vigencia_documento; ?></div>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="<?php echo $chipClass; ?>">
                                                                    <?php echo $diasRestantes >= 0 ? $diasRestantes . ' días' : 'Vencido'; ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="../../data/data_productor/<?php echo $documento->archivo_documento; ?>" target="_blank" class="btn btn-primary btn-xs px-2 py-1 d-inline-flex align-items-center">
                                                                    <i class="ti-download mr-1"></i><span>Descargar</span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center text-muted">Sin documentos pendientes.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row dashboard-row row-stretch">
                        <div class="col-lg-6 col-12 mb-15">
                            <div class="box compact-card h-100">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Recepciones por semana</h4>
                                        <span class="badge badge-outline badge-info">Neto recepcionado</span>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div id="chartSemanas" class="chart-container"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12 mb-15">
                            <div class="box compact-card h-100">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Top 5 variedades por kilo neto</h4>
                                        <span class="badge badge-outline badge-success">Materia prima</span>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div id="chartVariedades" class="chart-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row dashboard-row row-stretch">
                        <div class="col-lg-12 col-12 mb-15">
                            <div class="box compact-card h-100">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Kilos por CSP y variedad</h4>
                                        <span class="badge badge-outline badge-success">Materia prima</span>
                                    </div>
                                </div>
                                <div class="box-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-compact mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Productor</th>
                                                    <th>CSP</th>
                                                    <th>Variedad</th>
                                                    <th class="text-right">Kilos netos</th>
                                                    <th class="text-right">Envases</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($DETALLECSPVARIEDAD) { ?>
                                                    <?php foreach ($DETALLECSPVARIEDAD as $fila) { ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($fila['PRODUCTOR']); ?></td>
                                                            <td><?php echo $fila['CSP'] ? $fila['CSP'] : 'Sin dato'; ?></td>
                                                            <td><?php echo htmlspecialchars($fila['VARIEDAD']); ?></td>
                                                            <td class="text-right"><?php echo number_format((float)$fila['TOTAL'], 2, ',', '.'); ?> kg</td>
                                                            <td class="text-right"><?php echo number_format((float)$fila['ENVASES'], 0, ',', '.'); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">Sin registros por variedad.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                            <?php if ($DETALLECSPVARIEDAD) { ?>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3">Totales</td>
                                                        <td class="text-right"><?php echo number_format((float)$TOTALCSPVARIEDAD, 2, ',', '.'); ?> kg</td>
                                                        <td class="text-right"><?php echo number_format((float)$TOTALCSPVARIEDADENVASES, 0, ',', '.'); ?></td>
                                                    </tr>
                                                </tfoot>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php include_once "../../assest/config/footer.php"; ?>
        <?php include_once "../../assest/config/menuExtraOpera.php"; ?>
    </div>
    <?php include_once "../../assest/config/urlBase.php"; ?>
    <script src="../../api/cryptioadmin10/html/assets/vendor_components/d3/d3.min.js"></script>
    <script src="../../api/cryptioadmin10/html/assets/vendor_components/c3/c3.min.js"></script>
    <script>
        const datosSemanas = <?php echo json_encode($KILOSSEMANA); ?>;
        const datosVariedades = <?php echo json_encode($KILOSVARIEDAD); ?>;

        (function generarCharts() {
            const semanasColumns = [
                ['Kilos netos', ...datosSemanas.map((s) => parseFloat(s.TOTAL))]
            ];
            const semanasCategories = datosSemanas.map((s) => 'Semana ' + s.SEMANA);

            c3.generate({
                bindto: '#chartSemanas',
                data: {
                    columns: semanasColumns,
                    type: 'line',
                    colors: {
                        'Kilos netos': '#198754'
                    }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: semanasCategories
                    },
                    y: {
                        label: 'Kilos netos'
                    }
                }
            });

            const variedadColumns = [
                ['Kilos netos', ...datosVariedades.map((v) => parseFloat(v.TOTAL))]
            ];
            const variedadCategories = datosVariedades.map((v) => v.VARIEDAD);

            c3.generate({
                bindto: '#chartVariedades',
                data: {
                    columns: variedadColumns,
                    type: 'bar',
                    colors: {
                        'Kilos netos': '#0d6efd'
                    }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: variedadCategories
                    },
                    y: {
                        label: 'Kilos netos'
                    }
                }
            });
        })();
    </script>
</body>

</html>
