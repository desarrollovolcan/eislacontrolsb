<?php
require_once __DIR__ . "/../../assest/config/validarUsuarioOpera.php";

// Controladores requeridos para la data de materia prima
require_once __DIR__ . "/../../assest/controlador/RECEPCIONMP_ADO.php";
require_once __DIR__ . "/../../assest/controlador/DRECEPCIONMP_ADO.php";
require_once __DIR__ . "/../../assest/controlador/VESPECIES_ADO.php";
require_once __DIR__ . "/../../assest/controlador/ESPECIES_ADO.php";
require_once __DIR__ . "/../../assest/controlador/PRODUCTOR_ADO.php";
require_once __DIR__ . "/../../assest/controlador/EMPRESAPRODUCTOR_ADO.php";
require_once __DIR__ . "/../../assest/controlador/TRANSPORTE_ADO.php";
require_once __DIR__ . "/../../assest/controlador/CONDUCTOR_ADO.php";

// Inicialización de controladores
$RECEPCIONMP_ADO = new RECEPCIONMP_ADO();
$DRECEPCIONMP_ADO = new DRECEPCIONMP_ADO();
$VESPECIES_ADO = new VESPECIES_ADO();
$ESPECIES_ADO = new ESPECIES_ADO();
$PRODUCTOR_ADO = new PRODUCTOR_ADO();
$EMPRESAPRODUCTOR_ADO = new EMPRESAPRODUCTOR_ADO();
$TRANSPORTE_ADO = new TRANSPORTE_ADO();
$CONDUCTOR_ADO = new CONDUCTOR_ADO();

// Parámetros y filtros
$fechaDesde = isset($_GET['fecha_desde']) && $_GET['fecha_desde'] !== '' ? $_GET['fecha_desde'] : (new DateTime('-30 days'))->format('Y-m-d');
$fechaHasta = isset($_GET['fecha_hasta']) && $_GET['fecha_hasta'] !== '' ? $_GET['fecha_hasta'] : (new DateTime('yesterday'))->format('Y-m-d');
$filtroProductor = isset($_GET['productor']) ? trim($_GET['productor']) : '';
$filtroEspecie = isset($_GET['especie']) ? trim($_GET['especie']) : '';
$filtroVariedad = isset($_GET['variedad']) ? trim($_GET['variedad']) : '';
$filtroSemana = isset($_GET['semana']) ? trim($_GET['semana']) : '';

// Variables de ayuda
$PRODUCTORESASOCIADOS = [];
$cacheProductores = [];
$cacheVariedades = [];
$cacheEspecies = [];
$cacheTransporte = [];
$cacheConductores = [];
$detalleRecepciones = [];

// Resumen general
$resumen = [
    'kilos_neto' => 0,
    'kilos_declarados' => 0,
    'envases' => 0,
    'folios' => 0,
];

// Agrupaciones para gráficos y tablas
$porProductor = [];
$porVariedad = [];
$porEspecie = [];
$porEnvase = [];
$porCSG = [];
$logisticaPatente = [];
$temperaturas = [];

// Obtener productores asociados al usuario
$ARRAYEMPRESAPRODUCTOR = $EMPRESAPRODUCTOR_ADO->buscarEmpresaProductorPorUsuarioCBX($IDUSUARIOS);
if ($ARRAYEMPRESAPRODUCTOR) {
    foreach ($ARRAYEMPRESAPRODUCTOR as $registroProductor) {
        $PRODUCTORESASOCIADOS[] = $registroProductor['ID_PRODUCTOR'];
    }
    $PRODUCTORESASOCIADOS = array_unique($PRODUCTORESASOCIADOS);
}

