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
$DETALLEPRODUCTOR = array();
$EXISTENCIAPORVARIEDAD = array();
$TOPEXPORTADORES = array();
$ULTIMOSDOCUMENTOS = array();

$TOTALKILOSNETOS = 0;
$TOTALRECEPCIONES = 0;
$PROMEDIORECEPCION = 0;
$KILOSNETOPROCESO = 0;
$KILOSEXPORTADOS = 0;
$PORCENTAJEEXPORTACION = 0;
$EXISTENCIAMATERIAPRIMA = 0;
$EXISTENCIATIEMPORREAL = 0;
$EXISTENCIAREAL = 0;

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
    $DETALLEPRODUCTOR = $CONSULTA_ADO->kilosPorProductorAsociado($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);

    $RESUMENRECEPCION = $CONSULTA_ADO->resumenRecepcionesProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $TOTALKILOSNETOS = $RESUMENRECEPCION ? $RESUMENRECEPCION[0]["KILOS"] : 0;
    $TOTALRECEPCIONES = $RESUMENRECEPCION ? $RESUMENRECEPCION[0]["RECEPCIONES"] : 0;
    $PROMEDIORECEPCION = $TOTALRECEPCIONES ? ($TOTALKILOSNETOS / $TOTALRECEPCIONES) : 0;

    $KILOSNETOPROCESO = $CONSULTA_ADO->kilosProcesadosProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSEXPORTADOS = $CONSULTA_ADO->kilosExportadosProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $PORCENTAJEEXPORTACION = $CONSULTA_ADO->porcentajeExportacionProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $EXISTENCIAMATERIAPRIMA = $CONSULTA_ADO->existenciaMateriaPrimaProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);

    $EXISTENCIAPORVARIEDAD = $CONSULTA_ADO->existenciaMateriaPrimaPorVariedadProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $TOPEXPORTADORES = $CONSULTA_ADO->topExportacionProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS, 5);

    $EXISTENCIAREAL = $EXISTENCIAMATERIAPRIMA;
    $EXISTENCIATIEMPORREAL = max(($TOTALKILOSNETOS - $KILOSNETOPROCESO), 0);

    $ULTIMOSDOCUMENTOS = $productorController->ultimosDocumentosProductores($PRODUCTORESASOCIADOS, $ESPECIE, 6);
}

$maxExistenciaVariedad = 0;
if ($EXISTENCIAPORVARIEDAD) {
    $maxExistenciaVariedad = max(array_column($EXISTENCIAPORVARIEDAD, 'TOTAL'));
}

