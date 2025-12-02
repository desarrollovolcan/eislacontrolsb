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
$ULTIMOSDOCUMENTOS = array();

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

    $ULTIMOSDOCUMENTOS = $productorController->ultimosDocumentosProductores($PRODUCTORESASOCIADOS, $ESPECIE, 8);
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
                border: 1px solid #e5e5e5;
                border-radius: 12px;
                background: #fff;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
                padding: 18px 20px;
                height: 100%;
            }

            .kpi-title {
                font-size: 0.95rem;
                color: #6c757d;
                margin-bottom: 6px;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }

            .kpi-value {
                font-size: 1.9rem;
                font-weight: 600;
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
                margin-bottom: 10px;
            }

            .section-header .helper-text {
                margin: 0;
                color: #6c757d;
            }

            .btn-export {
                border: 1px solid #007bff;
                color: #007bff;
                background: #fff;
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
                        <div class="row mb-20">
                            <div class="col-12">
                                <p class="text-muted mb-10">Información basada en productores asociados, temporada y especie seleccionada.</p>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                                <div class="kpi-card">
                                    <div class="kpi-title">Kilos recepcionados acumulados</div>
                                    <p class="kpi-value text-primary"><?php echo number_format($KILOSRECEPCIONACUMULADOS, 0, ',', '.'); ?> kg</p>
                                    <p class="mb-0 text-muted">Materia prima neta recepcionada</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                                <div class="kpi-card">
                                    <div class="kpi-title">Kilos recepcionados hoy</div>
                                    <p class="kpi-value text-primary"><?php echo number_format($KILOSRECEPCIONHOY, 0, ',', '.'); ?> kg</p>
                                    <p class="mb-0 text-muted">Ingresos del día actual</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                                <div class="kpi-card">
                                    <div class="kpi-title">Kilos procesados acumulados</div>
                                    <p class="kpi-value text-primary"><?php echo number_format($KILOSPROCESOACUMULADOS, 0, ',', '.'); ?> kg</p>
                                    <p class="mb-0 text-muted">Procesos cerrados a la fecha</p>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-15">
                                <div class="kpi-card">
                                    <div class="kpi-title">Kilos procesados hoy</div>
                                    <p class="kpi-value text-primary"><?php echo number_format($KILOSPROCESOHOY, 0, ',', '.'); ?> kg</p>
                                    <p class="mb-0 text-muted">Procesos registrados en el día</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Kilos por productor (CSP)</h4>
                                            <button class="btn btn-sm btn-export" onclick="exportProductores()">Descargar Excel</button>
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
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Kilos por variedad</h4>
                                            <button class="btn btn-sm btn-export" onclick="exportVariedades()">Descargar Excel</button>
                                        </div>
                                        <div id="chartVariedad" class="chart-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Kilos por semana</h4>
                                            <span class="helper-text">Promedia el neto recepcionado semanal</span>
                                        </div>
                                        <div id="chartSemanas" class="chart-container"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12 mb-15">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Kilos por CSP y variedad</h4>
                                            <button class="btn btn-sm btn-export" onclick="exportCspVariedad()">Descargar Excel</button>
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
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="box">
                                    <div class="box-body">
                                        <div class="section-header">
                                            <h4 class="box-title mb-0">Últimos documentos subidos</h4>
                                            <span class="helper-text">Nombre registrado, vigencia y descarga directa</span>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-compact">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre registrado</th>
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
        <script src="../../api/cryptioadmin10/html/assets/vendor_components/d3/d3.min.js"></script>
        <script src="../../api/cryptioadmin10/html/assets/vendor_components/c3/c3.min.js"></script>
        <script>
            const datosVariedad = <?php echo json_encode($KILOSVARIEDAD); ?>;
            const datosSemanas = <?php echo json_encode($KILOSSEMANA); ?>;
            const datosProductor = <?php echo json_encode($DETALLEPRODUCTOR); ?>;
            const datosCspVariedad = <?php echo json_encode($DETALLECSPVARIEDAD); ?>;

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
            }

            function exportProductores() {
                const headers = ['Productor', 'CSP', 'Kilos netos', 'Recepciones'];
                const rows = datosProductor.map((p) => [p.NOMBRE, p.CSP || 'Sin dato', p.TOTAL, p.RECEPCIONES]);
                exportToExcel('kilos_por_productor.csv', headers, rows);
            }

            function exportVariedades() {
                const headers = ['Variedad', 'Kilos netos'];
                const rows = datosVariedad.map((v) => [v.NOMBRE, v.TOTAL]);
                exportToExcel('kilos_por_variedad.csv', headers, rows);
            }

            function exportCspVariedad() {
                const headers = ['Productor', 'CSP', 'Variedad', 'Kilos netos'];
                const rows = datosCspVariedad.map((v) => [v.PRODUCTOR, v.CSP || 'Sin dato', v.VARIEDAD, v.TOTAL]);
                exportToExcel('kilos_por_csp_variedad.csv', headers, rows);
            }

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
