<?php
require_once __DIR__ . "/../../assest/config/validarUsuarioOpera.php";

// Controladores requeridos
require_once __DIR__ . "/../../assest/controlador/RECEPCIONIND_ADO.php";
require_once __DIR__ . "/../../assest/controlador/DRECEPCIONIND_ADO.php";
require_once __DIR__ . "/../../assest/controlador/VESPECIES_ADO.php";
require_once __DIR__ . "/../../assest/controlador/PRODUCTOR_ADO.php";
require_once __DIR__ . "/../../assest/controlador/EMPRESAPRODUCTOR_ADO.php";

// Inicialización de controladores
$RECEPCIONIND_ADO = new RECEPCIONIND_ADO();
$DRECEPCIONIND_ADO = new DRECEPCIONIND_ADO();
$VESPECIES_ADO = new VESPECIES_ADO();
$PRODUCTOR_ADO = new PRODUCTOR_ADO();
$EMPRESAPRODUCTOR_ADO = new EMPRESAPRODUCTOR_ADO();

// Variables base
$fechaAyer = (new DateTime('yesterday'))->format('Y-m-d');
$PRODUCTORESASOCIADOS = [];
$cacheProductores = [];
$cacheVariedades = [];

// Acumuladores para el día anterior
$ayerResumen = [
    'kilos' => 0,
    'envases' => 0,
    'bruto' => 0,
    'recepciones' => 0,
];
$ayerVariedades = [];
$ayerProductores = [];

// Acumuladores para recepciones cerradas
$cerradasResumen = [
    'kilos' => 0,
    'envases' => 0,
    'recepciones' => 0,
];
$recepcionesCerradas = [];

// Obtener productores asociados al usuario
$ARRAYEMPRESAPRODUCTOR = $EMPRESAPRODUCTOR_ADO->buscarEmpresaProductorPorUsuarioCBX($IDUSUARIOS);
if ($ARRAYEMPRESAPRODUCTOR) {
    foreach ($ARRAYEMPRESAPRODUCTOR as $registroProductor) {
        $PRODUCTORESASOCIADOS[] = $registroProductor['ID_PRODUCTOR'];
    }
    $PRODUCTORESASOCIADOS = array_unique($PRODUCTORESASOCIADOS);
}

// Procesar recepciones por productor
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

            $productorId = $recepcion['ID_PRODUCTOR'];
            if (!isset($cacheProductores[$productorId])) {
                $prodData = $PRODUCTOR_ADO->verProductor($productorId);
                $cacheProductores[$productorId] = $prodData ? $prodData[0] : null;
            }
            $productorData = $cacheProductores[$productorId];
            $nombreProductor = $productorData ? $productorData['NOMBRE_PRODUCTOR'] : 'Sin datos';
            $csp = $productorData ? $productorData['CSG_PRODUCTOR'] : null;

            $sumaNeto = 0;
            $sumaEnvases = 0;
            $sumaBruto = 0;

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

                $neto = (float)$detalle['NETO'];
                $envases = (int)$detalle['ENVASE'];
                $bruto = isset($detalle['BRUTO']) ? (float)$detalle['BRUTO'] : 0;

                $sumaNeto += $neto;
                $sumaEnvases += $envases;
                $sumaBruto += $bruto;

                // Totales día anterior por variedad
                if ($recepcion['FECHA'] === $fechaAyer) {
                    $variedadNombre = $variedadData['NOMBRE_VESPECIES'];
                    if (!isset($ayerVariedades[$vespecieId])) {
                        $ayerVariedades[$vespecieId] = [
                            'VARIEDAD' => $variedadNombre,
                            'TOTAL' => 0,
                        ];
                    }
                    $ayerVariedades[$vespecieId]['TOTAL'] += $neto;
                }
            }

            // Resumen por productor para el día anterior
            if ($recepcion['FECHA'] === $fechaAyer && $sumaNeto > 0) {
                $ayerResumen['kilos'] += $sumaNeto;
                $ayerResumen['envases'] += $sumaEnvases;
                $ayerResumen['bruto'] += $sumaBruto;
                $ayerResumen['recepciones']++;

                if (!isset($ayerProductores[$productorId])) {
                    $ayerProductores[$productorId] = [
                        'NOMBRE' => $nombreProductor,
                        'CSP' => $csp,
                        'KILOS' => 0,
                        'ENVASES' => 0,
                    ];
                }
                $ayerProductores[$productorId]['KILOS'] += $sumaNeto;
                $ayerProductores[$productorId]['ENVASES'] += $sumaEnvases;
            }

            // Recepciones cerradas (ESTADO = 0)
            if ($recepcion['ESTADO'] == 0 && $sumaNeto > 0) {
                $cerradasResumen['kilos'] += $sumaNeto;
                $cerradasResumen['envases'] += $sumaEnvases;
                $cerradasResumen['recepciones']++;

                $recepcionesCerradas[] = [
                    'NUMERO' => $recepcion['NUMERO_RECEPCION'],
                    'FECHA' => $recepcion['FECHA'],
                    'PRODUCTOR' => $nombreProductor,
                    'CSP' => $csp,
                    'KILOS' => $sumaNeto,
                    'ENVASES' => $sumaEnvases,
                ];
            }
        }
    }
}

