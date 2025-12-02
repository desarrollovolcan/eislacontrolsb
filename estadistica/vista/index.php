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
$DETALLECSPVARIEDAD = array();
$DOCUMENTOSPORVENCER = array();
$TOTALPRODUCTORKILOS = 0;
$TOTALPRODUCTORECEPCIONES = 0;
$TOTALCSPVARIEDAD = 0;
$TOTALDOCUMENTOS = 0;

$KILOSRECEPCIONACUMULADOS = 0;
$KILOSRECEPCIONHOY = 0;
$KILOSPROCESOACUMULADOS = 0;
$KILOSPROCESOHOY = 0;

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
    $DETALLECSPVARIEDAD = $CONSULTA_ADO->kilosPorCspYVariedadProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);

    $KILOSRECEPCIONACUMULADOS = $CONSULTA_ADO->kilosMateriaPrimaProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSRECEPCIONHOY = $CONSULTA_ADO->kilosRecepcionadosHoyProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSPROCESOACUMULADOS = $CONSULTA_ADO->kilosProcesadosProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);
    $KILOSPROCESOHOY = $CONSULTA_ADO->kilosProcesadosHoyProductor($TEMPORADAS, $ESPECIE, $PRODUCTORESASOCIADOS);

    $DOCUMENTOSPORVENCER = $productorController->documentosPorVencerProductores($PRODUCTORESASOCIADOS, $ESPECIE, 8, 60);

    if ($DETALLEPRODUCTOR) {
        $TOTALPRODUCTORKILOS = array_sum(array_column($DETALLEPRODUCTOR, 'TOTAL'));
        $TOTALPRODUCTORECEPCIONES = array_sum(array_column($DETALLEPRODUCTOR, 'RECEPCIONES'));
    }
    if ($DETALLECSPVARIEDAD) {
        $TOTALCSPVARIEDAD = array_sum(array_column($DETALLECSPVARIEDAD, 'TOTAL'));
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
            .kpi-card {
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: #fff;
                padding: 16px 18px;
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 6px;
                box-shadow: 0 6px 20px rgba(17, 24, 39, 0.06);
            }

            .kpi-title {
                font-size: 0.95rem;
                color: #6c757d;
                letter-spacing: 0.04em;
                margin: 0;
            }

            .kpi-value {
                font-size: 1.8rem;
                font-weight: 600;
                color: #1f2937;
                margin: 0;
            }

            .kpi-foot {
                color: #6b7280;
                margin: 0;
            }

            .chart-container {
                min-height: 320px;
            }

            .table-compact th,
            .table-compact td {
                padding: 10px 8px;
            }

            .section-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 12px;
            }

            .section-header .helper-text {
                margin: 0;
                color: #6c757d;
            }

            .box.box-clean {
                border: 1px solid #e5e7eb;
                box-shadow: none;
            }

            .section-shell {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 18px;
                height: 100%;
                box-shadow: 0 6px 20px rgba(17, 24, 39, 0.06);
            }

            tfoot tr td {
                font-weight: 600;
                background: #f8fafc;
            }
        </style>
        <!- FUNCIONES BASES ->
        <script type="text/javascript">
            function irPagina(url) {
                location.href = "" + url;
            }
        </script>
</head>

