<?php
include_once "../../assest/config/validarUsuarioFruta.php";
include_once "../../assest/controlador/EXIMATERIAPRIMA_ADO.php";
include_once "../../assest/controlador/PLANTA_ADO.php";
include_once "../../assest/controlador/ESPECIES_ADO.php";
include_once "../../assest/controlador/VESPECIES_ADO.php";
include_once "../../assest/controlador/ERECEPCION_ADO.php";
include_once "../../assest/controlador/PROCESO_ADO.php";
include_once "../../assest/controlador/TPROCESO_ADO.php";

setlocale(LC_TIME, 'es_ES.UTF-8', 'es_CL.UTF-8', 'es_ES', 'es_CL');

$diasSemanaMap = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo',
];

function normalizarKilos($valor)
{
    $limpio = str_replace([' ', '.'], '', (string)$valor);
    $limpio = str_replace(',', '.', $limpio);
    return floatval($limpio);
}

$EXIMATERIAPRIMA_ADO = new EXIMATERIAPRIMA_ADO();
$PLANTA_ADO = new PLANTA_ADO();
$ESPECIES_ADO = new ESPECIES_ADO();
$VESPECIES_ADO = new VESPECIES_ADO();
$ERECEPCION_ADO = new ERECEPCION_ADO();
$PROCESO_ADO = new PROCESO_ADO();
$TPROCESO_ADO = new TPROCESO_ADO();

$ARRAYTEMPORADA = $TEMPORADA_ADO->listarTemporadaCBX();
$ARRAYESPECIE = array_values(array_filter($ESPECIES_ADO->listarEspeciesCBX(), function ($especie) {
    return isset($especie['ESTADO_REGISTRO']) ? intval($especie['ESTADO_REGISTRO']) === 1 : true;
}));

$especieDefault = '';
$empresaDefault = 'ALL';

$temporadaFiltro = isset($_REQUEST['TEMPORADA_FILTRO']) ? $_REQUEST['TEMPORADA_FILTRO'] : $TEMPORADAS;
$especieFiltro = isset($_REQUEST['ESPECIE_FILTRO']) ? $_REQUEST['ESPECIE_FILTRO'] : $especieDefault;
$empresaFiltro = isset($_REQUEST['EMPRESA_FILTRO']) ? $_REQUEST['EMPRESA_FILTRO'] : $empresaDefault;
$semanaActual = intval(date('W'));

$empresaSeleccionada = $EMPRESA_ADO->verEmpresa($EMPRESAS);
$nombreEmpresa = $empresaSeleccionada ? $empresaSeleccionada[0]['NOMBRE_EMPRESA'] : '';

if (!isset($_SESSION['INFORME_GERENCIAL_PROYECCIONES'])) {
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'] = [];
}

foreach ($_SESSION['INFORME_GERENCIAL_PROYECCIONES'] as &$proyeccionNormalizada) {
    if (!isset($proyeccionNormalizada['ano'])) {
        $proyeccionNormalizada['ano'] = isset($proyeccionNormalizada['creado']) ? intval(date('Y', strtotime($proyeccionNormalizada['creado']))) : intval(date('Y'));
    }
}
unset($proyeccionNormalizada);

$agrupacionPorEstandar = [];
$ARRAYESTANDARES = $ERECEPCION_ADO->listarEstandarCBX();
foreach ($ARRAYESTANDARES as $estandar) {
    if (!isset($estandar['ID_ESTANDAR'])) {
        continue;
    }

    $agrupacionPorEstandar[$estandar['ID_ESTANDAR']] = isset($estandar['ID_AGERENCIAL']) ? intval($estandar['ID_AGERENCIAL']) : null;
}

$mapVespeciesEspecie = [];
$ARRAYVESPECIES = $VESPECIES_ADO->listarVespeciesCBX();
foreach ($ARRAYVESPECIES as $variedad) {
    if (isset($variedad['ID_VESPECIES']) && isset($variedad['ID_ESPECIES'])) {
        $mapVespeciesEspecie[$variedad['ID_VESPECIES']] = $variedad['ID_ESPECIES'];
    }
}

