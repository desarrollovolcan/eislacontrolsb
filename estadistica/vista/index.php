<?php
require_once __DIR__ . "/../../assest/config/validarUsuarioOpera.php";

// Controladores requeridos para la data de materia prima
require_once __DIR__ . "/../../assest/controlador/RECEPCIONMP_ADO.php";
require_once __DIR__ . "/../../assest/controlador/DRECEPCIONMP_ADO.php";
require_once __DIR__ . "/../../assest/controlador/VESPECIES_ADO.php";
require_once __DIR__ . "/../../assest/controlador/ESPECIES_ADO.php";
require_once __DIR__ . "/../../assest/controlador/PRODUCTOR_ADO.php";
require_once __DIR__ . "/../../assest/controlador/EMPRESAPRODUCTOR_ADO.php";

// Inicialización de controladores
$RECEPCIONMP_ADO = new RECEPCIONMP_ADO();
$DRECEPCIONMP_ADO = new DRECEPCIONMP_ADO();
$VESPECIES_ADO = new VESPECIES_ADO();
$ESPECIES_ADO = new ESPECIES_ADO();
$PRODUCTOR_ADO = new PRODUCTOR_ADO();
$EMPRESAPRODUCTOR_ADO = new EMPRESAPRODUCTOR_ADO();

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
$recepcionesProcesadas = 0;
$recepcionesConObservaciones = 0;

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

            $productorId = $recepcion['ID_PRODUCTOR'];
            $observacionRecepcion = trim($recepcion['OBSERVACION_RECEPCION'] ?? '');
            $tieneDetalle = false;

            if ($observacionRecepcion !== '') {
                $recepcionesConObservaciones++;
            }

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

                if ($filtroVariedad && $vespecieId != $filtroVariedad && stripos($variedadData['NOMBRE_VESPECIES'] ?? '', $filtroVariedad) === false) {
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
                $tieneDetalle = true;

                $acumNetoRecepcion += $neto;
                $acumEnvasesRecepcion += $envases;

                $semanaRecepcion = (new DateTime($fechaRecepcion))->format('W');

                $resumen['kilos_neto'] += $neto;
                $resumen['envases'] += $envases;
                $resumen['folios'] += $folios;

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
                    'KILO_DECLARADO' => $kilosDeclaradosRecepcion,
                    'DIFERENCIA' => $kilosDeclaradosRecepcion - $neto,
                    'RANGO' => $semanaRecepcion,
                    'DIFERENCIA_DECLARADA' => $kilosDeclaradosRecepcion,
                    'FECHA_RECEPCION' => $fechaRecepcion,
                    'NUMERO_GUIA' => $recepcion['NUMERO_GUIA_RECEPCION'] ?? '',
                    'TEMPERATURA' => $recepcion['TEMPERATURA_RECEPCION'] ?? '',
                    'CAMARA' => $recepcion['CAMARA_RECEPCION'] ?? '',
                    'OBSERVACIONES' => $observacionRecepcion,
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

            if ($tieneDetalle && ($acumNetoRecepcion > 0 || $acumEnvasesRecepcion > 0)) {
                $recepcionesProcesadas++;
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

$merma = $resumen['kilos_declarados'] > 0 ? (($resumen['kilos_declarados'] - $resumen['kilos_neto']) / max($resumen['kilos_declarados'], 1)) * 100 : 0;
$totalVariedades = count($porVariedad);
$totalProductores = count($porProductor);
$kilosDiferencia = $resumen['kilos_declarados'] - $resumen['kilos_neto'];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Dashboard materia prima</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <link rel="stylesheet" href="../../api/cryptioadmin10/html/assets/vendor_components/c3/c3.min.css">
    <style>
        body {
            background: #f5f7fb;
        }

        .header-note {
            font-size: 13px;
            color: #6b7280;
        }

        .page-title {
            margin-bottom: 4px;
            font-weight: 700;
            color: #0f172a;
        }

        .panel-card {
            border: 0;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
        }

        .filter-bar {
            border-radius: 12px;
            padding: 16px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
        }

        .kpi-card {
            background: linear-gradient(135deg, #e0f2fe, #eff6ff);
            border-radius: 14px;
            border: 1px solid #dbeafe;
            padding: 16px;
            height: 100%;
        }

        .kpi-title {
            font-size: 13px;
            color: #6b7280;
        }

        .kpi-value {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }

        .kpi-hint {
            font-size: 12px;
            color: #16a34a;
        }

        .badge-soft {
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 12px;
        }

        .table-sm td {
            padding: 6px 10px;
        }

        .table thead th { white-space: nowrap; }
        .kpi-diff { font-weight: 700; }
    </style>
</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary dashboard-bg">
    <div class="wrapper">
        <?php include_once "../../assest/config/menuOpera.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <div class="content-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div>
                            <h3 class="page-title">Dashboard de productor</h3>
                            <p class="header-note">Información basada en productores asociados, temporada y especies seleccionadas.</p>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-primary badge-soft">Temporada <?php echo htmlspecialchars($TEMPORADAS); ?></span>
                            <div class="header-note">Rango: <?php echo htmlspecialchars($fechaDesde); ?> al <?php echo htmlspecialchars($fechaHasta); ?></div>
                        </div>
                    </div>
                </div>

                <section class="content">
                    <div class="filter-bar mb-4">
                        <form method="GET" class="row g-2 align-items-end">
                            <div class="col-lg-4 col-md-6">
                                <label class="form-label mb-1">Variedad</label>
                                <input type="text" name="variedad" value="<?php echo htmlspecialchars($filtroVariedad); ?>" class="form-control" placeholder="ID o nombre de variedad">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label mb-1">Fecha cosecha desde</label>
                                <input type="date" name="fecha_desde" value="<?php echo htmlspecialchars($fechaDesde); ?>" class="form-control">
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <label class="form-label mb-1">Fecha cosecha hasta</label>
                                <input type="date" name="fecha_hasta" value="<?php echo htmlspecialchars($fechaHasta); ?>" class="form-control">
                            </div>
                            <div class="col-lg-2 col-md-6 d-flex align-items-end justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary btn-sm mr-2"><i class="mdi mdi-filter-outline"></i> Aplicar</button>
                                    <a href="index.php" class="btn btn-light btn-sm">Limpiar</a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="row g-3">
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card">
                                <div class="kpi-title">Materia prima recepcionada acumulado</div>
                                <div class="kpi-value"><?php echo number_format($resumen['kilos_neto'], 2, ',', '.'); ?> kg</div>
                                <div class="kpi-hint">Productores activos: <?php echo number_format($totalProductores, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card">
                                <div class="kpi-title">Kilos recepcionados acumulados</div>
                                <div class="kpi-value"><?php echo number_format($resumen['kilos_declarados'], 2, ',', '.'); ?> kg</div>
                                <div class="kpi-hint text-<?php echo $kilosDiferencia >= 0 ? 'success' : 'danger'; ?>">Diferencia: <?php echo number_format($kilosDiferencia, 2, ',', '.'); ?> kg</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card">
                                <div class="kpi-title">Kilos netos acumulados</div>
                                <div class="kpi-value"><?php echo number_format($resumen['kilos_neto'], 2, ',', '.'); ?> kg</div>
                                <div class="kpi-hint">Variedades activas: <?php echo number_format($totalVariedades, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card">
                                <div class="kpi-title">Recepciones procesadas a la fecha</div>
                                <div class="kpi-value"><?php echo number_format($recepcionesProcesadas, 0, ',', '.'); ?></div>
                                <div class="kpi-hint text-primary">Con observaciones: <?php echo number_format($recepcionesConObservaciones, 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card" style="background: linear-gradient(135deg,#ecfccb,#fef9c3); border-color: #eab308;">
                                <div class="kpi-title">Envases acumulados</div>
                                <div class="kpi-value"><?php echo number_format($resumen['envases'], 0, ',', '.'); ?></div>
                                <div class="kpi-hint text-warning">Folios procesados: <?php echo number_format($resumen['folios'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card" style="background: linear-gradient(135deg,#dcfce7,#f0fdf4); border-color: #22c55e;">
                                <div class="kpi-title">Porcentaje merma</div>
                                <div class="kpi-value"><?php echo number_format($merma, 2, ',', '.'); ?>%</div>
                                <div class="kpi-hint text-<?php echo $merma < 3 ? 'success' : ($merma < 7 ? 'warning' : 'danger'); ?>">Desviación declarada vs neto</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card" style="background: linear-gradient(135deg,#e0f2fe,#eff6ff);">
                                <div class="kpi-title">Promedio kilos por recepción</div>
                                <div class="kpi-value"><?php echo $recepcionesProcesadas > 0 ? number_format($resumen['kilos_neto'] / $recepcionesProcesadas, 2, ',', '.') : '0,00'; ?> kg</div>
                                <div class="kpi-hint">Recepciones contabilizadas</div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="kpi-card" style="background: linear-gradient(135deg,#f1f5f9,#e2e8f0);">
                                <div class="kpi-title">Top productor</div>
                                <div class="kpi-value"><?php echo isset($porProductor[0]) ? htmlspecialchars($porProductor[0]['NOMBRE']) : 'Sin datos'; ?></div>
                                <div class="kpi-hint">Neto: <?php echo isset($porProductor[0]) ? number_format($porProductor[0]['NETO'], 2, ',', '.') . ' kg' : '--'; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-xl-6">
                            <div class="panel-card p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h5 class="mb-0">Kilos por productor (CSG)</h5>
                                        <small class="text-muted">Productores asociados al usuario</small>
                                    </div>
                                    <span class="badge badge-soft badge-primary"><?php echo number_format($totalProductores, 0, ',', '.'); ?> activos</span>
                                </div>
                                <div id="chartProductor" style="height: 260px;"></div>
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Productor</th>
                                                <th class="text-right">CSG</th>
                                                <th class="text-right">Kilos netos</th>
                                                <th class="text-right">Recepciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($porProductor) : ?>
                                                <?php foreach (array_slice($porProductor, 0, 5) as $prod) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($prod['NOMBRE']); ?></td>
                                                        <td class="text-right"><?php echo htmlspecialchars($prod['CSG']); ?></td>
                                                        <td class="text-right"><?php echo number_format($prod['NETO'], 2, ',', '.'); ?> kg</td>
                                                        <td class="text-right"><?php echo number_format($prod['ENVASES'], 0, ',', '.'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else : ?>
                                                <tr><td colspan="4" class="text-center text-muted">Sin datos en el rango seleccionado</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="panel-card p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h5 class="mb-0">Kilos por variedad</h5>
                                        <small class="text-muted">Top variedades por volumen neto</small>
                                    </div>
                                    <span class="badge badge-soft badge-success">Variedades <?php echo number_format($totalVariedades, 0, ',', '.'); ?></span>
                                </div>
                                <div id="chartVariedad" style="height: 220px;"></div>
                            </div>
                            <div class="panel-card p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h5 class="mb-0">Distribución por especie</h5>
                                        <small class="text-muted">Participación porcentual</small>
                                    </div>
                                </div>
                                <div id="chartEspecie" style="height: 220px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-xl-12">
                            <div class="panel-card p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h5 class="mb-0">Envases vs kilos netos</h5>
                                        <small class="text-muted">Nombres de estándar visibles</small>
                                    </div>
                                    <span class="badge badge-soft badge-info">Total envases <?php echo number_format($resumen['envases'], 0, ',', '.'); ?></span>
                                </div>
                                <div id="chartEnvase" style="height: 260px;"></div>
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

        (function generarCharts() {
            c3.generate({
                bindto: '#chartProductor',
                data: {
                    columns: [
                        ['Kilos netos', ...datosProductor.map(p => parseFloat(p.NETO || 0))]
                    ],
                    type: 'bar',
                    colors: { 'Kilos netos': '#2563eb' }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: datosProductor.map(p => `${p.CSG || ''} ${p.NOMBRE || ''}`.trim())
                    },
                    y: { label: 'Kilos netos' }
                },
                bar: { width: { ratio: 0.6 } },
                tooltip: {
                    format: {
                        name: (name, ratio, id, index) => datosProductor[index]?.NOMBRE || name
                    }
                },
                bar: { width: { ratio: 0.7 } }
            });

            c3.generate({
                bindto: '#chartVariedad',
                data: {
                    columns: datosVariedad.map(v => [v.NOMBRE, parseFloat(v.NETO || 0)]),
                    type: 'bar',
                    colors: { ...datosVariedad.reduce((acc, v, i) => ({ ...acc, [v.NOMBRE]: d3.schemeTableau10[i % 10] }), {}) }
                },
                axis: {
                    x: { type: 'category', categories: datosVariedad.map(v => v.NOMBRE), tick: { rotate: 45, multiline: false } },
                    y: { label: 'Kilos netos' }
                },
                bar: { width: { ratio: 0.65 } }
            });

            c3.generate({
                bindto: '#chartEspecie',
                data: {
                    columns: datosEspecie.map(e => [e.NOMBRE, parseFloat(e.NETO || 0)]),
                    type: 'donut',
                    colors: { ...datosEspecie.reduce((acc, e, i) => ({ ...acc, [e.NOMBRE]: d3.schemeSet2[i % 8] }), {}) }
                },
                donut: { title: 'Especies' }
            });

            c3.generate({
                bindto: '#chartEnvase',
                data: {
                    columns: datosEnvase.map(e => [e.NOMBRE, parseFloat(e.NETO || 0)]),
                    type: 'bar',
                    colors: { ...datosEnvase.reduce((acc, e, i) => ({ ...acc, [e.NOMBRE]: d3.schemeCategory10[i % 10] }), {}) }
                },
                axis: {
                    x: { type: 'category', categories: datosEnvase.map(e => e.NOMBRE), tick: { rotate: 35, multiline: false } },
                    y: { label: 'Kilos netos' }
                },
                bar: { width: { ratio: 0.6 } }
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
