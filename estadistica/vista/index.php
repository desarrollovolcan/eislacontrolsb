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
$KILOSPRODUCTOR = array();
$RESUMENRECEPCION = array();
$ULTIMOSDOCUMENTOS = array();

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
    $KILOSPRODUCTOR = $CONSULTA_ADO->kilosPorProductorAsociado($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $RESUMENRECEPCION = $CONSULTA_ADO->resumenRecepcionesProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $ULTIMOSDOCUMENTOS = $productorController->ultimosDocumentosProductores($PRODUCTORESASOCIADOS, $ESPECIE, 6);
}

$TOTALKILOS = $RESUMENRECEPCION ? $RESUMENRECEPCION[0]["KILOS"] : 0;
$TOTALRECEPCIONES = $RESUMENRECEPCION ? $RESUMENRECEPCION[0]["RECEPCIONES"] : 0;
$TOTALPRODUCTORES = $RESUMENRECEPCION ? $RESUMENRECEPCION[0]["PRODUCTORES"] : 0;
$PROMEDIORECEPCION = $TOTALRECEPCIONES ? ($TOTALKILOS / $TOTALRECEPCIONES) : 0;
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
                                <div class="box stat-card bg-gradient-sky">
                                    <div class="box-body">
                                        <h5 class="text-uppercase">Kilos recepcionados</h5>
                                        <h2 class="font-weight-600"><?php echo number_format($TOTALKILOS, 0, ',', '.'); ?> kg</h2>
                                        <span class="helper-text">Temporada actual / especie seleccionada</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-dusk">
                                    <div class="box-body">
                                        <h5 class="text-uppercase">Recepciones registradas</h5>
                                        <h2 class="font-weight-600"><?php echo number_format($TOTALRECEPCIONES, 0, ',', '.'); ?></h2>
                                        <span class="helper-text">Guías cerradas del productor asociado</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-teal">
                                    <div class="box-body">
                                        <h5 class="text-uppercase">Promedio por recepción</h5>
                                        <h2 class="font-weight-600"><?php echo number_format($PROMEDIORECEPCION, 0, ',', '.'); ?> kg</h2>
                                        <span class="helper-text">Distribución de los kilos totales</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div class="box stat-card bg-gradient-amber">
                                    <div class="box-body">
                                        <h5 class="text-uppercase">Productores asociados</h5>
                                        <h2 class="font-weight-600"><?php echo number_format($TOTALPRODUCTORES ? $TOTALPRODUCTORES : count($PRODUCTORESASOCIADOS), 0, ',', '.'); ?></h2>
                                        <span class="helper-text">Accesos según usuario</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Kilos por variedad</h4>
                                        <button class="btn btn-primary btn-sm" onclick="exportVariedadExcel()"><i class="fa fa-tag"></i> Exportar Excel</button>
                                    </div>
                                    <div class="box-body">
                                        <div id="graficoVariedad" class="chart-container"></div>
                                        <div id="graficoVariedadVacio" class="text-center text-muted <?php echo $KILOSVARIEDAD ? 'd-none' : ''; ?>">Sin información disponible</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Kilos por semana</h4>
                                        <button class="btn btn-primary btn-sm" onclick="exportSemanasExcel()"><i class="fa fa-tag"></i> Exportar Excel</button>
                                    </div>
                                    <div class="box-body">
                                        <div id="graficoSemanas" class="chart-container"></div>
                                        <div id="graficoSemanasVacio" class="text-center text-muted <?php echo $KILOSSEMANA ? 'd-none' : ''; ?>">Sin información disponible</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-12">
                                <div class="box">
                                    <div class="box-header with-border d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Detalle por productor</h4>
                                        <span class="helper-text mb-0">Solo productores asociados al usuario</span>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Productor</th>
                                                        <th class="text-right">Recepciones</th>
                                                        <th class="text-right">Kilos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($KILOSPRODUCTOR) { ?>
                                                        <?php foreach ($KILOSPRODUCTOR as $detalleProductor) { ?>
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
                                        <span class="helper-text mb-0">Documentos asociados a los productores del usuario</span>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Archivo</th>
                                                        <th>Nombre</th>
                                                        <th>Vigencia</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($ULTIMOSDOCUMENTOS) { ?>
                                                        <?php foreach ($ULTIMOSDOCUMENTOS as $documento) { ?>
                                                            <tr>
                                                                <td>
                                                                    <a href="../../data/data_productor/<?php echo $documento->archivo_documento; ?>" target="_blank" class="btn btn-info btn-sm">
                                                                        <i class="ti-file"></i>
                                                                    </a>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($documento->nombre_documento); ?></td>
                                                                <td><?php echo $documento->vigencia_documento; ?></td>
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
                exportToExcel('kilos_por_variedad.xls', ['Variedad', 'Kilos'], filas);
            }

            function exportSemanasExcel() {
                if (!datosSemanas || !datosSemanas.length) {
                    return;
                }
                const filas = datosSemanas.map((item) => [item.SEMANA, item.TOTAL]);
                exportToExcel('kilos_por_semana.xls', ['Semana', 'Kilos'], filas);
            }

            if (datosVariedad && datosVariedad.length) {
                const chartVariedad = c3.generate({
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
                            TOTAL: 'Kilos'
                        }
                    },
                    axis: {
                        x: {
                            type: 'category'
                        },
                        y: {
                            label: 'Kilos'
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
                const chartSemanas = c3.generate({
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
                            TOTAL: 'Kilos'
                        }
                    },
                    axis: {
                        x: {
                            type: 'category'
                        },
                        y: {
                            label: 'Kilos'
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