// Recopilar recepciones y detalles
if ($ARRAYEMPRESAPRODUCTOR) {
    foreach ($ARRAYEMPRESAPRODUCTOR as $registroProductor) {
        $recepciones = $RECEPCIONMP_ADO->listarRecepcionEmpresaPlantaTemporadaCBXProductor(
            $registroProductor['ID_EMPRESA'],
            $registroProductor['ID_PRODUCTOR'],
            $TEMPORADAS,
            $ESPECIE
        );

        foreach ($recepciones as $recepcion) {
            $fechaRecepcion = $recepcion['FECHA_RECEPCION'];
            if ($fechaRecepcion < $fechaDesde || $fechaRecepcion > $fechaHasta) {
                continue;
            }

            // Datos maestro
            $productorId = $recepcion['ID_PRODUCTOR'];
            $transportistaId = $recepcion['ID_TRANSPORTE'];
            $conductorId = $recepcion['ID_CONDUCTOR'];

            if (!isset($cacheProductores[$productorId])) {
                $prodData = $PRODUCTOR_ADO->verProductor($productorId);
                $cacheProductores[$productorId] = $prodData ? $prodData[0] : null;
            }
            $productorData = $cacheProductores[$productorId];
            $nombreProductor = $productorData ? $productorData['NOMBRE_PRODUCTOR'] : 'Productor sin nombre';
            $csg = $productorData ? $productorData['CSG_PRODUCTOR'] : '';
            $tipoProductor = $productorData ? $productorData['TIPO_PRODUCTOR'] : '';

            $temperatura = isset($recepcion['TEMPERATURA_RECEPCION']) ? (float)$recepcion['TEMPERATURA_RECEPCION'] : null;
            if ($temperatura !== null) {
                $temperaturas[] = $temperatura;
            }

            $kilosDeclaradosRecepcion = isset($recepcion['TOTAL_KILOS_GUIA_RECEPCION']) ? (float)$recepcion['TOTAL_KILOS_GUIA_RECEPCION'] : 0;
            $acumNetoRecepcion = 0;
            $acumEnvasesRecepcion = 0;

            if ($transportistaId && !isset($cacheTransporte[$transportistaId])) {
                $transporte = $TRANSPORTE_ADO->verTransporte($transportistaId);
                $cacheTransporte[$transportistaId] = $transporte ? $transporte[0] : null;
            }
            if ($conductorId && !isset($cacheConductores[$conductorId])) {
                $conductor = $CONDUCTOR_ADO->verConductor($conductorId);
                $cacheConductores[$conductorId] = $conductor ? $conductor[0] : null;
            }

            $detalles = $DRECEPCIONMP_ADO->buscarPorRecepcion($recepcion['ID_RECEPCION']);
            foreach ($detalles as $detalle) {
                $vespecieId = $detalle['ID_VESPECIES'];
                if (!isset($cacheVariedades[$vespecieId])) {
                    $variedad = $VESPECIES_ADO->verVespecies($vespecieId);
                    $cacheVariedades[$vespecieId] = $variedad ? $variedad[0] : null;
                }
                $variedadData = $cacheVariedades[$vespecieId];
                $especieId = $variedadData ? $variedadData['ID_ESPECIES'] : null;

                if ($filtroEspecie && $especieId != $filtroEspecie) {
                    continue;
                }
                if ($filtroVariedad && $vespecieId != $filtroVariedad) {
                    continue;
                }
                if ($filtroProductor && $productorId != $filtroProductor) {
                    continue;
                }

                if ($especieId && !isset($cacheEspecies[$especieId])) {
                    $especie = $ESPECIES_ADO->verEspecies($especieId);
                    $cacheEspecies[$especieId] = $especie ? $especie[0] : null;
                }
                $especieData = $cacheEspecies[$especieId] ?? null;

                $neto = (float)$detalle['NETO'];
                $bruto = (float)$detalle['BRUTO'];
                $envases = (int)$detalle['ENVASE'];
                $folios = 1;

                $acumNetoRecepcion += $neto;
                $acumEnvasesRecepcion += $envases;

                $semanaRecepcion = (new DateTime($fechaRecepcion))->format('W');
                if ($filtroSemana && $semanaRecepcion !== $filtroSemana) {
                    continue;
                }

                $resumen['kilos_neto'] += $neto;
                $resumen['envases'] += $envases;
                $resumen['folios'] += $folios;

                // Agrupación productor
                if (!isset($porProductor[$productorId])) {
                    $porProductor[$productorId] = [
                        'NOMBRE' => $nombreProductor,
                        'CSG' => $csg,
                        'TIPO' => $tipoProductor,
                        'NETO' => 0,
                        'ENVASES' => 0,
                        'DECLARADO' => 0,
                        'DIFERENCIA' => 0,
                    ];
                }
                $porProductor[$productorId]['NETO'] += $neto;
                $porProductor[$productorId]['ENVASES'] += $envases;
                $porProductor[$productorId]['DECLARADO'] += 0;

                // Agrupación variedad
                if ($variedadData) {
                    $variedadNombre = $variedadData['NOMBRE_VESPECIES'];
                    if (!isset($porVariedad[$vespecieId])) {
                        $porVariedad[$vespecieId] = [
                            'NOMBRE' => $variedadNombre,
                            'ESPECIE' => $especieData['NOMBRE_ESPECIES'] ?? 'N/D',
                            'NETO' => 0,
                        ];
                    }
                    $porVariedad[$vespecieId]['NETO'] += $neto;
                }

                // Agrupación especie
                if ($especieData) {
                    $especieNombre = $especieData['NOMBRE_ESPECIES'];
                    if (!isset($porEspecie[$especieId])) {
                        $porEspecie[$especieId] = [
                            'NOMBRE' => $especieNombre,
                            'NETO' => 0,
                        ];
                    }
                    $porEspecie[$especieId]['NETO'] += $neto;
                }

                // Agrupación envase / estándar
                $codigoEstandar = $detalle['ID_ESTANDAR'] ?? 'N/D';
                if (!isset($porEnvase[$codigoEstandar])) {
                    $porEnvase[$codigoEstandar] = [
                        'CODIGO' => $codigoEstandar,
                        'NETO' => 0,
                        'ENVASES' => 0,
                    ];
                }
                $porEnvase[$codigoEstandar]['NETO'] += $neto;
                $porEnvase[$codigoEstandar]['ENVASES'] += $envases;

                // Agrupación CSG
                if ($csg) {
                    if (!isset($porCSG[$csg])) {
                        $porCSG[$csg] = [
                            'PRODUCTOR' => $nombreProductor,
                            'NETO' => 0,
                        ];
                    }
                    $porCSG[$csg]['NETO'] += $neto;
                }

                // Logística por patente
                $patenteCamion = $recepcion['PATENTE_CAMION'] ?? '';
                if ($patenteCamion !== '') {
                    if (!isset($logisticaPatente[$patenteCamion])) {
                        $logisticaPatente[$patenteCamion] = [
                            'PATENTE' => $patenteCamion,
                            'VIAJES' => 0,
                            'KILOS' => 0,
                            'DIFERENCIA' => 0,
                        ];
                    }
                    $logisticaPatente[$patenteCamion]['VIAJES'] += 1;
                    $logisticaPatente[$patenteCamion]['KILOS'] += $neto;
                    $logisticaPatente[$patenteCamion]['DIFERENCIA'] += isset($recepcion['TOTAL_KILOS_GUIA_RECEPCION']) ? ((float)$recepcion['TOTAL_KILOS_GUIA_RECEPCION'] - $neto) : 0;
                }

                $detalleRecepciones[] = [
                    'FOLIO' => $detalle['FOLIO_DRECEPCION'] ?? $detalle['ID_DRECEPCION'],
                    'FECHA_COSECHA' => $detalle['COSECHA'],
                    'CODIGO_ESTANDAR' => $codigoEstandar,
                    'ENVASE' => $envases,
                    'CSG' => $csg,
                    'PRODUCTOR' => $nombreProductor,
                    'ESPECIE' => $especieData['NOMBRE_ESPECIES'] ?? 'N/D',
                    'VARIEDAD' => $variedadData['NOMBRE_VESPECIES'] ?? 'N/D',
                    'KILO_NETO' => $neto,
                    'KILO_BRUTO' => $bruto,
                    'KILO_DECLARADO' => $recepcion['TOTAL_KILOS_GUIA_RECEPCION'] ?? 0,
                    'DIFERENCIA' => ($recepcion['TOTAL_KILOS_GUIA_RECEPCION'] ?? 0) - $neto,
                    'RANGO' => $semanaRecepcion,
                    'DIFERENCIA_DECLARADA' => $recepcion['TOTAL_KILOS_GUIA_RECEPCION'] ?? 0,
                    'FECHA_RECEPCION' => $fechaRecepcion,
                    'NUMERO_GUIA' => $recepcion['NUMERO_GUIA_RECEPCION'] ?? '',
                    'TEMPERATURA' => $temperatura,
                    'CAMARA' => $recepcion['CAMARA_RECEPCION'] ?? '',
                    'OBSERVACIONES' => $recepcion['OBSERVACION_RECEPCION'] ?? '',
                    'TIPO_PRODUCTOR' => $tipoProductor,
                    'PATENTE_CAMION' => $patenteCamion,
                    'PATENTE_CARRO' => $recepcion['PATENTE_CARRO'] ?? '',
                    'SEMANA_RECEPCION' => $semanaRecepcion,
                    'SEMANA_GUIA' => isset($recepcion['FECHA_GUIA_RECEPCION']) ? (new DateTime($recepcion['FECHA_GUIA_RECEPCION']))->format('W') : '',
                    'EMPRESA' => $recepcion['EMPRESA'] ?? '',
                    'PLANTA' => $recepcion['PLANTA'] ?? '',
                    'TEMPORADA' => $recepcion['TEMPORADA'] ?? '',
                ];
            }

            if ($acumNetoRecepcion > 0 || $acumEnvasesRecepcion > 0) {
                $resumen['kilos_declarados'] += $kilosDeclaradosRecepcion;
                if (isset($porProductor[$productorId])) {
                    $porProductor[$productorId]['DECLARADO'] += $kilosDeclaradosRecepcion;
                    $porProductor[$productorId]['DIFERENCIA'] = $porProductor[$productorId]['DECLARADO'] - $porProductor[$productorId]['NETO'];
                }
            }
        }
    }
}