$maxExportadores = 0;
if ($TOPEXPORTADORES) {
    $maxExportadores = max(array_column($TOPEXPORTADORES, 'TOTAL'));
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
            .stat-card {
                color: #fff;
                border: 0;
                box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
                border-radius: 12px;
            }

            .stat-card .box-body {
                padding: 18px 20px;
            }

            .chart-container {
                min-height: 320px;
            }

            .helper-text {
                color: #6c757d;
                font-size: 0.9rem;
            }

            .subtle-badge {
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.8rem;
                background: rgba(255, 255, 255, 0.18);
                color: #fff;
                display: inline-block;
            }

            .list-indicator .media {
                padding: 12px 0;
                border-bottom: 1px dashed #e4e9f2;
            }

            .progress {
                height: 8px;
                border-radius: 10px;
            }

            .table-compact th,
            .table-compact td {
                padding: 10px 8px;
            }

            .muted-label {
                color: #98a6ad;
                font-size: 0.9rem;
            }
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
                        <div class="row mb-15">
                            <div class="col-12">
                                <p class="text-muted mb-10">Datos filtrados por empresa, temporada activa y productores asociados al usuario.</p>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-primary">
                                    <div class="box-body">
                                        <span class="subtle-badge">Materia prima</span>
                                        <h5 class="mt-15 mb-5">Kilos neto materia prima</h5>
                                        <h2 class="font-weight-600 mb-0"><?php echo number_format($TOTALKILOSNETOS, 0, ',', '.'); ?> kg</h2>
                                        <small class="helper-text">Total kilos netos recepcionados</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-info">
                                    <div class="box-body">
                                        <span class="subtle-badge">Proceso</span>
                                        <h5 class="mt-15 mb-5">Kilos neto entrada proceso</h5>
                                        <h2 class="font-weight-600 mb-0"><?php echo number_format($KILOSNETOPROCESO, 0, ',', '.'); ?> kg</h2>
                                        <small class="helper-text">Procesos cerrados a la fecha</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-success">
                                    <div class="box-body">
                                        <span class="subtle-badge">Existencia</span>
                                        <h5 class="mt-15 mb-5">Existencia neta real</h5>
                                        <h2 class="font-weight-600 mb-0"><?php echo number_format($EXISTENCIAREAL, 0, ',', '.'); ?> kg</h2>
                                        <small class="helper-text">Inventario disponible</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-warning">
                                    <div class="box-body">
                                        <span class="subtle-badge">Tiempo real</span>
                                        <h5 class="mt-15 mb-5">Existencia neta en tiempo real</h5>
                                        <h2 class="font-weight-600 mb-0"><?php echo number_format($EXISTENCIATIEMPORREAL, 0, ',', '.'); ?> kg</h2>
                                        <small class="helper-text">Recepcionado menos proceso</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-orange">
                                    <div class="box-body">
                                        <span class="subtle-badge">Exportación</span>
                                        <h5 class="mt-15 mb-5">Kilos exportados</h5>
                                        <h2 class="font-weight-600 mb-0"><?php echo number_format($KILOSEXPORTADOS, 0, ',', '.'); ?> kg</h2>
                                        <small class="helper-text">Avance: <?php echo number_format($PORCENTAJEEXPORTACION, 2, ',', '.'); ?>%</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-7 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="box-title mb-0">Indicadores operacionales</h4>
                                            <p class="muted-label mb-0">Recepciones y procesos basados en kilos netos</p>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="irPagina('listarProductorRecepcionmp.php');"><i class="fa fa-tag"></i> Recepciones</button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="irPagina('listarProductorProceso.php');"><i class="fa fa-tag"></i> Procesos</button>
                                        </div>
                                    </div>
                                    <div class="box-body list-indicator">
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <h5 class="mb-5">Recepciones cerradas</h5>
                                                <p class="mb-5 text-muted">Guías de productores asociados</p>
                                                <div class="progress mb-0">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                                                </div>
                                            </div>
                                            <div class="text-right" style="min-width: 140px;">
                                                <h4 class="mb-0"><?php echo number_format($TOTALRECEPCIONES, 0, ',', '.'); ?></h4>
                                                <small class="helper-text">Promedio: <?php echo number_format($PROMEDIORECEPCION, 0, ',', '.'); ?> kg</small>
                                            </div>
                                        </div>
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <h5 class="mb-5">Procesos cerrados</h5>
                                                <p class="mb-5 text-muted">Kilos netos ingresados a proceso</p>
                                                <div class="progress mb-0">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                                                </div>
                                            </div>
                                            <div class="text-right" style="min-width: 140px;">
                                                <h4 class="mb-0"><?php echo number_format($KILOSNETOPROCESO, 0, ',', '.'); ?> kg</h4>
                                                <small class="helper-text">Productores: <?php echo number_format(count($PRODUCTORESASOCIADOS), 0, ',', '.'); ?></small>
                                            </div>
                                        </div>
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <h5 class="mb-5">Exportación</h5>
                                                <p class="mb-5 text-muted">Kilos netos exportados de productores visibles</p>
                                                <div class="progress mb-0">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $PORCENTAJEEXPORTACION; ?>%"></div>
                                                </div>
                                            </div>
                                            <div class="text-right" style="min-width: 140px;">
                                                <h4 class="mb-0"><?php echo number_format($KILOSEXPORTADOS, 0, ',', '.'); ?> kg</h4>
                                                <small class="helper-text"><?php echo number_format($PORCENTAJEEXPORTACION, 2, ',', '.'); ?>% del total</small>
                                            </div>
                                        </div>
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <h5 class="mb-5">Existencia materia prima</h5>
                                                <p class="mb-5 text-muted">Inventario vigente en bodegas</p>
                                                <div class="progress mb-0">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 100%"></div>
                                                </div>
                                            </div>
                                            <div class="text-right" style="min-width: 140px;">
                                                <h4 class="mb-0"><?php echo number_format($EXISTENCIAMATERIAPRIMA, 0, ',', '.'); ?> kg</h4>
                                                <small class="helper-text">Tiempo real: <?php echo number_format($EXISTENCIATIEMPORREAL, 0, ',', '.'); ?> kg</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="box-title mb-0">Existencia de materia prima por variedad</h4>
                                            <p class="muted-label mb-0">Solo productores autorizados</p>
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="exportExistenciaExcel()"><i class="fa fa-tag"></i> Exportar Excel</button>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-compact mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Variedad</th>
                                                        <th class="text-right">Kilos</th>
                                                        <th style="width: 40%">Avance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($EXISTENCIAPORVARIEDAD) { ?>
                                                        <?php foreach ($EXISTENCIAPORVARIEDAD as $variedad) { 
                                                            $porcentajeVariedad = $maxExistenciaVariedad > 0 ? round(($variedad['TOTAL'] / $maxExistenciaVariedad) * 100, 2) : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $variedad['NOMBRE']; ?></td>
                                                                <td class="text-right"><?php echo number_format($variedad['TOTAL'], 0, ',', '.'); ?> kg</td>
                                                                <td>
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $porcentajeVariedad; ?>%"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">Sin existencias registradas.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Kilos netos por variedad</h4>
                                        <button class="btn btn-primary btn-sm" onclick="exportVariedadExcel()"><i class="fa fa-tag"></i> Exportar Excel</button>
                                    </div>
                                    <div class="box-body">
                                        <div id="graficoVariedad" class="chart-container"></div>
                                        <div id="graficoVariedadVacio" class="text-center text-muted <?php echo $KILOSVARIEDAD ? '' : 'd-none'; ?>">Sin información disponible</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Kilos netos por semana</h4>
                                        <button class="btn btn-primary btn-sm" onclick="exportSemanasExcel()"><i class="fa fa-tag"></i> Exportar Excel</button>
                                    </div>
                                    <div class="box-body">
                                        <div id="graficoSemanas" class="chart-container"></div>
                                        <div id="graficoSemanasVacio" class="text-center text-muted <?php echo $KILOSSEMANA ? '' : 'd-none'; ?>">Sin información disponible</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Detalle por productor</h4>
                                        <button class="btn btn-primary btn-sm" onclick="exportProductorExcel()"><i class="fa fa-tag"></i> Exportar Excel</button>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-compact">
                                                <thead>
                                                    <tr>
                                                        <th>Productor / CSP</th>
                                                        <th class="text-right">Recepciones</th>
                                                        <th class="text-right">Kilos netos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($DETALLEPRODUCTOR) { ?>
                                                        <?php foreach ($DETALLEPRODUCTOR as $detalleProductor) { ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo $detalleProductor["NOMBRE"]; ?><br>
                                                                    <small class="helper-text">CSP: <?php echo $detalleProductor["CSP"] ? $detalleProductor["CSP"] : 'Sin dato'; ?></small>
                                                                </td>
                                                                <td class="text-right"><?php echo number_format($detalleProductor["RECEPCIONES"], 0, ',', '.'); ?></td>
                                                                <td class="text-right"><?php echo number_format($detalleProductor["TOTAL"], 0, ',', '.'); ?> kg</td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">Sin registros para mostrar.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Top 5 exportación por productor</h4>
                                        <button class="btn btn-primary btn-sm" onclick="exportTopExportadorExcel()"><i class="fa fa-tag"></i> Exportar Excel</button>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-compact">
                                                <thead>
                                                    <tr>
                                                        <th>Productor / CSP</th>
                                                        <th class="text-right">Kilos</th>
                                                        <th style="width: 40%">Avance</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($TOPEXPORTADORES) { ?>
                                                        <?php foreach ($TOPEXPORTADORES as $exportador) { 
                                                            $porcentajeExportador = $maxExportadores > 0 ? round(($exportador['TOTAL'] / $maxExportadores) * 100, 2) : 0;
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <?php echo $exportador['NOMBRE']; ?><br>
                                                                    <small class="helper-text">CSP: <?php echo $exportador['CSP'] ? $exportador['CSP'] : 'Sin dato'; ?></small>
                                                                </td>
                                                                <td class="text-right"><?php echo number_format($exportador['TOTAL'], 0, ',', '.'); ?> kg</td>
                                                                <td>
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $porcentajeExportador; ?>%"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">Sin exportaciones registradas.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Últimos documentos subidos</h4>
                                        <span class="helper-text mb-0">Nombre registrado, vigencia y descarga directa</span>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-compact">
                                                <thead>
                                                    <tr>
                                                        <th>Documento</th>
                                                        <th>Vigencia</th>
                                                        <th>Descargar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($ULTIMOSDOCUMENTOS) { ?>
                                                        <?php foreach ($ULTIMOSDOCUMENTOS as $documento) { ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($documento->nombre_documento); ?></td>
                                                                <td><?php echo $documento->vigencia_documento; ?></td>
                                                                <td>
                                                                    <a href="../../data/data_productor/<?php echo $documento->archivo_documento; ?>" target="_blank" class="btn btn-info btn-sm">
                                                                        <i class="ti-download"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted">Aún no existen documentos registrados.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
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
        <script>
            const datosVariedad = <?php echo json_encode($KILOSVARIEDAD); ?>;
            const datosSemanas = <?php echo json_encode($KILOSSEMANA); ?>;
            const datosProductor = <?php echo json_encode($DETALLEPRODUCTOR); ?>;
            const datosExistencia = <?php echo json_encode($EXISTENCIAPORVARIEDAD); ?>;
            const datosTopExportadores = <?php echo json_encode($TOPEXPORTADORES); ?>;

            function exportToExcel(filename, headers, rows) {
                let csv = headers.join(';') + "\n";
                rows.forEach((fila) => {
                    csv += fila.join(';') + "\n";
                });
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(link.href);
            }

            function exportVariedadExcel() {
                if (!datosVariedad || !datosVariedad.length) {
                    return;
                }
                const filas = datosVariedad.map((item) => [item.NOMBRE, item.TOTAL]);
                exportToExcel('kilos_por_variedad.xls', ['Variedad', 'Kilos netos'], filas);
            }

            function exportSemanasExcel() {
                if (!datosSemanas || !datosSemanas.length) {
                    return;
                }
                const filas = datosSemanas.map((item) => [item.SEMANA, item.TOTAL]);
                exportToExcel('kilos_por_semana.xls', ['Semana', 'Kilos netos'], filas);
            }

            function exportProductorExcel() {
                if (!datosProductor || !datosProductor.length) {
                    return;
                }
                const filas = datosProductor.map((item) => [item.NOMBRE, item.CSP, item.RECEPCIONES, item.TOTAL]);
                exportToExcel('kilos_por_productor.xls', ['Productor', 'CSP', 'Recepciones', 'Kilos netos'], filas);
            }

            function exportExistenciaExcel() {
                if (!datosExistencia || !datosExistencia.length) {
                    return;
                }
                const filas = datosExistencia.map((item) => [item.NOMBRE, item.TOTAL]);
                exportToExcel('existencia_por_variedad.xls', ['Variedad', 'Kilos'], filas);
            }

            function exportTopExportadorExcel() {
                if (!datosTopExportadores || !datosTopExportadores.length) {
                    return;
                }
                const filas = datosTopExportadores.map((item) => [item.NOMBRE, item.CSP, item.TOTAL]);
                exportToExcel('top_exportacion_productor.xls', ['Productor', 'CSP', 'Kilos exportados'], filas);
            }

            if (datosVariedad && datosVariedad.length) {
                c3.generate({
                    bindto: '#graficoVariedad',
                    data: {
                        json: datosVariedad.map((item) => ({
                            NOMBRE: item.NOMBRE,
                            TOTAL: parseFloat(item.TOTAL)
                        })),
                        keys: {
                            x: 'NOMBRE',
                            value: ['TOTAL']
                        },
                        type: 'bar',
                        colors: {
                            TOTAL: '#00BCD4'
                        },
                        names: {
                            TOTAL: 'Kilos netos'
                        }
                    },
                    axis: {
                        x: {
                            type: 'category'
                        },
                        y: {
                            label: 'Kilos netos'
                        }
                    },
                    legend: {
                        show: false
                    },
                    padding: {
                        right: 20,
                        left: 40
                    }
                });
            }

            if (datosSemanas && datosSemanas.length) {
                c3.generate({
                    bindto: '#graficoSemanas',
                    data: {
                        json: datosSemanas.map((item) => ({
                            SEMANA: 'Semana ' + item.SEMANA,
                            TOTAL: parseFloat(item.TOTAL)
                        })),
                        keys: {
                            x: 'SEMANA',
                            value: ['TOTAL']
                        },
                        type: 'area-spline',
                        colors: {
                            TOTAL: '#8BC34A'
                        },
                        names: {
                            TOTAL: 'Kilos netos'
                        }
                    },
                    axis: {
                        x: {
                            type: 'category'
                        },
                        y: {
                            label: 'Kilos netos'
                        }
                    },
                    legend: {
                        show: false
                    },
                    padding: {
                        right: 20,
                        left: 40
                    }
                });
            }
        </script>
</body>

</html>
