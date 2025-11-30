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
$query_acumuladoHastaCincoAm = $CONSULTA_ADO->TotalKgMpRecepcionadoHastaCincoAm($TEMPORADAS, $EMPRESAS, $PLANTAS);

//proceso
$query_totalesProceso = $CONSULTA_ADO->TotalKgProcesoEntradaSalida($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_procesosBajaExportacion = $CONSULTA_ADO->UltimosProcesosBajaExportacionCerrados($TEMPORADAS, $EMPRESAS, $PLANTAS);

//exportación
$query_exportacionProductor = $CONSULTA_ADO->TopExportacionPorProductor($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionVariedad = $CONSULTA_ADO->TopExportacionPorVariedad($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionPais = $CONSULTA_ADO->TopExportacionPorPais($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionRecibidor = $CONSULTA_ADO->TopExportacionPorRecibidor($TEMPORADAS, $EMPRESAS, $PLANTAS);

//existencia materia prima
$query_existenciaVariedad = $CONSULTA_ADO->ExistenciaMateriaPrimaPorVariedad($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_registrosAbiertos = $CONSULTA_ADO->contarRegistrosAbiertosFruta($EMPRESAS, $PLANTAS, $TEMPORADAS);

$kilosMateriaPrimaAcumulado = $query_acumuladoMP ? $query_acumuladoMP[0]["TOTAL"] : 0;
$kilosMateriaPrimaHastaCinco = $query_acumuladoHastaCincoAm ? $query_acumuladoHastaCincoAm[0]["TOTAL"] : 0;
$kilosEntradaProceso = ($query_totalesProceso && isset($query_totalesProceso[0]["ENTRADA"])) ? $query_totalesProceso[0]["ENTRADA"] : 0;
$kilosSalidaProceso = ($query_totalesProceso && isset($query_totalesProceso[0]["SALIDA"])) ? $query_totalesProceso[0]["SALIDA"] : 0;
$recepcionesAbiertas = $query_registrosAbiertos ? $query_registrosAbiertos[0]["RECEPCION"] : 0;
$procesosAbiertos = $query_registrosAbiertos ? $query_registrosAbiertos[0]["PROCESO"] : 0;
$maxExportProd = 0;
$maxExportVariedad = 0;
$maxExportPais = 0;
$maxExportRecibidor = 0;
$maxExistencia = 0;

if ($query_exportacionProductor) {
    foreach ($query_exportacionProductor as $fila) {
        if ($fila["TOTAL"] > $maxExportProd) {
            $maxExportProd = $fila["TOTAL"];
        }
    }
}
if ($query_exportacionVariedad) {
    foreach ($query_exportacionVariedad as $fila) {
        if ($fila["TOTAL"] > $maxExportVariedad) {
            $maxExportVariedad = $fila["TOTAL"];
        }
    }
}
if ($query_existenciaVariedad) {
    foreach ($query_existenciaVariedad as $fila) {
        if ($fila["TOTAL"] > $maxExistencia) {
            $maxExistencia = $fila["TOTAL"];
        }
    }
}
if ($query_exportacionPais) {
    foreach ($query_exportacionPais as $fila) {
        if ($fila["TOTAL"] > $maxExportPais) {
            $maxExportPais = $fila["TOTAL"];
        }
    }
}
if ($query_exportacionRecibidor) {
    foreach ($query_exportacionRecibidor as $fila) {
        if ($fila["TOTAL"] > $maxExportRecibidor) {
            $maxExportRecibidor = $fila["TOTAL"];
        }
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
            .mini-progress { height: 8px; }
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

                        <div class="row">
                            <div class="col-xl-3 col-md-6 col-12">
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

                            <div class="col-xl-3 col-md-6 col-12">
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

                            <div class="col-xl-3 col-md-6 col-12">
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

                            <div class="col-xl-3 col-md-6 col-12">
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

                        <div class="row">
                            <div class="col-xl-4 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Indicadores operacionales</h4>
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
                                        <div class="bg-light p-2 rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">Existencia neta corte 05:00</span>
                                                <span class="badge badge-primary"><?php echo number_format(round($kilosMateriaPrimaHastaCinco, 0), 0, ",", "."); ?> kg</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Procesos cerrados con menor % de exportación</h4>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_procesosBajaExportacion) { ?>
                                            <?php foreach ($query_procesosBajaExportacion as $proceso) {
                                                $porcentajeExpo = number_format($proceso["PDEXPORTACION_PROCESO"], 2, ".", "");
                                                $porcentajeTotal = number_format($proceso["PDEXPORTACIONCD_PROCESO"], 2, ".", "");
                                            ?>
                                                <div class="mb-3 pb-2 border-bottom">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <strong>#<?php echo $proceso["NUMERO_PROCESO"]; ?></strong>
                                                            <div class="text-muted small"><?php echo $proceso["FECHA_PROCESO"]; ?></div>
                                                        </div>
                                                        <span class="badge badge-pill badge-warning">Expo <?php echo $porcentajeExpo; ?>%</span>
                                                    </div>
                                                    <div class="d-flex justify-content-between text-muted small mt-1">
                                                        <span>Entrada: <?php echo number_format($proceso["KILOS_NETO_ENTRADA"], 0, ",", "."); ?> kg</span>
                                                        <span>Exportado: <?php echo number_format($proceso["KILOS_EXPORTACION_PROCESO"], 0, ",", "."); ?> kg</span>
                                                        <span>Total: <?php echo $porcentajeTotal; ?>%</span>
                                                    </div>
                                                    <div class="progress mini-progress mt-2">
                                                        <div class="progress-bar progress-amber" role="progressbar" style="width: <?php echo $proceso["PDEXPORTACION_PROCESO"]; ?>%" aria-valuenow="<?php echo $proceso["PDEXPORTACION_PROCESO"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin procesos cerrados con baja exportación.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Existencia de materia prima por variedad</h4>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_existenciaVariedad) { ?>
                                            <?php foreach ($query_existenciaVariedad as $fila) {
                                                $nombreExi = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                $totalExi = round($fila["TOTAL"], 0);
                                                $porcentajeExi = $maxExistencia > 0 ? ($totalExi / $maxExistencia) * 100 : 0;
                                            ?>
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span><?php echo $nombreExi; ?></span>
                                                        <span><?php echo number_format($totalExi, 0, ",", "."); ?> kg</span>
                                                    </div>
                                                    <div class="progress mini-progress">
                                                        <div class="progress-bar progress-emerald" role="progressbar" style="width: <?php echo $porcentajeExi; ?>%" aria-valuenow="<?php echo $porcentajeExi; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">No hay existencias registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Top 5 exportación por productor</h4>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionProductor) { ?>
                                            <?php foreach ($query_exportacionProductor as $fila) {
                                                $nombreProd = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                $totalProd = round($fila["TOTAL"], 0);
                                                $porcentajeProd = $maxExportProd > 0 ? ($totalProd / $maxExportProd) * 100 : 0;
                                            ?>
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span><?php echo $nombreProd; ?></span>
                                                        <span><?php echo number_format($totalProd, 0, ",", "."); ?> kg</span>
                                                    </div>
                                                    <div class="progress mini-progress">
                                                        <div class="progress-bar progress-sky" role="progressbar" style="width: <?php echo $porcentajeProd; ?>%" aria-valuenow="<?php echo $porcentajeProd; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin exportaciones registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Top 5 exportación por variedad</h4>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionVariedad) { ?>
                                            <?php foreach ($query_exportacionVariedad as $fila) {
                                                $nombreVar = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre";
                                                $totalVar = round($fila["TOTAL"], 0);
                                                $porcentajeVar = $maxExportVariedad > 0 ? ($totalVar / $maxExportVariedad) * 100 : 0;
                                            ?>
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span><?php echo $nombreVar; ?></span>
                                                        <span><?php echo number_format($totalVar, 0, ",", "."); ?> kg</span>
                                                    </div>
                                                    <div class="progress mini-progress">
                                                        <div class="progress-bar progress-dusk" role="progressbar" style="width: <?php echo $porcentajeVar; ?>%" aria-valuenow="<?php echo $porcentajeVar; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin exportaciones registradas.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Kg netos exportados por país</h4>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionPais) { ?>
                                            <?php foreach ($query_exportacionPais as $fila) {
                                                $nombrePais = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin país";
                                                $totalPais = round($fila["TOTAL"], 0);
                                                $porcentajePais = $maxExportPais > 0 ? ($totalPais / $maxExportPais) * 100 : 0;
                                            ?>
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span><?php echo $nombrePais; ?></span>
                                                        <span><?php echo number_format($totalPais, 0, ",", "."); ?> kg</span>
                                                    </div>
                                                    <div class="progress mini-progress">
                                                        <div class="progress-bar progress-ocean" role="progressbar" style="width: <?php echo $porcentajePais; ?>%" aria-valuenow="<?php echo $porcentajePais; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin destinos registrados.</p>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6 col-12">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Kg netos exportados por recibidor</h4>
                                    </div>
                                    <div class="box-body">
                                        <?php if ($query_exportacionRecibidor) { ?>
                                            <?php foreach ($query_exportacionRecibidor as $fila) {
                                                $nombreRecibidor = $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin recibidor";
                                                $totalRecibidor = round($fila["TOTAL"], 0);
                                                $porcentajeRecibidor = $maxExportRecibidor > 0 ? ($totalRecibidor / $maxExportRecibidor) * 100 : 0;
                                            ?>
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span><?php echo $nombreRecibidor; ?></span>
                                                        <span><?php echo number_format($totalRecibidor, 0, ",", "."); ?> kg</span>
                                                    </div>
                                                    <div class="progress mini-progress">
                                                        <div class="progress-bar progress-coral" role="progressbar" style="width: <?php echo $porcentajeRecibidor; ?>%" aria-valuenow="<?php echo $porcentajeRecibidor; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <p class="text-center mb-0">Sin recibidores registrados.</p>
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