// Ordenar y preparar datos para gráficos
$ayerVariedades = array_values($ayerVariedades);
usort($ayerVariedades, function ($a, $b) {
    return $b['TOTAL'] <=> $a['TOTAL'];
});

$ayerProductores = array_values($ayerProductores);
usort($ayerProductores, function ($a, $b) {
    return $b['KILOS'] <=> $a['KILOS'];
});

usort($recepcionesCerradas, function ($a, $b) {
    return strcmp($b['FECHA'], $a['FECHA']);
});
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard de recepciones</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <link rel="stylesheet" href="../../api/cryptioadmin10/html/assets/vendor_components/c3/c3.min.css">
    <style>
        .summary-card {
            color: #fff;
            border: 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .bg-primary {
            background: linear-gradient(135deg, #2563eb, #60a5fa);
        }

        .bg-success {
            background: linear-gradient(135deg, #16a34a, #4ade80);
        }

        .bg-warning {
            background: linear-gradient(135deg, #d97706, #fbbf24);
        }

        .bg-info {
            background: linear-gradient(135deg, #0ea5e9, #38bdf8);
        }

        .compact-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .compact-card .box-body {
            flex: 1;
        }

        .table thead th {
            white-space: nowrap;
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
                            <h3 class="page-title">Dashboard de materia prima</h3>
                            <div class="d-inline-block align-items-center">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                        <li class="breadcrumb-item" aria-current="page">Estadísticas</li>
                                        <li class="breadcrumb-item active" aria-current="page">Recepciones</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>
                <section class="content">
                    <p class="text-muted mb-3">Resumen centrado únicamente en materia prima recepcionada el día anterior y en recepciones cerradas de la temporada y especie seleccionada.</p>

                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-primary">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Kilos netos día anterior</p>
                                        <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$ayerResumen['kilos'], 2, ',', '.'); ?> kg</h3>
                                    </div>
                                    <span class="icon-Alarm-clock fs-40 text-white"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-success">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Envases día anterior</p>
                                        <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$ayerResumen['envases'], 0, ',', '.'); ?></h3>
                                    </div>
                                    <span class="icon-Incoming-mail fs-40 text-white"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-warning">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Recepciones día anterior</p>
                                        <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$ayerResumen['recepciones'], 0, ',', '.'); ?></h3>
                                    </div>
                                    <span class="icon-Notes fs-40 text-white"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-info">
                                <div class="flexbox align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Kilos en recepciones cerradas</p>
                                        <h3 class="mt-0 mb-0 text-white"><?php echo number_format((float)$cerradasResumen['kilos'], 2, ',', '.'); ?> kg</h3>
                                    </div>
                                    <span class="icon-Lock fs-40 text-white"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-12">
                            <div class="box compact-card">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Distribución por variedad (día anterior)</h4>
                                </div>
                                <div class="box-body">
                                    <div id="chartVariedad" style="min-height:320px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-12">
                            <div class="box compact-card">
                                <div class="box-header with-border">
                                    <h4 class="box-title mb-0">Kilos por productor (día anterior)</h4>
                                </div>
                                <div class="box-body">
                                    <div id="chartProductor" style="min-height:320px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-12">
                            <div class="box compact-card">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Detalle día anterior por productor</h4>
                                        <span class="badge badge-primary">Ayer</span>
                                    </div>
                                </div>
                                <div class="box-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Productor</th>
                                                    <th>CSP</th>
                                                    <th class="text-right">Kilos netos</th>
                                                    <th class="text-right">Envases</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($ayerProductores) { ?>
                                                    <?php foreach ($ayerProductores as $prod) { ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($prod['NOMBRE']); ?></td>
                                                            <td><?php echo $prod['CSP'] ? $prod['CSP'] : 'Sin dato'; ?></td>
                                                            <td class="text-right"><?php echo number_format((float)$prod['KILOS'], 2, ',', '.'); ?> kg</td>
                                                            <td class="text-right"><?php echo number_format((float)$prod['ENVASES'], 0, ',', '.'); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">Sin recepciones registradas el día anterior.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-6 col-12">
                            <div class="box compact-card">
                                <div class="box-header with-border">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="box-title mb-0">Recepciones cerradas</h4>
                                        <span class="badge badge-info">Cerradas</span>
                                    </div>
                                </div>
                                <div class="box-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>N° Recepción</th>
                                                    <th>Fecha</th>
                                                    <th>Productor</th>
                                                    <th>CSP</th>
                                                    <th class="text-right">Kilos netos</th>
                                                    <th class="text-right">Envases</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($recepcionesCerradas) { ?>
                                                    <?php foreach ($recepcionesCerradas as $fila) { ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($fila['NUMERO']); ?></td>
                                                            <td><?php echo $fila['FECHA']; ?></td>
                                                            <td><?php echo htmlspecialchars($fila['PRODUCTOR']); ?></td>
                                                            <td><?php echo $fila['CSP'] ? $fila['CSP'] : 'Sin dato'; ?></td>
                                                            <td class="text-right"><?php echo number_format((float)$fila['KILOS'], 2, ',', '.'); ?> kg</td>
                                                            <td class="text-right"><?php echo number_format((float)$fila['ENVASES'], 0, ',', '.'); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted">No hay recepciones cerradas registradas.</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                            <?php if ($recepcionesCerradas) { ?>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="4">Totales</td>
                                                        <td class="text-right"><?php echo number_format((float)$cerradasResumen['kilos'], 2, ',', '.'); ?> kg</td>
                                                        <td class="text-right"><?php echo number_format((float)$cerradasResumen['envases'], 0, ',', '.'); ?></td>
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
        const datosVariedades = <?php echo json_encode($ayerVariedades); ?>;
        const datosProductores = <?php echo json_encode($ayerProductores); ?>;

        (function generarCharts() {
            c3.generate({
                bindto: '#chartVariedad',
                data: {
                    columns: [
                        ['Kilos netos', ...datosVariedades.map((v) => parseFloat(v.TOTAL))]
                    ],
                    type: 'bar',
                    colors: {
                        'Kilos netos': '#2563eb'
                    }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: datosVariedades.map((v) => v.VARIEDAD)
                    },
                    y: {
                        label: 'Kilos netos'
                    }
                }
            });

            c3.generate({
                bindto: '#chartProductor',
                data: {
                    columns: [
                        ['Kilos netos', ...datosProductores.map((p) => parseFloat(p.KILOS))]
                    ],
                    type: 'line',
                    colors: {
                        'Kilos netos': '#16a34a'
                    }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: datosProductores.map((p) => p.NOMBRE)
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
