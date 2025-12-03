<?php
require_once __DIR__ . "/../../assest/config/validarUsuarioOpera.php";

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
require_once __DIR__ . "/../../assest/controlador/CONSULTA_ADO.php";
require_once __DIR__ . "/../../assest/controlador/EMPRESAPRODUCTOR_ADO.php";
require_once __DIR__ . "/../../assest/controlador/productor_controller.php";

//INICIALIZAR CONTROLADOR
$CONSULTA_ADO =  new CONSULTA_ADO();
$EMPRESAPRODUCTOR_ADO =  new EMPRESAPRODUCTOR_ADO();
$productorController = new ProductorController();

//INICIALIZAR ARREGLOS
$PRODUCTORESASOCIADOS = array();
$KILOSVARIEDAD = array();
$KILOSSEMANA = array();
$KILOSPROCESOSEMANAS = array();
$DETALLEPRODUCTOR = array();
$DETALLECSPVARIEDAD = array();
$DOCUMENTOSPORVENCER = array();
$TOTALPRODUCTORKILOS = 0;
$TOTALPRODUCTORECEPCIONES = 0;
$TOTALPRODUCTORENVASES = 0;
$TOTALCSPVARIEDAD = 0;
$TOTALCSPVARIEDADENVASES = 0;
$TOTALDOCUMENTOS = 0;

$KILOSRECEPCIONACUMULADOS = 0;
$KILOSRECEPCIONHOY = 0;
$KILOSPROCESOACUMULADOS = 0;
$KILOSPROCESOHOY = 0;
$RELACIONPROCESO = 0;
$RELACIONPROCESOBARRA = 0;

$ARRAYEMPRESAPRODUCTOR = $EMPRESAPRODUCTOR_ADO->buscarEmpresaProductorPorUsuarioCBX($IDUSUARIOS);
if ($ARRAYEMPRESAPRODUCTOR) {
    foreach ($ARRAYEMPRESAPRODUCTOR as $registroProductor) {
        $PRODUCTORESASOCIADOS[] = $registroProductor["ID_PRODUCTOR"];
    }
    $PRODUCTORESASOCIADOS = array_unique($PRODUCTORESASOCIADOS);
}

