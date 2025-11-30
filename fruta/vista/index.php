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

//exportación
$query_exportacionProductor = $CONSULTA_ADO->TopExportacionPorProductor($TEMPORADAS, $EMPRESAS, $PLANTAS);
$query_exportacionVariedad = $CONSULTA_ADO->TopExportacionPorVariedad($TEMPORADAS, $EMPRESAS, $PLANTAS);

//existencia materia prima
$query_existenciaVariedad = $CONSULTA_ADO->ExistenciaMateriaPrimaPorVariedad($TEMPORADAS, $EMPRESAS, $PLANTAS);

$kilosMateriaPrimaAcumulado = $query_acumuladoMP ? $query_acumuladoMP[0]["TOTAL"] : 0;
$kilosMateriaPrimaHastaCinco = $query_acumuladoHastaCincoAm ? $query_acumuladoHastaCincoAm[0]["TOTAL"] : 0;
$kilosEntradaProceso = ($query_totalesProceso && isset($query_totalesProceso[0]["ENTRADA"])) ? $query_totalesProceso[0]["ENTRADA"] : 0;
$kilosSalidaProceso = ($query_totalesProceso && isset($query_totalesProceso[0]["SALIDA"])) ? $query_totalesProceso[0]["SALIDA"] : 0;

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
                                <div class="box box-body bg-primary-light">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0">Kilos netos materia prima acumulados</p>
                                            <h3 class="mt-0 mb-0 text-primary"><?php echo number_format(round($kilosMateriaPrimaAcumulado, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Add-cart fs-40 text-primary"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box box-body bg-info-light">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0">Kilos netos hasta las 05:00</p>
                                            <h3 class="mt-0 mb-0 text-info"><?php echo number_format(round($kilosMateriaPrimaHastaCinco, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Alarm-clock fs-40 text-info"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box box-body bg-success-light">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0">Proceso - kilos netos entrada</p>
                                            <h3 class="mt-0 mb-0 text-success"><?php echo number_format(round($kilosEntradaProceso, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Incoming-mail fs-40 text-success"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-md-6 col-12">
                                <div class="box box-body bg-warning-light">
                                    <div class="flexbox align-items-center">
                                        <div>
                                            <p class="mb-0">Proceso - kilos netos salida</p>
                                            <h3 class="mt-0 mb-0 text-warning"><?php echo number_format(round($kilosSalidaProceso, 0), 0, ",", "."); ?> kg</h3>
                                        </div>
                                        <span class="icon-Outcoming-mail fs-40 text-warning"></span>
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
                                    <div class="box-body p-0">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Productor</th>
                                                        <th class="text-right">Kilos netos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($query_exportacionProductor) { ?>
                                                        <?php foreach ($query_exportacionProductor as $fila) { ?>
                                                            <tr>
                                                                <td><?php echo $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre"; ?></td>
                                                                <td class="text-right"><?php echo number_format(round($fila["TOTAL"], 0), 0, ",", "."); ?> kg</td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="2" class="text-center">Sin exportaciones registradas.</td>
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
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Top 5 exportación por variedad</h4>
                                    </div>
                                    <div class="box-body p-0">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Variedad</th>
                                                        <th class="text-right">Kilos netos</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($query_exportacionVariedad) { ?>
                                                        <?php foreach ($query_exportacionVariedad as $fila) { ?>
                                                            <tr>
                                                                <td><?php echo $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre"; ?></td>
                                                                <td class="text-right"><?php echo number_format(round($fila["TOTAL"], 0), 0, ",", "."); ?> kg</td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="2" class="text-center">Sin exportaciones registradas.</td>
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
                                    <div class="box-header with-border">
                                        <h4 class="box-title">Existencia de materia prima por variedad</h4>
                                    </div>
                                    <div class="box-body p-0">
                                        <div class="table-responsive">
                                            <table class="table mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Variedad</th>
                                                        <th class="text-right">Kilos netos disponibles</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($query_existenciaVariedad) { ?>
                                                        <?php foreach ($query_existenciaVariedad as $fila) { ?>
                                                            <tr>
                                                                <td><?php echo $fila["NOMBRE"] ? $fila["NOMBRE"] : "Sin nombre"; ?></td>
                                                                <td class="text-right"><?php echo number_format(round($fila["TOTAL"], 0), 0, ",", "."); ?> kg</td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="2" class="text-center">No hay existencias registradas.</td>
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