$anoActual = intval(date('o'));
$proyeccionesFiltradas = array_values(array_filter(
    $_SESSION['INFORME_GERENCIAL_PROYECCIONES'],
    function ($proyeccion) use ($temporadaFiltro, $especieFiltro, $semanaActual, $empresaFiltro, $anoActual) {
        $habilitado = !isset($proyeccion['habilitado']) || $proyeccion['habilitado'];
        $especieProyeccion = isset($proyeccion['especie']) ? $proyeccion['especie'] : null;
        $coincideEspecie = ($especieFiltro === '' || !$especieFiltro) ? true : ($especieProyeccion ? intval($especieProyeccion) === intval($especieFiltro) : false);
        $semanaProyeccion = isset($proyeccion['semana']) ? intval($proyeccion['semana']) : null;
        $anoProyeccion = isset($proyeccion['ano']) ? intval($proyeccion['ano']) : $anoActual;
        $dentroSemana = $semanaProyeccion !== null && $semanaProyeccion > 0 && $semanaProyeccion <= $semanaActual;
        $coincideEmpresa = $empresaFiltro === 'ALL' ? true : (isset($proyeccion['empresa']) && intval($proyeccion['empresa']) === intval($empresaFiltro));
        return $habilitado && $coincideEmpresa && $proyeccion['temporada'] == $temporadaFiltro && $coincideEspecie && $dentroSemana && $anoProyeccion > 0;
    }
));

usort($proyeccionesFiltradas, function ($a, $b) {
    $anoA = isset($a['ano']) ? intval($a['ano']) : 0;
    $anoB = isset($b['ano']) ? intval($b['ano']) : 0;
    if ($anoA === $anoB) {
        $semanaA = isset($a['semana']) ? intval($a['semana']) : 0;
        $semanaB = isset($b['semana']) ? intval($b['semana']) : 0;
        return $semanaA <=> $semanaB;
    }
    return $anoA <=> $anoB;
});

$totalProyectado = 0;
$totalReal = 0;
$empresasConDatos = [];
$empresasConDatosSemana = [];
$proyeccionTotalEmpresa = [];
$proyeccionSemanaActualEmpresa = [];
$plantasPorEmpresaSemana = [];
$kilosDiariosPorEmpresaPlanta = [];
$kilosRealesPorEmpresaPlanta = [];
$kilosRealesTotales = [];
$kilosTotalesPorEmpresaPlanta = [];
$kilosBulkPorEmpresaPlanta = [];
$empresasNombres = [];
$plantasNombres = [];
$kilosProcesadosPorPlanta = [];
$totalesProcesadosTipo = [];
$plantasActivas = $PLANTA_ADO->listarPlantaCBX();
$tipoProcesoNombres = [];

$empresasActivasFull = array_values(array_filter($EMPRESA_ADO->listarEmpresaCBX(), function ($empresa) {
    return isset($empresa['ESTADO_REGISTRO']) ? intval($empresa['ESTADO_REGISTRO']) === 1 : true;
}));
foreach ($empresasActivasFull as $empresaActiva) {
    $empresasNombres[$empresaActiva['ID_EMPRESA']] = $empresaActiva['NOMBRE_EMPRESA'];
}

$empresasActivas = array_values(array_filter($empresasActivasFull, function ($empresa) use ($empresaFiltro) {
    return $empresaFiltro === 'ALL' || intval($empresa['ID_EMPRESA']) === intval($empresaFiltro);
}));

foreach ($proyeccionesFiltradas as $proyeccion) {
    $kgProyectado = isset($proyeccion['kg_proyectado']) ? normalizarKilos($proyeccion['kg_proyectado']) : 0;
    $empresaId = isset($proyeccion['empresa']) ? intval($proyeccion['empresa']) : null;
    $esBulk = isset($proyeccion['es_bulk']) ? (bool) $proyeccion['es_bulk'] : false;
    $esSemanaActual = isset($proyeccion['semana']) ? intval($proyeccion['semana']) === $semanaActual : false;
    $anoProyeccion = isset($proyeccion['ano']) ? intval($proyeccion['ano']) : $anoActual;
    $esAnoActual = $anoProyeccion === $anoActual;

    if (!$empresaId || !isset($empresasNombres[$empresaId]) || $kgProyectado <= 0) {
        continue;
    }

    if (!isset($proyeccionTotalEmpresa[$empresaId])) {
        $proyeccionTotalEmpresa[$empresaId] = ['granel' => 0, 'bulk' => 0, 'total' => 0];
    }

    $tipoClave = $esBulk ? 'bulk' : 'granel';
    $proyeccionTotalEmpresa[$empresaId][$tipoClave] += $kgProyectado;
    $proyeccionTotalEmpresa[$empresaId]['total'] += $kgProyectado;

    if ($esSemanaActual && $esAnoActual) {
        if (!isset($proyeccionSemanaActualEmpresa[$empresaId])) {
            $proyeccionSemanaActualEmpresa[$empresaId] = 0;
        }
        $proyeccionSemanaActualEmpresa[$empresaId] += $kgProyectado;
    }
    $totalProyectado += $kgProyectado;
    $empresasConDatos[$empresaId] = true;
}