if ($PRODUCTORESASOCIADOS) {
    $KILOSVARIEDAD = $CONSULTA_ADO->kilosPorVariedadProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSSEMANA = $CONSULTA_ADO->kilosPorSemanaProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSPROCESOSEMANAS = $CONSULTA_ADO->kilosProcesadosPorSemanaProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $DETALLEPRODUCTOR = $CONSULTA_ADO->kilosPorProductorAsociado($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $DETALLECSPVARIEDAD = $CONSULTA_ADO->kilosPorCspYVariedadProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);

    $KILOSRECEPCIONACUMULADOS = $CONSULTA_ADO->kilosMateriaPrimaProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSRECEPCIONHOY = $CONSULTA_ADO->kilosRecepcionadosHoyProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSPROCESOACUMULADOS = $CONSULTA_ADO->kilosProcesadosProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSPROCESOHOY = $CONSULTA_ADO->kilosProcesadosHoyProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);

    $RELACIONPROCESO = $KILOSRECEPCIONACUMULADOS > 0
        ? ($KILOSPROCESOACUMULADOS / $KILOSRECEPCIONACUMULADOS) * 100
        : 0;
    $RELACIONPROCESOBARRA = $RELACIONPROCESO > 100 ? 100 : $RELACIONPROCESO;

    $DOCUMENTOSPORVENCER = $productorController->documentosPorVencerProductores($PRODUCTORESASOCIADOS, $ESPECIE, 5, 60);

    if ($DETALLEPRODUCTOR) {
        $TOTALPRODUCTORKILOS = array_sum(array_column($DETALLEPRODUCTOR, 'TOTAL'));
        $TOTALPRODUCTORECEPCIONES = array_sum(array_column($DETALLEPRODUCTOR, 'RECEPCIONES'));
        $TOTALPRODUCTORENVASES = array_sum(array_column($DETALLEPRODUCTOR, 'ENVASES'));
    }
    if ($DETALLECSPVARIEDAD) {
        $TOTALCSPVARIEDAD = array_sum(array_column($DETALLECSPVARIEDAD, 'TOTAL'));
        $TOTALCSPVARIEDADENVASES = array_sum(array_column($DETALLECSPVARIEDAD, 'ENVASES'));
    }
    $TOTALDOCUMENTOS = $DOCUMENTOSPORVENCER ? count($DOCUMENTOSPORVENCER) : 0;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>INICIO</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <!- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA ->
        <?php include_once "../../assest/config/urlHead.php"; ?>
        <link rel="stylesheet" href="../../api/cryptioadmin10/html/assets/vendor_components/c3/c3.min.css">
        <style>
            .dashboard-card {
                color: #fff;
                border: 0;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
                height: 100%;
            }

            .collage-card { background-color: #fff; }
            .collage-card .box-body > *:last-child { margin-bottom: 0; }

            .dashboard-row { margin-bottom: 15px; }
            .dashboard-row > [class*='col-'] { margin-bottom: 15px; }

            .collage-row { margin-left: -8px; margin-right: -8px; }
            .collage-row > [class*='col-'] { padding-left: 8px; padding-right: 8px; }

            @media (min-width: 1200px) {
                .col-xl-4th { flex: 0 0 25%; max-width: 25%; }
            }

            .row .col-xl-4th { display: flex; }

            .row-stretch { align-items: stretch; }
            .row-stretch > [class*='col-'] { display: flex; }
            .row-stretch > [class*='col-'] > .box { flex: 1; display: flex; flex-direction: column; }

            .bg-gradient-sky { background: linear-gradient(135deg, #1d8cf8 0%, #5ac8fa 100%); }
            .bg-gradient-dusk { background: linear-gradient(135deg, #7b42f6 0%, #b06ab3 100%); }
            .bg-gradient-emerald { background: linear-gradient(135deg, #2ecc71 0%, #58d68d 100%); }
            .bg-gradient-amber { background: linear-gradient(135deg, #f5a623 0%, #f7c46c 100%); }

            .compact-card { display: flex; flex-direction: column; height: 100%; }
            .compact-card .box-header { padding: 10px 12px; flex-shrink: 0; }
            .compact-card .box-body {
                padding: 12px;
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .compact-card .table-responsive { flex: 1; }

            .compact-table th, .compact-table td { padding: 8px 6px; font-size: 13px; vertical-align: middle; }
            .compact-table th { font-weight: 600; }

            .mini-progress { height: 6px; }

            .badge-slim { padding: 2px 6px; font-size: 11px; }

            section.content { padding-top: 10px; }

            .content-header { margin-bottom: 12px; }

            .chart-container { min-height: 320px; }

            tfoot tr td { font-weight: 600; background: #f8fafc; }

            .main-sidebar { height: 100vh; overflow-y: auto; }

            .sidebar-menu > li.active > a,
            .sidebar-menu > li.menu-open > a,
            .sidebar-menu > li:hover > a { background: transparent !important; color: #0d6efd !important; }

            .sidebar-menu > li > a { padding-top: 10px; padding-bottom: 10px; }

            .doc-chip { padding: 4px 8px; border-radius: 10px; font-weight: 600; background: #eef2ff; color: #4338ca; }
            .doc-chip.near { background: #fff7ed; color: #c2410c; }
        </style>
        <!- FUNCIONES BASES ->
        <script type="text/javascript">
            function irPagina(url) {
                location.href = "" + url;
            }
        </script>
</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary" >
    <div class="wrapper">
        <!- LLAMADA AL MENU PRINCIPAL DE LA PAGINA->
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
                        <p class="text-muted mb-15">Datos filtrados por productores asociados, temporada y especie seleccionada. Los acumulados consideran información hasta el cierre del día anterior.</p>

                        <div class="row dashboard-row row-stretch">
                            <div class="col-xl-4th col-lg-6 col-12">
                                <div class="box box-body dashboard-card bg-gradient-sky">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0 text-white-50">Kilos recepcionados acumulados</p>
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$KILOSRECEPCIONACUMULADOS, 2, ',', '.'); ?> kg</h3>
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
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$KILOSRECEPCIONHOY, 2, ',', '.'); ?> kg</h3>
                                        </div>
                                        <span class="icon-Alarm-clock fs-40 text-white"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4th col-lg-6 col-12">
                                <div class="box box-body dashboard-card bg-gradient-emerald">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0 text-white-50">Procesos acumulados (neto entrada)</p>
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$KILOSPROCESOACUMULADOS, 2, ',', '.'); ?> kg</h3>
                                        </div>
                                        <span class="icon-Incoming-mail fs-40 text-white"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4th col-lg-6 col-12">
                                <div class="box box-body dashboard-card bg-gradient-amber">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0 text-white-50">Procesos día anterior</p>
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$KILOSPROCESOHOY, 2, ',', '.'); ?> kg</h3>
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
                                            <span class="badge badge-outline badge-info">Corte día previo</span>
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
                                                <span class="text-muted small">Kilos recepcionados (día previo)</span>
                                                <span class="badge badge-primary"><?php echo number_format((float)$KILOSRECEPCIONHOY, 2, ',', '.'); ?> kg</span>
                                            </div>
                                        </div>
                                        <div class="bg-light p-2 rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Procesos cerrados (día previo)</span>
                                                <span class="badge badge-info"><?php echo number_format((float)$KILOSPROCESOHOY, 2, ',', '.'); ?> kg</span>
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
                                                            <td colspan="3" class="text-center text-muted">Aún no existen documentos próximos a vencer.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="3" class="text-right">Total documentos: <?php echo number_format((float)$TOTALDOCUMENTOS, 0, ',', '.'); ?></td>
                                                    </tr>
                                                </tfoot>
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
                                            <h4 class="box-title mb-0">Procesos por semana</h4>
                                            <span class="badge badge-outline badge-success">Neto de proceso</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div id="chartProcesoSemanal" class="chart-container"></div>
                                    </div>
                                </div>
                            </div>
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
                        </div>

                        <div class="row dashboard-row row-stretch">
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box compact-card h-100">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Indicadores operacionales</h4>
                                            <span class="badge badge-outline badge-info">Corte día previo</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <div class="text-muted small">Recepciones registradas</div>
                                                <div class="h4 mb-0 font-weight-700"><?php echo number_format((float)$TOTALPRODUCTORECEPCIONES, 0, ',', '.'); ?></div>
                                            </div>
                                            <span class="badge badge-primary badge-slim">Agrupado</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <div class="text-muted small">Kilos netos por productor</div>
                                                <div class="h4 mb-0 font-weight-700"><?php echo number_format((float)$TOTALPRODUCTORKILOS, 2, ',', '.'); ?> kg</div>
                                            </div>
                                            <span class="badge badge-success badge-slim">Materia prima</span>
                                        </div>
                                        <div class="bg-light p-2 rounded mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Kilos recepcionados (día previo)</span>
                                                <span class="badge badge-secondary"><?php echo number_format((float)$KILOSRECEPCIONHOY, 2, ',', '.'); ?> kg</span>
                                            </div>
                                        </div>
                                        <div class="bg-light p-2 rounded mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Procesos cerrados (día previo)</span>
                                                <span class="badge badge-info"><?php echo number_format((float)$KILOSPROCESOHOY, 2, ',', '.'); ?> kg</span>
                                            </div>
                                        </div>
                                        <p class="text-muted small mb-0">Totales alineados a <strong>Agrupado de proceso</strong> con corte al día previo.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-12 mb-15">
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
                                                            <td colspan="4" class="text-center text-muted">Sin registros por variedad.</td>
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
            <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO ->
            <?php include_once "../../assest/config/footer.php"; ?>
            <?php include_once "../../assest/config/menuExtraOpera.php"; ?>
    </div>
    <!- LLAMADA URL DE ARCHIVOS DE DISEÑO Y JQUERY E OTROS ->
        <?php include_once "../../assest/config/urlBase.php"; ?>
        <script src="../../api/cryptioadmin10/html/assets/vendor_components/d3/d3.min.js"></script>
        <script src="../../api/cryptioadmin10/html/assets/vendor_components/c3/c3.min.js"></script>
        <script>
            const datosSemanas = <?php echo json_encode($KILOSSEMANA); ?>;
            const datosProcesos = <?php echo json_encode($KILOSPROCESOSEMANAS); ?>;

            (function generarCharts() {
                const procesosColumns = [
                    ['Kilos procesados', ...datosProcesos.map((p) => p.TOTAL)]
                ];
                const procesosCategories = datosProcesos.map((p) => 'Semana ' + p.SEMANA);

                c3.generate({
                    bindto: '#chartProcesoSemanal',
                    data: {
                        columns: procesosColumns,
                        type: 'area-spline',
                        colors: {
                            'Kilos procesados': '#0d6efd'
                        }
                    },
                    axis: {
                        x: {
                            type: 'category',
                            categories: procesosCategories
                        },
                        y: {
                            label: 'Kilos netos'
                        }
                    },
                    point: {
                        show: true
                    }
                });

                const semanasColumns = [
                    ['Kilos netos', ...datosSemanas.map((s) => s.TOTAL)]
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
            })();
        </script>
</body>

</html>
