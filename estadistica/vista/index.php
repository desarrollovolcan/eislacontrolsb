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
$filtroVariedad = isset($_GET['variedad']) ? trim($_GET['variedad']) : '';

// Variables de ayuda
$PRODUCTORESASOCIADOS = [];
$cacheProductores = [];
$cacheVariedades = [];
$cacheEspecies = [];
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
            $temperatura = $recepcion['TEMPERATURA_RECEPCION'] ?? '';

            if (!isset($cacheProductores[$productorId])) {
                $prodData = $PRODUCTOR_ADO->verProductor($productorId);
                $cacheProductores[$productorId] = $prodData ? $prodData[0] : null;
            }
            $productorData = $cacheProductores[$productorId];
            $nombreProductor = $productorData ? $productorData['NOMBRE_PRODUCTOR'] : 'Productor sin nombre';
            $csg = $productorData ? $productorData['CSG_PRODUCTOR'] : '';
            $tipoProductor = $productorData ? $productorData['TIPO_PRODUCTOR'] : '';

            $kilosDeclaradosRecepcion = isset($recepcion['TOTAL_KILOS_GUIA_RECEPCION']) ? (float)$recepcion['TOTAL_KILOS_GUIA_RECEPCION'] : 0;
            $acumNetoRecepcion = 0;
            $acumEnvasesRecepcion = 0;

            $detalles = $DRECEPCIONMP_ADO->buscarPorRecepcion($recepcion['ID_RECEPCION']);
            foreach ($detalles as $detalle) {
                $vespecieId = $detalle['ID_VESPECIES'];
                if (!isset($cacheVariedades[$vespecieId])) {
                    $variedad = $VESPECIES_ADO->verVespecies($vespecieId);
                    $cacheVariedades[$vespecieId] = $variedad ? $variedad[0] : null;
                }
                $variedadData = $cacheVariedades[$vespecieId];
                $especieId = $variedadData ? $variedadData['ID_ESPECIES'] : null;

                if ($filtroVariedad && $vespecieId != $filtroVariedad) {
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
                $nombreEstandar = isset($detalle['NOMBRE_ESTANDAR']) && $detalle['NOMBRE_ESTANDAR'] !== ''
                    ? $detalle['NOMBRE_ESTANDAR']
                    : 'Estándar ' . $codigoEstandar;
                if (!isset($porEnvase[$codigoEstandar])) {
                    $porEnvase[$codigoEstandar] = [
                        'CODIGO' => $codigoEstandar,
                        'NOMBRE' => $nombreEstandar,
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
                    'PATENTE_CAMION' => $recepcion['PATENTE_CAMION'] ?? '',
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

$merma = $resumen['kilos_declarados'] > 0 ? (($resumen['kilos_declarados'] - $resumen['kilos_neto']) / max($resumen['kilos_declarados'], 1)) * 100 : 0;
$totalVariedades = count($porVariedad);
$totalProductores = count($porProductor);
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
            --green: #2ecc71;
            --blue: #1e88e5;
            --teal: #26c6da;
            --amber: #f6c344;
            --dark: #0f172a;
        }

        .dashboard-bg {
            background: linear-gradient(135deg, rgba(30, 136, 229, 0.12), rgba(46, 204, 113, 0.14)), #f7fafc;
        }

        .summary-card {
            color: #fff;
            border: 0;
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0.05));
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .summary-card:before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.2), transparent 45%),
                radial-gradient(circle at 80% 10%, rgba(255, 255, 255, 0.12), transparent 35%);
            pointer-events: none;
        }

        .summary-card .box-body {
            position: relative;
            z-index: 2;
        }

        .summary-icon {
            width: 56px;
            height: 56px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #fff;
            font-size: 24px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.35);
        }

        .bg-blue { background: linear-gradient(120deg, #1e88e5, #64b5f6); }
        .bg-green { background: linear-gradient(120deg, #2ecc71, #6ee7b7); }
        .bg-teal { background: linear-gradient(120deg, #26c6da, #67e8f9); }
        .bg-amber { background: linear-gradient(120deg, #f6c344, #f59e0b); }

        .panel-title {
            font-weight: 700;
            color: var(--dark);
        }

        .box {
            border-radius: 16px;
            border: 0;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.06);
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
            border: 1px solid #e2e8f0;
        }

        .table thead th { white-space: nowrap; }
        .kpi-diff { font-weight: 700; }
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
                            <div class="col-md-5">
                                <label class="form-label">Variedad</label>
                                <input type="text" name="variedad" value="<?php echo htmlspecialchars($filtroVariedad); ?>" class="form-control" placeholder="ID Variedad o nombre">
                            </div>
                            <div class="col-md-7">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label">Fecha cosecha desde</label>
                                        <input type="date" name="fecha_desde" value="<?php echo htmlspecialchars($fechaDesde); ?>" class="form-control">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Fecha cosecha hasta</label>
                                        <input type="date" name="fecha_hasta" value="<?php echo htmlspecialchars($fechaHasta); ?>" class="form-control">
                                    </div>
                                </div>
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
                            <div class="box box-body summary-card bg-green">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Total kilos netos</p>
                                        <h3 class="mb-0 text-white"><?php echo number_format($resumen['kilos_neto'], 2, ',', '.'); ?> kg</h3>
                                    </div>
                                    <div class="summary-icon bg-green"><i class="mdi mdi-scale-balance"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-blue">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Total envases</p>
                                        <h3 class="mb-0 text-white"><?php echo number_format($resumen['envases'], 0, ',', '.'); ?></h3>
                                    </div>
                                    <div class="summary-icon bg-blue"><i class="mdi mdi-archive"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-teal">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Folios procesados</p>
                                        <h3 class="mb-0 text-white"><?php echo number_format($resumen['folios'], 0, ',', '.'); ?></h3>
                                    </div>
                                    <div class="summary-icon bg-teal"><i class="mdi mdi-format-list-checks"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-amber">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Kilos declarados vs neto</p>
                                        <h3 class="mb-0 text-white"><?php echo number_format($resumen['kilos_declarados'], 2, ',', '.'); ?> kg</h3>
                                        <small class="kpi-diff text-<?php echo $merma < 3 ? 'success' : ($merma < 7 ? 'warning' : 'danger'); ?>">Diferencia: <?php echo number_format($resumen['kilos_declarados'] - $resumen['kilos_neto'], 2, ',', '.'); ?> kg (<?php echo number_format($merma, 2, ',', '.'); ?>%)</small>
                                    </div>
                                    <div class="summary-icon bg-amber"><i class="mdi mdi-alert-decagram"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-blue">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Variedades activas</p>
                                        <h3 class="mb-0 text-white"><?php echo number_format($totalVariedades, 0, ',', '.'); ?></h3>
                                    </div>
                                    <div class="summary-icon bg-blue"><i class="mdi mdi-flower"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-green">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Productores activos</p>
                                        <h3 class="mb-0 text-white"><?php echo number_format($totalProductores, 0, ',', '.'); ?></h3>
                                    </div>
                                    <div class="summary-icon bg-green"><i class="mdi mdi-account-multiple"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-teal">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Merma estimada</p>
                                        <h3 class="mb-0 text-white"><?php echo number_format($merma, 2, ',', '.'); ?>%</h3>
                                    </div>
                                    <div class="summary-icon bg-teal"><i class="mdi mdi-chart-areaspline"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6 col-12">
                            <div class="box box-body summary-card bg-amber">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="mb-0 text-white-50">Diferencia total</p>
                                        <h3 class="mb-0 text-white"><?php echo number_format($resumen['kilos_declarados'] - $resumen['kilos_neto'], 2, ',', '.'); ?> kg</h3>
                                    </div>
                                    <div class="summary-icon bg-amber"><i class="mdi mdi-balance-scale"></i></div>
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
                                                        <td><?php echo htmlspecialchars($env['NOMBRE']); ?></td>
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
                    columns: datosEnvase.map(e => [e.NOMBRE, parseFloat(e.NETO || 0)]),
                    type: 'bar',
                    colors: { ...datosEnvase.reduce((acc, e, i) => ({ ...acc, [e.NOMBRE]: d3.schemeSet2[i % 8] }), {}) }
                },
                axis: {
                    x: { type: 'category', categories: datosEnvase.map(e => e.NOMBRE) },
                    y: { label: 'Kilos netos' }
                }
            });

        })();
    </script>
</body>

</html>
