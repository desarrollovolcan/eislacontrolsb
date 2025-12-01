<?php
include_once "../../assest/config/validarUsuarioFruta.php";



//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
include_once "../../assest/controlador/CONSULTA_ADO.php";


//INICIALIZAR CONTROLADOR
$CONSULTA_ADO =  NEW CONSULTA_ADO;

//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD

$query_datosPlanta = $CONSULTA_ADO->verPlanta($PLANTAS);

//acumulados materia prima
$query_acumuladoMP = $CONSULTA_ADO->TotalKgMpRecepcionadoAcumulado($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_existenciaActual = $CONSULTA_ADO->TotalExistenciaMateriaPrimaActual($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_recepcionDiaActual = $CONSULTA_ADO->TotalKgMpRecepcionadoDiaActual($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_despachoDiaActual = $CONSULTA_ADO->TotalKgDespachoMpDiaActual($TEMPORADAS, $EMPRESAS, $PLANTAS);

//proceso
$query_totalesProceso = $CONSULTA_ADO->TotalKgProcesoEntradaSalida($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_totalesProcesoDiaActual = $CONSULTA_ADO->TotalKgProcesoDiaActual($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_procesosBajaExportacion = $CONSULTA_ADO->UltimosProcesosBajaExportacionCerrados($TEMPORADAS, $EMPRESAS, $PLANTAS);

//exportación
$query_exportacionProductor = $CONSULTA_ADO->TopExportacionPorProductor($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionVariedad = $CONSULTA_ADO->TopExportacionPorVariedad($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionPais = $CONSULTA_ADO->TopExportacionPorPais($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionRecibidor = $CONSULTA_ADO->TopExportacionPorRecibidor($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_cajasPorPais = $CONSULTA_ADO->CajasAprobadasPorPais($TEMPORADAS, $EMPRESAS, $PLANTAS);

//existencia materia prima
$query_existenciaVariedad = $CONSULTA_ADO->ExistenciaMateriaPrimaPorVariedad($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_registrosAbiertos = $CONSULTA_ADO->contarRegistrosAbiertosFruta($EMPRESAS, $PLANTAS, $TEMPORADAS);

$kilosMateriaPrimaAcumulado = $query_acumuladoMP ? $query_acumuladoMP[0]["TOTAL"] : 0;
$kilosMateriaPrimaActual = $query_existenciaActual ? $query_existenciaActual[0]["TOTAL"] : 0;
$kilosRecepcionDiaActual = $query_recepcionDiaActual ? $query_recepcionDiaActual[0]["TOTAL"] : 0;
$kilosProcesoDiaActual = $query_totalesProcesoDiaActual ? $query_totalesProcesoDiaActual[0]["TOTAL"] : 0;
$kilosDespachoDiaActual = $query_despachoDiaActual ? $query_despachoDiaActual[0]["TOTAL"] : 0;
$kilosMateriaPrimaHastaCinco = $kilosMateriaPrimaActual + $kilosProcesoDiaActual + $kilosDespachoDiaActual - $kilosRecepcionDiaActual;
$kilosEntradaProceso = ($query_totalesProceso && isset($query_totalesProceso[0]["ENTRADA"])) ? $query_totalesProceso[0]["ENTRADA"] : 0;
$kilosSalidaProceso = ($query_totalesProceso && isset($query_totalesProceso[0]["SALIDA"])) ? $query_totalesProceso[0]["SALIDA"] : 0;
$recepcionesAbiertas = $query_registrosAbiertos ? $query_registrosAbiertos[0]["RECEPCION"] : 0;
$procesosAbiertos = $query_registrosAbiertos ? $query_registrosAbiertos[0]["PROCESO"] : 0;
$maxExportProd = 0;
$maxExportVariedad = 0;
$maxExportPais = 0;
$maxExportRecibidor = 0;
$maxExistencia = 0;
$maxCajasPais = 0;
$totalExportProd = 0;
$totalExportVariedad = 0;
$totalExportPais = 0;
$totalExportRecibidor = 0;
$totalExistencia = 0;
$totalCajasAprobadas = 0;
$totalEntradaProcesosCerrados = 0;
$totalExportProcesosCerrados = 0;

if ($query_exportacionProductor) {
    foreach ($query_exportacionProductor as $fila) {
        if ($fila["TOTAL"] > $maxExportProd) {
            $maxExportProd = $fila["TOTAL"];
        }
        $totalExportProd += $fila["TOTAL"];
    }
}
if ($query_exportacionVariedad) {
    foreach ($query_exportacionVariedad as $fila) {
        if ($fila["TOTAL"] > $maxExportVariedad) {
            $maxExportVariedad = $fila["TOTAL"];
        }
        $totalExportVariedad += $fila["TOTAL"];
    }
}
if ($query_existenciaVariedad) {
    foreach ($query_existenciaVariedad as $fila) {
        if ($fila["TOTAL"] > $maxExistencia) {
            $maxExistencia = $fila["TOTAL"];
        }
        $totalExistencia += $fila["TOTAL"];
    }
}
if ($query_exportacionPais) {
    foreach ($query_exportacionPais as $fila) {
        if ($fila["TOTAL"] > $maxExportPais) {
            $maxExportPais = $fila["TOTAL"];
        }
        $totalExportPais += $fila["TOTAL"];
    }
}
if ($query_exportacionRecibidor) {
    foreach ($query_exportacionRecibidor as $fila) {
        if ($fila["TOTAL"] > $maxExportRecibidor) {
            $maxExportRecibidor = $fila["TOTAL"];
        }
        $totalExportRecibidor += $fila["TOTAL"];
    }
}

if ($query_cajasPorPais) {
    foreach ($query_cajasPorPais as $fila) {
        if ($fila["TOTAL"] > $maxCajasPais) {
            $maxCajasPais = $fila["TOTAL"];
        }
        $totalCajasAprobadas += $fila["TOTAL"];
    }
}

if ($query_procesosBajaExportacion) {
    foreach ($query_procesosBajaExportacion as $procesoTotal) {
        $totalEntradaProcesosCerrados += $procesoTotal["KILOS_NETO_ENTRADA"];
        $totalExportProcesosCerrados += $procesoTotal["KILOS_EXPORTACION_PROCESO"];
    }
}

if ($query_datosPlanta) {
    $nombePlanta = $query_datosPlanta[0]['NOMBRE_PLANTA'];
}






/*$RECEPCION=0;
$RECEPCIONMP=0;
$RECEPCIONIND=0;
$RECEPCIONPT=0;
$DESPACHO=0;
$PROCESO=0;
$REEMBALAJE=0;
$REPALETIZAJE=0;

//INICIALIZAR ARREGLOS
$ARRAYREGISTROSABIERTOS="";
$ARRAYAVISOS1=$AVISO_ADO->listarAvisoActivosCBX();
//$ARRAYAVISOS2=$AVISO_ADO->listarAvisoActivosFijoCBX();



//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS CONTROLADORES
$ARRAYREGISTROSABIERTOS=$CONSULTA_ADO->contarRegistrosAbiertosFruta($EMPRESAS,$PLANTAS,$TEMPORADAS);
if($ARRAYREGISTROSABIERTOS){
    $RECEPCION=$ARRAYREGISTROSABIERTOS[0]["RECEPCION"];
    $RECEPCIONMP=$ARRAYREGISTROSABIERTOS[0]["RECEPCIONMP"];
    $RECEPCIONIND=$ARRAYREGISTROSABIERTOS[0]["RECEPCIONIND"];
    $RECEPCIONPT=$ARRAYREGISTROSABIERTOS[0]["RECEPCIONPT"];
    $DESPACHO=$ARRAYREGISTROSABIERTOS[0]["DESPACHO"];
    $DESPACHOMP=$ARRAYREGISTROSABIERTOS[0]["DESPACHOMP"];
    $DESPACHOIND=$ARRAYREGISTROSABIERTOS[0]["DESPACHOIND"];
    $DESPACHOPT=$ARRAYREGISTROSABIERTOS[0]["DESPACHOPT"];
    $DESPACHOEXPO=$ARRAYREGISTROSABIERTOS[0]["DESPACHOEXPO"];
    $PROCESO=$ARRAYREGISTROSABIERTOS[0]["PROCESO"];
    $REEMBALAJE=$ARRAYREGISTROSABIERTOS[0]["REEMBALAJE"];
    $REPALETIZAJE=$ARRAYREGISTROSABIERTOS[0]["REPALETIZAJE"];
}*/


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <title>INICIO</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
        <!- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -!>
        <?php include_once "../../assest/config/urlHead.php"; ?>
        <style>
            .dashboard-card {
                color: #fff;
                border: 0;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
                height: 100%;
            }
            .compact-card .box-body {
                padding: 12px;
            }
            .compact-card .box-header {
                padding: 10px 12px;
            }
            .dashboard-row {
                margin-bottom: 10px;
            }
            .collage-row {
                margin-left: -8px;
                margin-right: -8px;
            }
            .collage-row > [class*='col-'] {
                padding-left: 8px;
                padding-right: 8px;
            }
            .collage-card {
                height: 100%;
            }
            @media (min-width: 1200px) {
                .col-xl-5th {
                    flex: 0 0 20%;
                    max-width: 20%;
                }
            }
            .row .col-xl-5th {
                display: flex;
            }
            .bg-gradient-sky { background: linear-gradient(135deg, #1d8cf8 0%, #5ac8fa 100%); }
            .bg-gradient-dusk { background: linear-gradient(135deg, #7b42f6 0%, #b06ab3 100%); }
            .bg-gradient-emerald { background: linear-gradient(135deg, #2ecc71 0%, #58d68d 100%); }
            .bg-gradient-amber { background: linear-gradient(135deg, #f5a623 0%, #f7c46c 100%); }
            .bg-gradient-teal { background: linear-gradient(135deg, #00a6a4 0%, #39c6c9 100%); }
            .progress-sky { background-color: #1d8cf8; }
            .progress-dusk { background-color: #7b42f6; }
            .progress-emerald { background-color: #2ecc71; }
            .progress-amber { background-color: #f5a623; }
            .progress-ocean { background: linear-gradient(135deg, #00b4d8 0%, #0077b6 100%); }
            .progress-coral { background: linear-gradient(135deg, #ff7e5f 0%, #feb47b 100%); }
            .mini-progress { height: 6px; }
            .mini-progress.super-thin { height: 4px; }
            .compact-list .item {
                padding: 6px 0;
                border-bottom: 1px dashed #e6e6e6;
            }
            .compact-list .item:last-child {
                border-bottom: none;
            }
            .compact-table th, .compact-table td {
                padding: 6px 4px;
                font-size: 12px;
                vertical-align: middle;
            }
            .compact-table th { font-weight: 600; }
            .badge-slim { padding: 2px 6px; font-size: 11px; }
            section.content {
                padding-top: 10px;
            }
        </style>
        <!- FUNCIONES BASES -!>
        <script type="text/javascript">
            //REDIRECCIONAR A LA PAGINA SELECIONADA
            function irPagina(url) {
                location.href = "" + url;
            }
        </script>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary" >
    <div class="wrapper">
        <!- LLAMADA AL MENU PRINCIPAL DE LA PAGINA-!>
            <?php include_once "../../assest/config/menuFruta.php"; ?>
            <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -!>
            <div class="content-wrapper">
                <div class="container-full">
                    <section class="content">
                        <div class="content-header">
                            <div class="d-flex align-items-center">
                                <div class="mr-auto">
                                    <h3 class="page-title">Dashboard planta <?php echo isset($nombePlanta) ? strtoupper($nombePlanta) : ""; ?></h3>
                                    <p class="mb-0">Datos filtrados por empresa, temporada y planta activa.</p>
                                </div>
                                <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box box-body dashboard-card bg-gradient-sky">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0 text-white-50">Kilos netos materia prima acumulados</p>
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format(round($kilosMateriaPrimaAcumulado, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Add-cart fs-40 text-white"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box box-body dashboard-card bg-gradient-dusk">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0 text-white-50">Existencia neta (corte 05:00)</p>
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format(round($kilosMateriaPrimaHastaCinco, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Alarm-clock fs-40 text-white"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box box-body dashboard-card bg-gradient-teal">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0 text-white-50">Existencia neta en tiempo real</p>
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format(round($kilosMateriaPrimaActual, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Network fs-40 text-white"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box box-body dashboard-card bg-gradient-emerald">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0 text-white-50">Proceso - kilos netos entrada</p>
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format(round($kilosEntradaProceso, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Incoming-mail fs-40 text-white"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5th col-lg-6 col-12">
                                <div class="box box-body dashboard-card bg-gradient-amber">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0 text-white-50">Proceso - kilos netos salida</p>
                                            <h3 class="mt-0 mb-0 text-white"><?php echo number_format(round($kilosSalidaProceso, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Outcoming-mail fs-40 text-white"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row collage-row align-items-stretch">
                            <div class="col-xl-4 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Indicadores operacionales</h4>
                                            <span class="badge badge-outline badge-info">Procesos / recepción</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-pill badge-info mr-2"><i class="icon-Notes"></i></span>
                                            <div>
                                                <div class="text-muted small">Recepciones abiertas</div>
                                                <div class="h5 mb-0"><?php echo intval($recepcionesAbiertas); ?></div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="badge badge-pill badge-success mr-2"><i class="icon-Gear"></i></span>
                                            <div>
                                                <div class="text-muted small">Procesos abiertos</div>
                                                <div class="h5 mb-0"><?php echo intval($procesosAbiertos); ?></div>
                                            </div>
                                        </div>
                                        <div class="bg-light p-2 rounded mb-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Existencia neta corte 05:00</span>
                                                <span class="badge badge-primary"><?php echo number_format(round($kilosMateriaPrimaHastaCinco, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                        </div>
                                        <div class="bg-light p-2 rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Existencia neta al momento</span>
                                                <span class="badge badge-info"><?php echo number_format(round($kilosMateriaPrimaActual, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Existencia de materia prima por variedad</h4>
                                            <span class="badge badge-outline badge-success">Materia prima</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_existenciaVariedad) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total variedades</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExistencia, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Variedad</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_existenciaVariedad as $fila) {
                                                            $nombreExi = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                            $totalExi = round($fila["TOTAL"], 0);
                                                            $porcentajeExi = $maxExistencia > 0 ? ($totalExi / $maxExistencia) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreExi; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalExi, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="progress mini-progress super-thin">
                                                                        <div class="progress-bar progress-emerald" role="progressbar" style="width: <?php echo $porcentajeExi; ?>%" aria-valuenow="<?php echo $porcentajeExi; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">No hay existencias registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Top 5 exportación por productor</h4>
                                            <span class="badge badge-outline badge-warning">Proceso</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionProductor) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 productores</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExportProd, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Productor</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_exportacionProductor as $fila) {
                                                            $nombreProd = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                            $totalProd = round($fila["TOTAL"], 0);
                                                            $porcentajeProd = $maxExportProd > 0 ? ($totalProd / $maxExportProd) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreProd; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalProd, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="progress mini-progress super-thin">
                                                                        <div class="progress-bar progress-sky" role="progressbar" style="width: <?php echo $porcentajeProd; ?>%" aria-valuenow="<?php echo $porcentajeProd; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5). Origen: procesos.</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin exportaciones registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row collage-row align-items-stretch">
                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Top 5 exportación por variedad</h4>
                                            <span class="badge badge-outline badge-warning">Proceso</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionVariedad) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 variedades</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExportVariedad, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Variedad</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_exportacionVariedad as $fila) {
                                                            $nombreVar = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                            $totalVar = round($fila["TOTAL"], 0);
                                                            $porcentajeVar = $maxExportVariedad > 0 ? ($totalVar / $maxExportVariedad) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreVar; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalVar, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="progress mini-progress super-thin">
                                                                        <div class="progress-bar progress-dusk" role="progressbar" style="width: <?php echo $porcentajeVar; ?>%" aria-valuenow="<?php echo $porcentajeVar; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5). Origen: procesos.</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin exportaciones registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Kg netos exportados por país</h4>
                                            <span class="badge badge-outline badge-primary">Exportación</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionPais) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 países</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExportPais, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>País</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_exportacionPais as $fila) {
                                                            $nombrePais = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin país";
                                                            $totalPais = round($fila["TOTAL"], 0);
                                                            $porcentajePais = $maxExportPais > 0 ? ($totalPais / $maxExportPais) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombrePais; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalPais, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="progress mini-progress super-thin">
                                                                        <div class="progress-bar progress-ocean" role="progressbar" style="width: <?php echo $porcentajePais; ?>%" aria-valuenow="<?php echo $porcentajePais; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5).</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin destinos registrados.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Kg netos exportados por recibidor</h4>
                                            <span class="badge badge-outline badge-primary">Exportación</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionRecibidor) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 recibidores</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalExportRecibidor, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Recibidor</th>
                                                            <th class="text-right">Kg netos</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_exportacionRecibidor as $fila) {
                                                            $nombreRecibidor = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin recibidor";
                                                            $totalRecibidor = round($fila["TOTAL"], 0);
                                                            $porcentajeRecibidor = $maxExportRecibidor > 0 ? ($totalRecibidor / $maxExportRecibidor) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreRecibidor; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalRecibidor, 0, ",", "."); ?> kg</td>
                                                                <td class="text-right">
                                                                    <div class="progress mini-progress super-thin">
                                                                        <div class="progress-bar progress-coral" role="progressbar" style="width: <?php echo $porcentajeRecibidor; ?>%" aria-valuenow="<?php echo $porcentajeRecibidor; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5).</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin recibidores registrados.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box compact-card collage-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Cajas aprobadas por país</h4>
                                            <span class="badge badge-outline badge-info">Inspección</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_cajasPorPais) { ?>
                                            <div class="d-flex justify-content-between align-items-center text-muted small mb-1">
                                                <span>Total top 5 países</span>
                                                <span class="badge badge-secondary"><?php echo number_format(round($totalCajasAprobadas, 0), 0, ",", "."); ?> cajas</span>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th>País</th>
                                                            <th class="text-right">Cajas</th>
                                                            <th class="text-right">Avance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_cajasPorPais as $fila) {
                                                            $nombreCajaPais = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin país";
                                                            $totalCajas = round($fila["TOTAL"], 0);
                                                            $porcentajeCajaPais = $maxCajasPais > 0 ? ($totalCajas / $maxCajasPais) * 100 : 0;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $nombreCajaPais; ?></td>
                                                                <td class="text-right"><?php echo number_format($totalCajas, 0, ",", "."); ?></td>
                                                                <td class="text-right">
                                                                    <div class="progress mini-progress super-thin">
                                                                        <div class="progress-bar progress-coral" role="progressbar" style="width: <?php echo $porcentajeCajaPais; ?>%" aria-valuenow="<?php echo $porcentajeCajaPais; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Totales consideran únicamente los registros mostrados (top 5).</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin cajas aprobadas registradas para país 1.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row dashboard-row">
                            <div class="col-12">
                                <div class="box compact-card">
                                    <div class="box-header with-border">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h4 class="box-title mb-0">Procesos cerrados con menor % de exportación</h4>
                                            <span class="badge badge-outline badge-warning">Proceso</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_procesosBajaExportacion) { ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0 compact-table">
                                                    <thead>
                                                        <tr>
                                                            <th># / Fecha</th>
                                                            <th class="text-right">Ent. (kg)</th>
                                                            <th class="text-right">Expo (kg)</th>
                                                            <th class="text-right">Expo %</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($query_procesosBajaExportacion as $proceso) {
                                                            $porcentajeExpo = number_format($proceso["PDEXPORTACION_PROCESO"], 2, ".", "");
                                                            $porcentajeTotal = number_format($proceso["PDEXPORTACIONCD_PROCESO"], 2, ".", "");
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="font-weight-600">#<?php echo $proceso["NUMERO_PROCESO"]; ?></div>
                                                                    <div class="text-muted small"><?php echo $proceso["FECHA_PROCESO"]; ?></div>
                                                                    <div class="progress mini-progress super-thin mt-1">
                                                                        <div class="progress-bar progress-amber" role="progressbar" style="width: <?php echo $proceso["PDEXPORTACION_PROCESO"]; ?>%" aria-valuenow="<?php echo $proceso["PDEXPORTACION_PROCESO"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-right align-middle"><?php echo number_format($proceso["KILOS_NETO_ENTRADA"], 0, ",", "."); ?></td>
                                                                <td class="text-right align-middle"><?php echo number_format($proceso["KILOS_EXPORTACION_PROCESO"], 0, ",", "."); ?></td>
                                                                <td class="text-right align-middle">
                                                                    <span class="badge badge-warning-light badge-slim">Expo <?php echo $porcentajeExpo; ?>%</span>
                                                                    <div class="text-muted small">Total <?php echo $porcentajeTotal; ?>%</div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th>Total procesos listados</th>
                                                            <th class="text-right"><?php echo number_format(round($totalEntradaProcesosCerrados, 0), 0, ",", "."); ?> kg</th>
                                                            <th class="text-right"><?php echo number_format(round($totalExportProcesosCerrados, 0), 0, ",", "."); ?> kg</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <p class="text-muted small mt-1 mb-0">Datos provenientes de procesos cerrados (kilos suman lo mostrado en la tabla).</p>
                                            </div>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin procesos cerrados con baja exportación.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- /.content -->
                </div>
            </div>

            <?php include_once "../../assest/config/footer.php"; ?>
            <?php include_once "../../assest/config/menuExtraFruta.php"; ?>
    </div>
    <!- LLAMADA URL DE ARCHIVOS DE DISEÑO Y JQUERY E OTROS -!>
        <?php include_once "../../assest/config/urlBase.php"; ?>
        <!--<script>
    Morris.Bar({
        element: 'graficofrigorifico',
        data: [{
            y: 'Angus',
            a: 17600,
            b: 9500
        }, {
            y: 'BBCH',
            a: 8000,
            b: 7000
        }, {
            y: 'Greenvic',
            a: 550,
            b: 4500
        }, {
            y: 'Volcan Foods',
            a: 800,
            b: 450
        }, {
            y: 'LLF',
            a: 55000,
            b: 45000
        }],
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['D. Exportación', 'D. Interplanta'],
        barColors:['#ff3f3f', '#0080ff'],
        hideHover: 'auto',
        gridLineColor: '#eef0f2',
        resize: true
    });
            </script>
-->
</body>
</html>