<body class="hold-transition light-skin fixed sidebar-mini" >
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
                        <div class="row mb-20 align-items-stretch">
                            <div class="col-12">
                                <p class="text-muted mb-5">Información basada en productores asociados, temporada y especie seleccionada. Los acumulados y gráficos consideran datos hasta el día previo; las cifras diarias corresponden al último día cerrado.</p>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15 d-flex">
                                <div class="kpi-card w-100">
                                    <p class="kpi-title">Kilos recepcionados acumulados</p>
                                    <p class="kpi-value"><?php echo number_format((float)$KILOSRECEPCIONACUMULADOS, 2, ',', '.'); ?> kg</p>
                                    <p class="kpi-foot">Materia prima neta recepcionada</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15 d-flex">
                                <div class="kpi-card w-100">
                                    <p class="kpi-title">Kilos recepcionados (día anterior)</p>
                                    <p class="kpi-value"><?php echo number_format((float)$KILOSRECEPCIONHOY, 2, ',', '.'); ?> kg</p>
                                    <p class="kpi-foot">Ingresos netos del último día cerrado</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15 d-flex">
                                <div class="kpi-card w-100">
                                    <p class="kpi-title">Kilos procesados acumulados</p>
                                    <p class="kpi-value"><?php echo number_format((float)$KILOSPROCESOACUMULADOS, 2, ',', '.'); ?> kg</p>
                                    <p class="kpi-foot">Neto de entrada procesado al día previo</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15 d-flex">
                                <div class="kpi-card w-100">
                                    <p class="kpi-title">Kilos procesados (día anterior)</p>
                                    <p class="kpi-value"><?php echo number_format((float)$KILOSPROCESOHOY, 2, ',', '.'); ?> kg</p>
                                    <p class="kpi-foot">Procesos cerrados el último día</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box box-clean section-shell">
                                    <div class="box-body p-0">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Kilos por productor (CSP)</h4>
                                            <p class="helper-text mb-0">Netos de recepciones</p>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-compact">
                                                <thead>
                                                    <tr>
                                                        <th>Productor</th>
                                                        <th>CSP</th>
                                                        <th class="text-right">Kilos netos</th>
                                                        <th class="text-right">Recepciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($DETALLEPRODUCTOR) { ?>
                                                        <?php foreach ($DETALLEPRODUCTOR as $productor) { ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($productor['NOMBRE']); ?></td>
                                                                <td><?php echo $productor['CSP'] ? $productor['CSP'] : 'Sin dato'; ?></td>
                                                                <td class="text-right"><?php echo number_format($productor['TOTAL'], 0, ',', '.'); ?> kg</td>
                                                                <td class="text-right"><?php echo number_format($productor['RECEPCIONES'], 0, ',', '.'); ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">Sin información disponible.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <?php if ($DETALLEPRODUCTOR) { ?>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="2">Totales</td>
                                                            <td class="text-right"><?php echo number_format($TOTALPRODUCTORKILOS, 0, ',', '.'); ?> kg</td>
                                                            <td class="text-right"><?php echo number_format($TOTALPRODUCTORECEPCIONES, 0, ',', '.'); ?></td>
                                                        </tr>
                                                    </tfoot>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box box-clean section-shell">
                                    <div class="box-body p-0">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Kilos por variedad</h4>
                                            <p class="helper-text mb-0">Distribución por especie</p>
                                        </div>
                                        <div id="chartVariedad" class="chart-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box box-clean section-shell">
                                    <div class="box-body p-0">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Kilos por semana</h4>
                                            <span class="helper-text">Promedia el neto recepcionado semanal</span>
                                        </div>
                                        <div id="chartSemanas" class="chart-container"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box box-clean section-shell">
                                    <div class="box-body p-0">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Kilos por CSP y variedad</h4>
                                            <span class="helper-text">Detalle neto por productor y variedad</span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-compact">
                                                <thead>
                                                    <tr>
                                                        <th>Productor</th>
                                                        <th>CSP</th>
                                                        <th>Variedad</th>
                                                        <th class="text-right">Kilos netos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($DETALLECSPVARIEDAD) { ?>
                                                        <?php foreach ($DETALLECSPVARIEDAD as $fila) { ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($fila['PRODUCTOR']); ?></td>
                                                                <td><?php echo $fila['CSP'] ? $fila['CSP'] : 'Sin dato'; ?></td>
                                                                <td><?php echo htmlspecialchars($fila['VARIEDAD']); ?></td>
                                                                <td class="text-right"><?php echo number_format($fila['TOTAL'], 0, ',', '.'); ?> kg</td>
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
                                                            <td class="text-right"><?php echo number_format($TOTALCSPVARIEDAD, 0, ',', '.'); ?> kg</td>
                                                        </tr>
                                                    </tfoot>
                                                <?php } ?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="box box-clean section-shell">
                                    <div class="box-body p-0">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Documentos próximos a vencer</h4>
                                            <span class="helper-text">Nombre registrado, vigencia y descarga directa</span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-compact">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre registrado</th>
                                                        <th>Vigencia</th>
                                                        <th>Días restantes</th>
                                                        <th>Descargar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($DOCUMENTOSPORVENCER) { ?>
                                                        <?php $hoy = new DateTime(); ?>
                                                        <?php foreach ($DOCUMENTOSPORVENCER as $documento) { ?>
                                                            <?php
                                                                $vigencia = new DateTime($documento->vigencia_documento);
                                                                $diasRestantes = (int) $hoy->diff($vigencia)->format('%r%a');
                                                            ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($documento->nombre_documento); ?></td>
                                                                <td><?php echo $documento->vigencia_documento; ?></td>
                                                                <td><?php echo $diasRestantes >= 0 ? $diasRestantes . ' días' : 'Vencido'; ?></td>
                                                                <td>
                                                                    <a href="../../data/data_productor/<?php echo $documento->archivo_documento; ?>" target="_blank" class="btn btn-info btn-sm">
                                                                        <i class="ti-download"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted">Aún no existen documentos próximos a vencer.</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4">Total documentos listados: <?php echo $TOTALDOCUMENTOS; ?></td>
                                                    </tr>
                                                </tfoot>
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
            const datosVariedad = <?php echo json_encode($KILOSVARIEDAD); ?>;
            const datosSemanas = <?php echo json_encode($KILOSSEMANA); ?>;

            (function generarCharts() {
                const variedadColumns = [['Variedad', ...datosVariedad.map((v) => v.TOTAL)]];
                const variedadCategories = datosVariedad.map((v) => v.NOMBRE);

                c3.generate({
                    bindto: '#chartVariedad',
                    data: {
                        columns: variedadColumns,
                        type: 'bar',
                        colors: {
                            Variedad: '#0d6efd'
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
                    },
                    bar: {
                        width: {
                            ratio: 0.6
                        }
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