$inicioSemana = new DateTime();
$inicioSemana->setISODate(intval(date('o')), $semanaActual);
$diasSemanaActual = [];
for ($i = 0; $i < 7; $i++) {
    $dia = clone $inicioSemana;
    $dia->modify("+{$i} day");
    $numeroDia = intval($dia->format('N'));
    $diasSemanaActual[] = [
        'fecha' => $dia->format('Y-m-d'),
        'nombre' => isset($diasSemanaMap[$numeroDia]) ? $diasSemanaMap[$numeroDia] : $dia->format('l'),
    ];
}

foreach ($empresasActivas as $empresaActiva) {
    $empresaId = $empresaActiva['ID_EMPRESA'];
    $existenciasEmpresa = $EXIMATERIAPRIMA_ADO->listarEximateriaprimaEmpresaTemporada($empresaId, $temporadaFiltro);
    foreach ($existenciasEmpresa as $existencia) {
        if (!isset($existencia['ESTADO_REGISTRO']) || intval($existencia['ESTADO_REGISTRO']) !== 1) {
            continue;
        }

        $estadoExistencia = isset($existencia['ESTADO']) ? intval($existencia['ESTADO']) : null;
        if ($estadoExistencia === 0 || $estadoExistencia === 5 || $estadoExistencia === 6) {
            continue;
        }

        $idVespecies = isset($existencia['ID_VESPECIES']) ? $existencia['ID_VESPECIES'] : null;
        $especieAsociada = $idVespecies && isset($mapVespeciesEspecie[$idVespecies]) ? $mapVespeciesEspecie[$idVespecies] : null;
        if (!$especieAsociada && isset($existencia['ID_ESPECIES'])) {
            $especieAsociada = $existencia['ID_ESPECIES'];
        }

        $coincideEspecie = ($especieFiltro === '' || !$especieFiltro || !$especieAsociada) ? true : intval($especieAsociada) === intval($especieFiltro);
        if (!$coincideEspecie) {
            continue;
        }

        $kgReal = isset($existencia['KILOS_NETO_EXIMATERIAPRIMA']) ? normalizarKilos($existencia['KILOS_NETO_EXIMATERIAPRIMA']) : 0;
        $plantaId = isset($existencia['ID_PLANTA']) ? $existencia['ID_PLANTA'] : null;
        $estandarId = isset($existencia['ID_ESTANDAR']) ? $existencia['ID_ESTANDAR'] : null;
        $agrupacion = ($estandarId && isset($agrupacionPorEstandar[$estandarId])) ? $agrupacionPorEstandar[$estandarId] : null;
        $fechaRecepcion = isset($existencia['FECHA_RECEPCION']) ? $existencia['FECHA_RECEPCION'] : null;

        if (!$plantaId || $kgReal <= 0) {
            continue;
        }

        if (!isset($kilosTotalesPorEmpresaPlanta[$empresaId])) {
            $kilosTotalesPorEmpresaPlanta[$empresaId] = [];
        }

        if (!isset($kilosTotalesPorEmpresaPlanta[$empresaId][$plantaId])) {
            $kilosTotalesPorEmpresaPlanta[$empresaId][$plantaId] = 0;
        }

        $kilosTotalesPorEmpresaPlanta[$empresaId][$plantaId] += $kgReal;

        if ($agrupacion === 2) {
            if (!isset($kilosBulkPorEmpresaPlanta[$empresaId])) {
                $kilosBulkPorEmpresaPlanta[$empresaId] = [];
            }

            if (!isset($kilosBulkPorEmpresaPlanta[$empresaId][$plantaId])) {
                $kilosBulkPorEmpresaPlanta[$empresaId][$plantaId] = 0;
            }

            $kilosBulkPorEmpresaPlanta[$empresaId][$plantaId] += $kgReal;
        }

        if (!isset($plantasNombres[$plantaId])) {
            $plantaInfo = $PLANTA_ADO->verPlanta($plantaId);
            $plantasNombres[$plantaId] = $plantaInfo ? $plantaInfo[0]['NOMBRE_PLANTA'] : ('Planta ' . $plantaId);
        }

        if ($fechaRecepcion) {
            $fechaObj = new DateTime($fechaRecepcion);
            $semanaExistencia = intval($fechaObj->format('W'));
            $anoExistencia = intval($fechaObj->format('o'));
            if ($semanaExistencia === $semanaActual && $anoExistencia === intval(date('o'))) {
                $fechaClave = $fechaObj->format('Y-m-d');
                if (!isset($kilosDiariosPorEmpresaPlanta[$empresaId])) {
                    $kilosDiariosPorEmpresaPlanta[$empresaId] = [];
                }
                if (!isset($kilosDiariosPorEmpresaPlanta[$empresaId][$fechaClave])) {
                    $kilosDiariosPorEmpresaPlanta[$empresaId][$fechaClave] = [
                        'plantas' => [],
                        'total' => 0,
                        'bulk' => 0,
                    ];
                }
                if (!isset($kilosDiariosPorEmpresaPlanta[$empresaId][$fechaClave]['plantas'][$plantaId])) {
                    $kilosDiariosPorEmpresaPlanta[$empresaId][$fechaClave]['plantas'][$plantaId] = ['total' => 0, 'bulk' => 0];
                }

                $kilosDiariosPorEmpresaPlanta[$empresaId][$fechaClave]['plantas'][$plantaId]['total'] += $kgReal;
                $kilosDiariosPorEmpresaPlanta[$empresaId][$fechaClave]['total'] += $kgReal;

                if ($agrupacion === 2) {
                    $kilosDiariosPorEmpresaPlanta[$empresaId][$fechaClave]['plantas'][$plantaId]['bulk'] += $kgReal;
                    $kilosDiariosPorEmpresaPlanta[$empresaId][$fechaClave]['bulk'] += $kgReal;
                }

                if (!isset($plantasPorEmpresaSemana[$empresaId])) {
                    $plantasPorEmpresaSemana[$empresaId] = [];
                }
                $plantasPorEmpresaSemana[$empresaId][$plantaId] = true;
                $empresasConDatosSemana[$empresaId] = true;
            }
        }

    }
}

