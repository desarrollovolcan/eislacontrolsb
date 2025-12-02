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
$ULTIMOSDOCUMENTOS = array();

$TOTALKILOSNETOS = 0;
$TOTALRECEPCIONES = 0;
$PROMEDIORECEPCION = 0;
$KILOSNETOPROCESO = 0;
$KILOSEXPORTADOS = 0;
$PORCENTAJEEXPORTACION = 0;
$EXISTENCIAMATERIAPRIMA = 0;

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

    $ULTIMOSDOCUMENTOS = $productorController->ultimosDocumentosProductores($PRODUCTORESASOCIADOS, $ESPECIE, 6);
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
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            }
            .stat-card .box-body {
                padding: 16px;
            }
            .chart-container {
                min-height: 320px;
            }
            .helper-text {
                color: #6c757d;
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
                                <h3 class="page-title">Panel Estadístico</h3>
                                <div class="d-inline-block align-items-center">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                            <li class="breadcrumb-item" aria-current="page">Estadísticas</li>
                                            <li class="breadcrumb-item active" aria-current="page">Dashboard productor</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                        </div>
                    </div>
                    <section class="content">
                        <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-primary">
                                    <div class="box-body">
                                        <h5 class="text-uppercase mb-10">Kg acumulados materia prima</h5>
                                        <h2 class="font-weight-600 mb-5"><?php echo number_format($TOTALKILOSNETOS, 0, ',', '.'); ?> kg</h2>
                                        <span class="helper-text">Total kilos netos de recepciones</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-info">
                                    <div class="box-body">
                                        <h5 class="text-uppercase mb-10">Kilos neto entrada</h5>
                                        <h2 class="font-weight-600 mb-5"><?php echo number_format($KILOSNETOPROCESO, 0, ',', '.'); ?> kg</h2>
                                        <span class="helper-text">Kilos procesados a la fecha</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-success">
                                    <div class="box-body">
                                        <h5 class="text-uppercase mb-10">Kilos exportados</h5>
                                        <h2 class="font-weight-600 mb-5"><?php echo number_format($KILOSEXPORTADOS, 0, ',', '.'); ?> kg</h2>
                                        <span class="helper-text">% Exportación: <?php echo number_format($PORCENTAJEEXPORTACION, 2, ',', '.'); ?>%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-secondary">
                                    <div class="box-body">
                                        <h5 class="text-uppercase mb-10">Existencia materia prima</h5>
                                        <h2 class="font-weight-600 mb-5"><?php echo number_format($EXISTENCIAMATERIAPRIMA, 0, ',', '.'); ?> kg</h2>
                                        <span class="helper-text">Stock disponible según existencias</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-sky">
                                    <div class="box-body">
                                        <h5 class="text-uppercase mb-10">Recepciones registradas</h5>
                                        <h2 class="font-weight-600 mb-5"><?php echo number_format($TOTALRECEPCIONES, 0, ',', '.'); ?></h2>
                                        <span class="helper-text">Guías cerradas de productores asociados</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-amber">
                                    <div class="box-body">
                                        <h5 class="text-uppercase mb-10">Promedio por recepción</h5>
                                        <h2 class="font-weight-600 mb-5"><?php echo number_format($PROMEDIORECEPCION, 0, ',', '.'); ?> kg</h2>
                                        <span class="helper-text">Basado en kilos netos recepcionados</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-dusk">
                                    <div class="box-body">
                                        <h5 class="text-uppercase mb-10">Productores visibles</h5>
                                        <h2 class="font-weight-600 mb-5"><?php echo number_format(count($PRODUCTORESASOCIADOS), 0, ',', '.'); ?></h2>
                                        <span class="helper-text">Según permisos del usuario</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-teal">
                                    <div class="box-body">
                                        <h5 class="text-uppercase mb-10">Detalle descargable</h5>
                                        <button class="btn btn-light" onclick="irPagina('listarProductorRecepcionmp.php');"><i class="fa fa-tag"></i> Recepciones</button>
                                        <button class="btn btn-light mt-5" onclick="irPagina('listarProductorProceso.php');"><i class="fa fa-tag"></i> Procesos</button>
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
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Productor</th>
                                                        <th class="text-right">Recepciones</th>
                                                        <th class="text-right">Kilos netos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($DETALLEPRODUCTOR) { ?>
                                                        <?php foreach ($DETALLEPRODUCTOR as $detalleProductor) { ?>
                                                            <tr>
                                                                <td><?php echo $detalleProductor["NOMBRE"]; ?></td>
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
                                        <h4 class="box-title mb-0">Últimos documentos subidos</h4>
                                        <span class="helper-text mb-0">Nombre registrado y fecha de vencimiento</span>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
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
                const filas = datosProductor.map((item) => [item.NOMBRE, item.RECEPCIONES, item.TOTAL]);
                exportToExcel('kilos_por_productor.xls', ['Productor', 'Recepciones', 'Kilos netos'], filas);
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