// Preparar datos para front
$porProductor = array_values($porProductor);
usort($porProductor, fn($a, $b) => $b['NETO'] <=> $a['NETO']);

$porVariedad = array_values($porVariedad);
usort($porVariedad, fn($a, $b) => $b['NETO'] <=> $a['NETO']);

$porEspecie = array_values($porEspecie);
usort($porEspecie, fn($a, $b) => $b['NETO'] <=> $a['NETO']);

$porEnvase = array_values($porEnvase);
usort($porEnvase, fn($a, $b) => $b['NETO'] <=> $a['NETO']);

$porCSG = array_values($porCSG);

$logisticaPatente = array_values($logisticaPatente);
usort($logisticaPatente, fn($a, $b) => $b['KILOS'] <=> $a['KILOS']);

$merma = $resumen['kilos_declarados'] > 0 ? (($resumen['kilos_declarados'] - $resumen['kilos_neto']) / max($resumen['kilos_declarados'], 1)) * 100 : 0;
$promedioTemperatura = $temperaturas ? array_sum($temperaturas) / count($temperaturas) : null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard profesional de recepciones</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <link rel="stylesheet" href="../../api/cryptioadmin10/html/assets/vendor_components/c3/c3.min.css">
    <style>
        :root {
            --green: #16a34a;
            --blue: #2563eb;
            --teal: #0ea5e9;
            --amber: #d97706;
        }

        .dashboard-bg {
            background: radial-gradient(circle at 20% 20%, rgba(22, 163, 74, 0.08), transparent 25%),
                radial-gradient(circle at 80% 10%, rgba(37, 99, 235, 0.08), transparent 25%),
                radial-gradient(circle at 50% 80%, rgba(14, 165, 233, 0.06), transparent 20%), #f8fafc;
        }

        .summary-card {
            color: #0f172a;
            border: 0;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            background: linear-gradient(135deg, #ffffff, #f0f9ff);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(37, 99, 235, 0.2);
        }

        .summary-icon {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #fff;
            font-size: 24px;
        }

        .bg-blue {
            background: linear-gradient(120deg, #2563eb, #60a5fa);
        }

        .bg-green {
            background: linear-gradient(120deg, #16a34a, #4ade80);
        }

        .bg-teal {
            background: linear-gradient(120deg, #0ea5e9, #38bdf8);
        }

        .bg-amber {
            background: linear-gradient(120deg, #d97706, #fbbf24);
        }

        .panel-title {
            font-weight: 700;
            color: #0f172a;
        }

        .box {
            border-radius: 18px;
            border: 0;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        }

        .box-header {
            border-bottom: 1px solid #e2e8f0;
        }

        .badge-soft {
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 600;
        }

        .filter-bar {
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        }

        .table thead th {
            white-space: nowrap;
        }

        .kpi-diff {
            font-weight: 700;
        }
    </style>
    <script type="text/javascript">
        function irPagina(url) {
            location.href = "" + url;
        }
    </script>
</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary dashboard-bg">
    <div class="wrapper">
        <?php include_once "../../assest/config/menuOpera.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <div class="content-header">
                    <div class="d-flex align-items-center">
                        <div class="mr-auto">
                            <h3 class="page-title">Dashboard de Materia Prima</h3>
                            <div class="d-inline-block align-items-center">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#"><i class="mdi mdi-home-outline"></i></a></li>
                                        <li class="breadcrumb-item" aria-current="page">Estadísticas</li>
                                        <li class="breadcrumb-item active" aria-current="page">Dashboard MP</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                        <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                    </div>
                </div>
                <section class="content">
                    <div class="filter-bar p-3 mb-4">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Fecha cosecha desde</label>
                                <input type="date" name="fecha_desde" value="<?php echo htmlspecialchars($fechaDesde); ?>" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Fecha cosecha hasta</label>
                                <input type="date" name="fecha_hasta" value="<?php echo htmlspecialchars($fechaHasta); ?>" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Productor</label>
                                <input type="text" name="productor" value="<?php echo htmlspecialchars($filtroProductor); ?>" class="form-control" placeholder="ID Productor">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Especie</label>
                                <input type="text" name="especie" value="<?php echo htmlspecialchars($filtroEspecie); ?>" class="form-control" placeholder="ID Especie">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Variedad</label>
                                <input type="text" name="variedad" value="<?php echo htmlspecialchars($filtroVariedad); ?>" class="form-control" placeholder="ID">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Semana</label>
                                <input type="text" name="semana" value="<?php echo htmlspecialchars($filtroSemana); ?>" class="form-control" placeholder="Ej: 08">
                            </div>
                            <div class="col-md-12 d-flex justify-content-between mt-3">
                                <div>
                                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-outline"></i> Aplicar filtros</button>
                                    <a href="index.php" class="btn btn-light">Limpiar</a>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-success" onclick="window.print()"><i class="mdi mdi-printer"></i> Exportar</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-muted">Total kilos netos</p>
                                        <h3 class="mb-0"><?php echo number_format($resumen['kilos_neto'], 2, ',', '.'); ?> kg</h3>
                                    </div>
                                    <div class="summary-icon bg-green">
                                        <i class="mdi mdi-scale-balance"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-muted">Total envases</p>
                                        <h3 class="mb-0"><?php echo number_format($resumen['envases'], 0, ',', '.'); ?></h3>
                                    </div>
                                    <div class="summary-icon bg-blue">
                                        <i class="mdi mdi-archive"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-muted">Folios procesados</p>
                                        <h3 class="mb-0"><?php echo number_format($resumen['folios'], 0, ',', '.'); ?></h3>
                                    </div>
                                    <div class="summary-icon bg-teal">
                                        <i class="mdi mdi-format-list-checks"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-muted">Kilos declarados vs neto</p>
                                        <h3 class="mb-0"><?php echo number_format($resumen['kilos_declarados'], 2, ',', '.'); ?> kg</h3>
                                        <small class="kpi-diff text-<?php echo $merma < 3 ? 'success' : ($merma < 7 ? 'warning' : 'danger'); ?>">Diferencia: <?php echo number_format($resumen['kilos_declarados'] - $resumen['kilos_neto'], 2, ',', '.'); ?> kg (<?php echo number_format($merma, 2, ',', '.'); ?>%)</small>
                                    </div>
                                    <div class="summary-icon bg-amber">
                                        <i class="mdi mdi-alert-decagram"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-8 col-12">
                            <div class="box">
                                <div class="box-header with-border d-flex justify-content-between align-items-center">
                                    <h4 class="box-title panel-title mb-0">Participación por especie y variedad</h4>
                                    <span class="badge-soft bg-light text-primary">Cosechas seleccionadas</span>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div id="chartEspecie" style="min-height:300px;"></div>
                                        </div>
                                        <div class="col-md-8">
                                            <div id="chartVariedad" style="min-height:300px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="box">
                                <div class="box-header with-border d-flex justify-content-between align-items-center">
                                    <h4 class="box-title panel-title mb-0">Tendencia diaria</h4>
                                    <span class="badge-soft bg-light text-success">Timeline</span>
                                </div>
                                <div class="box-body">
                                    <div id="chartTendencia" style="min-height:300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-12">
                            <div class="box">
                                <div class="box-header with-border d-flex justify-content-between align-items-center">
                                    <h4 class="box-title panel-title mb-0">Ranking por productor</h4>
                                    <span class="badge-soft bg-light text-info">CSG</span>
                                </div>
                                <div class="box-body">
                                    <div id="chartProductor" style="min-height:300px;"></div>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Productor</th>
                                                    <th>CSG</th>
                                                    <th class="text-right">Kilos netos</th>
                                                    <th class="text-right">% Variación</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($porProductor as $prod) { 
                                                    $variacion = $prod['DECLARADO'] > 0 ? (($prod['DECLARADO'] - $prod['NETO']) / max($prod['DECLARADO'], 1)) * 100 : 0;
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($prod['NOMBRE']); ?></td>
                                                        <td><?php echo htmlspecialchars($prod['CSG']); ?></td>
                                                        <td class="text-right"><?php echo number_format($prod['NETO'], 2, ',', '.'); ?> kg</td>
                                                        <td class="text-right text-<?php echo $variacion < 3 ? 'success' : ($variacion < 7 ? 'warning' : 'danger'); ?>"><?php echo number_format($variacion, 2, ',', '.'); ?>%</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-12">
                            <div class="box">
                                <div class="box-header with-border d-flex justify-content-between align-items-center">
                                    <h4 class="box-title panel-title mb-0">Envases vs kilos</h4>
                                    <span class="badge-soft bg-light text-warning">Estandar</span>
                                </div>
                                <div class="box-body">
                                    <div id="chartEnvase" style="min-height:300px;"></div>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Envase / Estándar</th>
                                                    <th class="text-right">Envases</th>
                                                    <th class="text-right">Kilos netos</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($porEnvase as $env) { ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($env['CODIGO']); ?></td>
                                                        <td class="text-right"><?php echo number_format($env['ENVASES'], 0, ',', '.'); ?></td>
                                                        <td class="text-right"><?php echo number_format($env['NETO'], 2, ',', '.'); ?> kg</td>
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
                        <div class="col-xl-6 col-12">
                            <div class="box">
                                <div class="box-header with-border d-flex justify-content-between align-items-center">
                                    <h4 class="box-title panel-title mb-0">Logística y seguimiento</h4>
                                    <span class="badge-soft bg-light text-primary">Patentes</span>
                                </div>
                                <div class="box-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Patente camión</th>
                                                    <th class="text-right">Viajes</th>
                                                    <th class="text-right">Kilos transportados</th>
                                                    <th class="text-right">Diferencias</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($logisticaPatente as $log) { ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($log['PATENTE'] ?? ''); ?></td>
                                                        <td class="text-right"><?php echo number_format($log['VIAJES'], 0, ',', '.'); ?></td>
                                                        <td class="text-right"><?php echo number_format($log['KILOS'], 2, ',', '.'); ?> kg</td>
                                                        <td class="text-right text-<?php echo $log['DIFERENCIA'] <= 0 ? 'success' : 'danger'; ?>"><?php echo number_format($log['DIFERENCIA'], 2, ',', '.'); ?> kg</td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <p class="mb-1 text-muted">Temperatura promedio</p>
                                        <h4 class="mb-0"><?php echo $promedioTemperatura !== null ? number_format($promedioTemperatura, 1, ',', '.') . ' °C' : 'Sin datos'; ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-12">
                            <div class="box">
                                <div class="box-header with-border d-flex justify-content-between align-items-center">
                                    <h4 class="box-title panel-title mb-0">Control de calidad</h4>
                                    <span class="badge-soft bg-light text-danger">Alertas</span>
                                </div>
                                <div class="box-body">
                                    <div id="chartDiferencias" style="min-height:280px;"></div>
                                    <p class="mt-3 mb-1 text-muted">Alertas automáticas</p>
                                    <ul class="mb-0">
                                        <li>Se resalta en rojo cuando la diferencia supera el 7%.</li>
                                        <li>Variación moderada entre 3% y 7% en amarillo.</li>
                                        <li>Óptimo bajo 3% en verde.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="box-header with-border d-flex justify-content-between align-items-center">
                            <h4 class="box-title panel-title mb-0">Detallado de recepciones</h4>
                            <div>
                                <input type="search" class="form-control" placeholder="Buscar folio" onkeyup="buscarFolio(this.value)">
                            </div>
                        </div>
                        <div class="box-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="tablaDetalle">
                                    <thead>
                                        <tr class="text-center">
                                            <th>N° Folio</th>
                                            <th>Fecha Cosecha</th>
                                            <th>Código Estándar</th>
                                            <th>Envase / Estándar</th>
                                            <th>CSG</th>
                                            <th>Productor</th>
                                            <th>Especie</th>
                                            <th>Variedad</th>
                                            <th>Cantidad Envase</th>
                                            <th>Kilo Neto</th>
                                            <th>Kilo Bruto</th>
                                            <th>Kilos Declarados</th>
                                            <th>Diferencia</th>
                                            <th>Rango</th>
                                            <th>Diferencia Declarada</th>
                                            <th>Fecha Recepción</th>
                                            <th>Número Guía</th>
                                            <th>Temperatura</th>
                                            <th>Cámara</th>
                                            <th>Observaciones</th>
                                            <th>Tipo de Productor</th>
                                            <th>Patente Camión</th>
                                            <th>Patente Carro</th>
                                            <th>Semana Recepción</th>
                                            <th>Semana Guía</th>
                                            <th>Empresa</th>
                                            <th>Planta</th>
                                            <th>Temporada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($detalleRecepciones as $detalle) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($detalle['FOLIO']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['FECHA_COSECHA']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['CODIGO_ESTANDAR']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['CODIGO_ESTANDAR']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['CSG']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['PRODUCTOR']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['ESPECIE']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['VARIEDAD']); ?></td>
                                                <td class="text-right"><?php echo number_format($detalle['ENVASE'], 0, ',', '.'); ?></td>
                                                <td class="text-right"><?php echo number_format($detalle['KILO_NETO'], 2, ',', '.'); ?></td>
                                                <td class="text-right"><?php echo number_format($detalle['KILO_BRUTO'], 2, ',', '.'); ?></td>
                                                <td class="text-right"><?php echo number_format($detalle['KILO_DECLARADO'], 2, ',', '.'); ?></td>
                                                <td class="text-right text-<?php echo $detalle['DIFERENCIA'] <= 0 ? 'success' : 'danger'; ?>"><?php echo number_format($detalle['DIFERENCIA'], 2, ',', '.'); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['RANGO']); ?></td>
                                                <td class="text-right"><?php echo number_format($detalle['DIFERENCIA_DECLARADA'], 2, ',', '.'); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['FECHA_RECEPCION']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['NUMERO_GUIA']); ?></td>
                                                <td><?php echo $detalle['TEMPERATURA'] !== null ? number_format($detalle['TEMPERATURA'], 1, ',', '.') : 'N/D'; ?></td>
                                                <td><?php echo htmlspecialchars($detalle['CAMARA']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['OBSERVACIONES']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['TIPO_PRODUCTOR']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['PATENTE_CAMION']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['PATENTE_CARRO']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['SEMANA_RECEPCION']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['SEMANA_GUIA']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['EMPRESA']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['PLANTA']); ?></td>
                                                <td><?php echo htmlspecialchars($detalle['TEMPORADA']); ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
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
        const datosProductor = <?php echo json_encode($porProductor); ?>;
        const datosVariedad = <?php echo json_encode($porVariedad); ?>;
        const datosEspecie = <?php echo json_encode($porEspecie); ?>;
        const datosEnvase = <?php echo json_encode($porEnvase); ?>;
        const datosDetalle = <?php echo json_encode($detalleRecepciones); ?>;

        function buscarFolio(valor) {
            const term = valor.toLowerCase();
            document.querySelectorAll('#tablaDetalle tbody tr').forEach((fila) => {
                const folio = fila.cells[0].innerText.toLowerCase();
                fila.style.display = folio.includes(term) ? '' : 'none';
            });
        }

        (function generarCharts() {
            c3.generate({
                bindto: '#chartEspecie',
                data: {
                    columns: datosEspecie.map(e => [e.NOMBRE, parseFloat(e.NETO || 0)]),
                    type: 'donut',
                    colors: {
                        ...datosEspecie.reduce((acc, e, i) => ({ ...acc, [e.NOMBRE]: d3.schemeCategory10[i % 10] }), {})
                    }
                },
                donut: {
                    title: 'Especies'
                }
            });

            c3.generate({
                bindto: '#chartVariedad',
                data: {
                    columns: datosVariedad.map(v => [v.NOMBRE, parseFloat(v.NETO || 0)]),
                    type: 'bar',
                    colors: {
                        ...datosVariedad.reduce((acc, v, i) => ({ ...acc, [v.NOMBRE]: d3.schemeTableau10[i % 10] }), {})
                    }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: datosVariedad.map(v => v.NOMBRE)
                    },
                    y: {
                        label: 'Kilos netos'
                    }
                },
                bar: { width: { ratio: 0.7 } }
            });

            c3.generate({
                bindto: '#chartProductor',
                data: {
                    columns: [
                        ['Kilos netos', ...datosProductor.map(p => parseFloat(p.NETO || 0))]
                    ],
                    type: 'bar',
                    colors: { 'Kilos netos': '#16a34a' }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: datosProductor.map(p => p.NOMBRE)
                    },
                    y: { label: 'Kilos netos' }
                },
                tooltip: {
                    format: {
                        name: (name, ratio, id, index) => datosProductor[index]?.CSG || name
                    }
                }
            });

            const tendenciaPorFecha = datosDetalle.reduce((acc, item) => {
                if (!acc[item.FECHA_RECEPCION]) {
                    acc[item.FECHA_RECEPCION] = 0;
                }
                acc[item.FECHA_RECEPCION] += parseFloat(item.KILO_NETO || 0);
                return acc;
            }, {});
            const fechasOrdenadas = Object.keys(tendenciaPorFecha).sort();

            c3.generate({
                bindto: '#chartTendencia',
                data: {
                    x: 'x',
                    columns: [
                        ['x', ...fechasOrdenadas],
                        ['Kilos netos', ...fechasOrdenadas.map(f => tendenciaPorFecha[f])]
                    ],
                    type: 'spline',
                    colors: { 'Kilos netos': '#2563eb' }
                },
                axis: {
                    x: { type: 'category', tick: { rotate: 45 } },
                    y: { label: 'Kilos netos' }
                }
            });

            c3.generate({
                bindto: '#chartEnvase',
                data: {
                    columns: datosEnvase.map(e => [e.CODIGO, parseFloat(e.NETO || 0)]),
                    type: 'bar',
                    colors: { ...datosEnvase.reduce((acc, e, i) => ({ ...acc, [e.CODIGO]: d3.schemeSet2[i % 8] }), {}) }
                },
                axis: {
                    x: { type: 'category', categories: datosEnvase.map(e => e.CODIGO) },
                    y: { label: 'Kilos netos' }
                }
            });

            const difTotales = datosProductor.map(p => p.DIFERENCIA || 0);
            c3.generate({
                bindto: '#chartDiferencias',
                data: {
                    columns: [
                        ['Diferencia', ...difTotales]
                    ],
                    type: 'area-spline',
                    colors: { 'Diferencia': '#d97706' }
                },
                axis: {
                    x: { type: 'category', categories: datosProductor.map(p => p.NOMBRE) },
                    y: { label: 'Kg declarados - Kg netos' }
                }
            });
        })();
    </script>
</body>

</html>