foreach ($kilosTotalesPorEmpresaPlanta as $empresaId => $plantasTotales) {
    foreach ($plantasTotales as $plantaId => $totalKg) {
        $bulkKg = isset($kilosBulkPorEmpresaPlanta[$empresaId][$plantaId]) ? $kilosBulkPorEmpresaPlanta[$empresaId][$plantaId] : 0;
        $granelKg = max($totalKg - $bulkKg, 0);

        if (!isset($kilosRealesPorEmpresaPlanta[$empresaId])) {
            $kilosRealesPorEmpresaPlanta[$empresaId] = [];
        }

        if (!isset($kilosRealesTotales[$empresaId])) {
            $kilosRealesTotales[$empresaId] = ['granel' => 0, 'bulk' => 0, 'total' => 0];
        }

        $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId] = [
            'granel' => $granelKg,
            'bulk' => $bulkKg,
        ];

        $kilosRealesTotales[$empresaId]['granel'] += $granelKg;
        $kilosRealesTotales[$empresaId]['bulk'] += $bulkKg;
        $kilosRealesTotales[$empresaId]['total'] += $totalKg;
        $totalReal += $totalKg;

        $empresasConDatos[$empresaId] = true;
    }
}

foreach ($plantasActivas as $planta) {
    if (!isset($planta['ESTADO_REGISTRO']) || intval($planta['ESTADO_REGISTRO']) !== 1) {
        continue;
    }

    $plantaId = $planta['ID_PLANTA'];
    $empresaPlanta = isset($planta['ID_EMPRESA']) ? $planta['ID_EMPRESA'] : null;

    if (!$empresaPlanta || !isset($empresasNombres[$empresaPlanta])) {
        continue;
    }

    if ($empresaFiltro !== 'ALL' && intval($empresaFiltro) !== intval($empresaPlanta)) {
        continue;
    }

    if (!isset($plantasNombres[$plantaId])) {
        $plantasNombres[$plantaId] = $planta['NOMBRE_PLANTA'];
    }

    $procesosPlanta = $PROCESO_ADO->listarProcesoEmpresaPlantaTemporadaCBX($empresaPlanta, $plantaId, $temporadaFiltro);
    foreach ($procesosPlanta as $proceso) {
        if (!isset($proceso['ESTADO_REGISTRO']) || intval($proceso['ESTADO_REGISTRO']) !== 1) {
            continue;
        }

        if (isset($proceso['ESTADO']) && in_array(intval($proceso['ESTADO']), [0, 5, 6])) {
            continue;
        }

        $idVespeciesProceso = isset($proceso['ID_VESPECIES']) ? $proceso['ID_VESPECIES'] : null;
        $especieProceso = $idVespeciesProceso && isset($mapVespeciesEspecie[$idVespeciesProceso]) ? $mapVespeciesEspecie[$idVespeciesProceso] : null;
        if (!$especieProceso && isset($proceso['ID_ESPECIES'])) {
            $especieProceso = $proceso['ID_ESPECIES'];
        }

        $coincideEspecieProceso = ($especieFiltro === '' || !$especieFiltro || !$especieProceso) ? true : intval($especieProceso) === intval($especieFiltro);
        if (!$coincideEspecieProceso) {
            continue;
        }

        $kgProcesados = isset($proceso['KILOS_NETO_PROCESO']) ? normalizarKilos($proceso['KILOS_NETO_PROCESO']) : (isset($proceso['NETO']) ? normalizarKilos($proceso['NETO']) : 0);
        if ($kgProcesados <= 0) {
            continue;
        }

        $tipoProcesoId = isset($proceso['ID_TPROCESO']) ? $proceso['ID_TPROCESO'] : null;
        if ($tipoProcesoId && !isset($tipoProcesoNombres[$tipoProcesoId])) {
            $tipoProceso = $TPROCESO_ADO->verTproceso($tipoProcesoId);
            $tipoProcesoNombres[$tipoProcesoId] = $tipoProceso ? $tipoProceso[0]['NOMBRE_TPROCESO'] : 'Proceso';
        }
        $nombreTipo = $tipoProcesoId && isset($tipoProcesoNombres[$tipoProcesoId]) ? $tipoProcesoNombres[$tipoProcesoId] : 'Proceso';

        if (!isset($kilosProcesadosPorPlanta[$plantaId])) {
            $kilosProcesadosPorPlanta[$plantaId] = [
                'planta' => $plantasNombres[$plantaId],
                'empresa' => $empresaPlanta,
                'tipos' => [],
                'total' => 0,
            ];
        }

        if (!isset($kilosProcesadosPorPlanta[$plantaId]['tipos'][$nombreTipo])) {
            $kilosProcesadosPorPlanta[$plantaId]['tipos'][$nombreTipo] = 0;
        }

        $kilosProcesadosPorPlanta[$plantaId]['tipos'][$nombreTipo] += $kgProcesados;
        $kilosProcesadosPorPlanta[$plantaId]['total'] += $kgProcesados;

        if (!isset($totalesProcesadosTipo[$nombreTipo])) {
            $totalesProcesadosTipo[$nombreTipo] = 0;
        }
        $totalesProcesadosTipo[$nombreTipo] += $kgProcesados;
    }
}

$empresasReporteIds = array_values(array_filter(array_keys($empresasConDatos), function ($empresaId) use ($empresasNombres) {
    return isset($empresasNombres[$empresaId]);
}));
$plantasReporte = array_keys($plantasNombres);

$empresasSemanaIds = array_values(array_filter(array_keys($empresasConDatosSemana + $proyeccionSemanaActualEmpresa), function ($empresaId) use ($empresasNombres) {
    return isset($empresasNombres[$empresaId]);
}));

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Informe gerencial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Informe gerencial de proyecciones" />
    <meta name="author" content="">
    <?php include_once "../../assest/config/urlHead.php"; ?>
    <style>
        .metric-card {
            border: 0;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            border-radius: 12px;
        }
        .metric-card .box-body { padding: 16px; }
        .tag { padding: 3px 8px; border-radius: 999px; font-size: 11px; }
        .tag-bulk { background: #fff4e5; color: #d9822b; }
        .tag-envasado { background: #e7f6ef; color: #2f855a; }
        .projection-table th, .projection-table td { font-size: 11px; padding: 6px 8px; }
        .section-title { font-weight: 600; font-size: 16px; }
        .badge-soft { padding: 6px 10px; border-radius: 10px; font-size: 12px; background: #f5f7fb; }
        .alert-soft { background: #f3f7ff; border: 1px solid #d4e2ff; color: #2d4b7a; }
        .filter-compact .form-control { font-size: 12px; padding: 6px 8px; }
        .filter-compact button { padding: 6px 12px; }
        .section-highlight { background: #f9e0c7; font-weight: 700; }
    </style>
    <script type="text/javascript">
        function limpiarFiltrosGerencial() {
            var form = document.getElementById('filtroInformeGerencial');
            if (!form) {
                return;
            }
            var especieSelect = form.querySelector('select[name="ESPECIE_FILTRO"]');
            var empresaSelect = form.querySelector('select[name="EMPRESA_FILTRO"]');
            if (especieSelect) {
                especieSelect.value = '<?php echo $especieDefault; ?>';
            }
            if (empresaSelect) {
                empresaSelect.value = '<?php echo $empresaDefault; ?>';
            }
            form.submit();
        }
    </script>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary" >
    <div class="wrapper">
        <?php include_once "../../assest/config/menuFruta.php"; ?>
        <div class="content-wrapper">
            <div class="container-full">
                <section class="content">
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Informe gerencial</h3>
                                <p class="mb-0">Distribución de kilos netos por empresa y planta.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border d-flex flex-wrap align-items-center justify-content-between">
                                    <div>
                                        <h4 class="box-title mb-0">Recepciones acumuladas y cumplimiento de lo proyectado</h4>
                                        <p class="mb-0 text-muted">Distribución por empresa y planta comparando kilos reales vs. proyectados.</p>
                                    </div>
                                    <form method="post" id="filtroInformeGerencial" class="d-flex align-items-end gap-2 filter-compact">
                                        <input type="hidden" name="TEMPORADA_FILTRO" value="<?php echo htmlspecialchars($temporadaFiltro); ?>">
                                        <div class="col-auto p-0">
                                            <label class="mb-1">Especie</label>
                                            <select name="ESPECIE_FILTRO" class="form-control form-control-sm">
                                                <option value="" <?php echo $especieFiltro === '' ? 'selected' : ''; ?>>Todas</option>
                                                <?php foreach ($ARRAYESPECIE as $ESPECIE) { ?>
                                                    <option value="<?php echo $ESPECIE['ID_ESPECIES']; ?>" <?php echo $ESPECIE['ID_ESPECIES'] == $especieFiltro ? 'selected' : ''; ?>>
                                                        <?php echo $ESPECIE['NOMBRE_ESPECIES']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-auto p-0">
                                            <label class="mb-1">Empresa</label>
                                            <select name="EMPRESA_FILTRO" class="form-control form-control-sm">
                                                <option value="ALL" <?php echo $empresaFiltro === 'ALL' ? 'selected' : ''; ?>>Todas</option>
                                                <?php foreach ($empresasNombres as $empresaId => $empresaNombre) { ?>
                                                    <option value="<?php echo $empresaId; ?>" <?php echo $empresaFiltro !== 'ALL' && intval($empresaFiltro) === intval($empresaId) ? 'selected' : ''; ?>>
                                                        <?php echo $empresaNombre; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-auto p-0 d-flex align-items-end">
                                            <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
                                        </div>
                                        <div class="col-auto p-0 d-flex align-items-end ml-1">
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="limpiarFiltrosGerencial()">Limpiar</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="box-body table-responsive">
                                    <?php if ($empresasReporteIds && $plantasReporte) { ?>
                                        <table class="table table-bordered table-sm table-hover projection-table text-center">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2" class="align-middle text-left">Planta</th>
                                                    <?php foreach ($empresasReporteIds as $empresaId) {
                                                        $proyectadoEmpresa = isset($proyeccionTotalEmpresa[$empresaId]['total']) ? $proyeccionTotalEmpresa[$empresaId]['total'] : 0;
                                                        $realEmpresa = isset($kilosRealesTotales[$empresaId]['total']) ? $kilosRealesTotales[$empresaId]['total'] : 0;
                                                        $cumplimientoEmpresa = $proyectadoEmpresa > 0 ? ($realEmpresa / $proyectadoEmpresa) * 100 : 0;
                                                        $cumplimientoColor = $proyectadoEmpresa > 0 ? ($realEmpresa < $proyectadoEmpresa ? '#c53030' : '#2f855a') : '#4a5568';
                                                    ?>
                                                        <th colspan="4">
                                                            <div class="d-flex flex-column align-items-center">
                                                                <span><?php echo htmlspecialchars($empresasNombres[$empresaId]); ?></span>
                                                                <span class="badge-soft" style="color: <?php echo $cumplimientoColor; ?>;">
                                                                    <?php echo round($cumplimientoEmpresa, 1); ?>%
                                                                </span>
                                                            </div>
                                                        </th>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <?php foreach ($empresasReporteIds as $empresaId) { ?>
                                                        <th>Real granel</th>
                                                        <th>Real bulk</th>
                                                        <th>Proy. granel</th>
                                                        <th>Proy. bulk</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($plantasReporte as $plantaId) { ?>
                                                    <tr>
                                                        <td class="text-left"><?php echo htmlspecialchars($plantasNombres[$plantaId]); ?></td>
                                                        <?php foreach ($empresasReporteIds as $empresaId) {
                                                            $realPlantaGranel = isset($kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['granel']) ? $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['granel'] : 0;
                                                            $realPlantaBulk = isset($kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['bulk']) ? $kilosRealesPorEmpresaPlanta[$empresaId][$plantaId]['bulk'] : 0;
                                                            $proyectadoEmpresaGranel = isset($proyeccionTotalEmpresa[$empresaId]['granel']) ? $proyeccionTotalEmpresa[$empresaId]['granel'] : 0;
                                                            $proyectadoEmpresaBulk = isset($proyeccionTotalEmpresa[$empresaId]['bulk']) ? $proyeccionTotalEmpresa[$empresaId]['bulk'] : 0;
                                                        ?>
                                                            <td class="text-right"><?php echo $realPlantaGranel ? number_format($realPlantaGranel, 0, ',', '.') : '-'; ?></td>
                                                            <td class="text-right"><?php echo $realPlantaBulk ? number_format($realPlantaBulk, 0, ',', '.') : '-'; ?></td>
                                                            <td class="text-right text-muted"><?php echo $proyectadoEmpresaGranel ? number_format($proyectadoEmpresaGranel, 0, ',', '.') : '-'; ?></td>
                                                            <td class="text-right text-muted"><?php echo $proyectadoEmpresaBulk ? number_format($proyectadoEmpresaBulk, 0, ',', '.') : '-'; ?></td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                                <tr class="font-weight-600">
                                                    <td class="text-left">Subtotal</td>
                                                    <?php foreach ($empresasReporteIds as $empresaId) {
                                                        $proyectadoEmpresaGranel = isset($proyeccionTotalEmpresa[$empresaId]['granel']) ? $proyeccionTotalEmpresa[$empresaId]['granel'] : 0;
                                                        $proyectadoEmpresaBulk = isset($proyeccionTotalEmpresa[$empresaId]['bulk']) ? $proyeccionTotalEmpresa[$empresaId]['bulk'] : 0;
                                                        $realEmpresaGranel = isset($kilosRealesTotales[$empresaId]['granel']) ? $kilosRealesTotales[$empresaId]['granel'] : 0;
                                                        $realEmpresaBulk = isset($kilosRealesTotales[$empresaId]['bulk']) ? $kilosRealesTotales[$empresaId]['bulk'] : 0;
                                                    ?>
                                                        <td class="text-right"><?php echo $realEmpresaGranel ? number_format($realEmpresaGranel, 0, ',', '.') : '-'; ?></td>
                                                        <td class="text-right"><?php echo $realEmpresaBulk ? number_format($realEmpresaBulk, 0, ',', '.') : '-'; ?></td>
                                                        <td class="text-right"><?php echo $proyectadoEmpresaGranel ? number_format($proyectadoEmpresaGranel, 0, ',', '.') : '-'; ?></td>
                                                        <td class="text-right"><?php echo $proyectadoEmpresaBulk ? number_format($proyectadoEmpresaBulk, 0, ',', '.') : '-'; ?></td>
                                                    <?php } ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <div class="alert alert-soft mb-0">No hay datos de recepciones o proyecciones para construir el resumen por empresa.</div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </section>
                <section class="content pt-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border d-flex flex-wrap align-items-center justify-content-between">
                                    <div>
                                        <h4 class="box-title mb-0">Acumulado temporada actual</h4>
                                        <p class="mb-0 text-muted">Kilos procesados por planta y tipo de proceso.</p>
                                    </div>
                                </div>
                                <div class="box-body table-responsive">
                                    <?php if ($kilosProcesadosPorPlanta) { ?>
                                        <table class="table table-bordered table-sm projection-table text-center">
                                            <thead>
                                                <tr class="section-highlight">
                                                    <th class="text-left">Packing</th>
                                                    <th class="text-right">Kg procesados</th>
                                                    <th>Tipo de proceso</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($kilosProcesadosPorPlanta as $plantaId => $datosPlanta) { ?>
                                                    <?php foreach ($datosPlanta['tipos'] as $nombreTipo => $kgTipo) { ?>
                                                        <tr>
                                                            <td class="text-left"><?php echo htmlspecialchars($datosPlanta['planta']); ?></td>
                                                            <td class="text-right"><?php echo number_format($kgTipo, 0, ',', '.'); ?></td>
                                                            <td><?php echo htmlspecialchars($nombreTipo); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                                <tr class="font-weight-600">
                                                    <td class="text-left">Totales</td>
                                                    <td class="text-right"><?php echo number_format(array_sum($totalesProcesadosTipo), 0, ',', '.'); ?></td>
                                                    <td>
                                                        <?php foreach ($totalesProcesadosTipo as $nombreTipo => $kgTipo) { ?>
                                                            <span class="d-inline-block mr-2"><?php echo htmlspecialchars($nombreTipo) . ': ' . number_format($kgTipo, 0, ',', '.'); ?></span>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <div class="alert alert-soft mb-0">No hay procesos registrados con los filtros seleccionados.</div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="box metric-card">
                                <div class="box-header with-border d-flex flex-wrap align-items-center justify-content-between">
                                    <div>
                                        <h4 class="box-title mb-0">Recepción diaria por planta</h4>
                                        <p class="mb-0 text-muted">Semana <?php echo $semanaActual; ?> - proyección semanal dividida en 7 días.</p>
                                    </div>
                                </div>
                                <div class="box-body table-responsive">
                                    <?php if ($empresasSemanaIds) { ?>
                                        <div class="row">
                                            <?php foreach ($empresasSemanaIds as $empresaId) {
                                                $proyeccionDiaria = isset($proyeccionSemanaActualEmpresa[$empresaId]) ? $proyeccionSemanaActualEmpresa[$empresaId] / 7 : 0;
                                                $tieneDatos = $proyeccionDiaria > 0 || isset($kilosDiariosPorEmpresaPlanta[$empresaId]);
                                                if (!$tieneDatos) { continue; }
                                            ?>
                                                <div class="col-lg-6 col-12">
                                                    <h5 class="mt-0 mb-2 font-weight-600"><?php echo htmlspecialchars($empresasNombres[$empresaId]); ?></h5>
                                                    <table class="table table-bordered table-sm table-hover projection-table text-center mb-4">
                                                        <thead>
                                                            <tr>
                                                                <th class="align-middle text-left" style="min-width:140px;">Fecha</th>
                                                                <th class="text-right" style="min-width:110px;">Real</th>
                                                                <th class="text-right" style="min-width:120px;">Proyectado diario</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($diasSemanaActual as $diaSemana) {
                                                                $fechaDia = $diaSemana['fecha'];
                                                                $nombreDia = $diaSemana['nombre'];
                                                                $realDiaTotal = isset($kilosDiariosPorEmpresaPlanta[$empresaId][$fechaDia]['total']) ? $kilosDiariosPorEmpresaPlanta[$empresaId][$fechaDia]['total'] : 0;
                                                                $cumplimientoDia = $proyeccionDiaria > 0 ? ($realDiaTotal / $proyeccionDiaria) * 100 : 0;
                                                                $colorCumplimiento = $proyeccionDiaria > 0 ? ($realDiaTotal < $proyeccionDiaria ? '#c53030' : '#2f855a') : '#4a5568';
                                                            ?>
                                                                <tr>
                                                                    <td class="text-left"><?php echo $nombreDia . ' ' . date('d-m', strtotime($fechaDia)); ?></td>
                                                                    <td class="text-right" style="color: <?php echo $colorCumplimiento; ?>;">
                                                                        <?php echo $realDiaTotal ? number_format($realDiaTotal, 0, ',', '.') : 'Sin recep'; ?>
                                                                        <?php if ($proyeccionDiaria) { ?>
                                                                            <div class="small mb-0 text-muted" style="color: <?php echo $colorCumplimiento; ?> !important;">
                                                                                <?php echo round($cumplimientoDia, 1); ?>%
                                                                            </div>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td class="text-right text-muted"><?php echo $proyeccionDiaria ? number_format($proyeccionDiaria, 0, ',', '.') : '-'; ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="alert alert-soft mb-0">No hay datos para la semana actual con los filtros seleccionados.</div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <?php include_once "../../assest/config/urlBase.php"; ?>
</body>
</html>
