<?php


include_once "../../assest/config/validarUsuarioExpo.php";

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
include_once '../../assest/controlador/EXIEXPORTACION_ADO.php';
include_once '../../assest/controlador/EEXPORTACION_ADO.php';
include_once '../../assest/controlador/PRODUCTOR_ADO.php';
include_once '../../assest/controlador/VESPECIES_ADO.php';
include_once '../../assest/controlador/ESPECIES_ADO.php';
include_once '../../assest/controlador/FOLIO_ADO.php';
include_once '../../assest/controlador/FOLIO_ADO.php';
include_once '../../assest/controlador/TMANEJO_ADO.php';
include_once '../../assest/controlador/TCALIBRE_ADO.php';
include_once '../../assest/controlador/TEMBALAJE_ADO.php';
include_once '../../assest/controlador/TPROCESO_ADO.php';
include_once '../../assest/controlador/TREEMBALAJE_ADO.php';
include_once '../../assest/controlador/COMPRADOR_ADO.php';
include_once '../../assest/controlador/DFINAL_ADO.php';
include_once '../../assest/controlador/TCOLOR_ADO.php';
include_once '../../assest/controlador/TCATEGORIA_ADO.php';
include_once '../../assest/controlador/ICARGA_ADO.php';

 


include_once '../../assest/controlador/RECEPCIONPT_ADO.php';
include_once '../../assest/controlador/REPALETIZAJEEX_ADO.php';
include_once '../../assest/controlador/PROCESO_ADO.php';
include_once '../../assest/controlador/REEMBALAJE_ADO.php';
include_once '../../assest/controlador/DESPACHOPT_ADO.php';
include_once '../../assest/controlador/DESPACHOEX_ADO.php';
include_once '../../assest/controlador/TINPSAG_ADO.php';
include_once '../../assest/controlador/INPSAG_ADO.php';


//INCIALIZAR LAS VARIBLES
//INICIALIZAR CONTROLADOR

$EXIEXPORTACION_ADO =  new EXIEXPORTACION_ADO();
$EEXPORTACION_ADO =  new EEXPORTACION_ADO();

$PRODUCTOR_ADO =  new PRODUCTOR_ADO();
$VESPECIES_ADO =  new VESPECIES_ADO();
$ESPECIES_ADO =  new ESPECIES_ADO();
$FOLIO_ADO =  new FOLIO_ADO();
$TMANEJO_ADO =  new TMANEJO_ADO();
$TCALIBRE_ADO =  new TCALIBRE_ADO();
$TEMBALAJE_ADO =  new TEMBALAJE_ADO();
$TPROCESO_ADO =  new TPROCESO_ADO();
$TREEMBALAJE_ADO =  new TREEMBALAJE_ADO();
$COMPRADOR_ADO =  new COMPRADOR_ADO();
$DFINAL_ADO =  new DFINAL_ADO();
$TCOLOR_ADO =  new TCOLOR_ADO();
$TCATEGORIA_ADO =  new TCATEGORIA_ADO();
$ICARGA_ADO =  new ICARGA_ADO();




$RECEPCIONPT_ADO =  new RECEPCIONPT_ADO();
$REPALETIZAJEEX_ADO =  new REPALETIZAJEEX_ADO();
$DESPACHOPT_ADO =  new DESPACHOPT_ADO();
$DESPACHOEX_ADO =  new DESPACHOEX_ADO();
$PROCESO_ADO =  new PROCESO_ADO();
$REEMBALAJE_ADO =  new REEMBALAJE_ADO();
$TINPSAG_ADO =  new TINPSAG_ADO();
$INPSAG_ADO =  new INPSAG_ADO();

//FUNCIONES DE APOYO
function obtenerDesdeCache($id, array &$cache, callable $callback)
{
    if (!$id) {
        return null;
    }
    if (!array_key_exists($id, $cache)) {
        $cache[$id] = $callback($id) ?: null;
    }
    return $cache[$id];
}

//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD




//INICIALIZAR ARREGLOS
//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD
$TOTALNETO = "";
$TOTALENVASE = "";
$TAMAÑO=0;
$CONTADOR=0;


//INICIALIZAR ARREGLOS
$ARRAYEXIEXPORTACION = "";
$ARRAYTOTALEXIEXPORTACION = "";
$ARRAYVEREEXPORTACIONID = "";
$ARRAYVERPRODUCTORID = "";
$ARRAYVERPVESPECIESID = "";
$ARRAYVERVESPECIESID = "";
$ARRAYVERESPECIESID = "";
$ARRAYVERFOLIOID = "";
$ARRAYEMPRESA = "";
$ARRAYPLANTA = "";
$ARRAYVERRECEPCIONPT = "";
$ARRAYDESPACHO2="";
$ARRAYTINPSAG = "";
$ARRAYINPSAG = "";

//CACHES PARA REDUCIR CONSULTAS REPETIDAS
$PRODUCTOR_CACHE = [];
$VESPECIES_CACHE = [];
$ESPECIES_CACHE = [];
$ESTANDAR_CACHE = [];
$RECEPCION_CACHE = [];
$DESPACHO_CACHE = [];
$PLANTA_CACHE = [];
$EMPRESA_CACHE = [];
$TEMPORADA_CACHE = [];
$TMANEJO_CACHE = [];
$TCALIBRE_CACHE = [];
$TEMBALAJE_CACHE = [];
$TPROCESO_CACHE = [];
$PROCESO_CACHE = [];
$TREEMBALAJE_CACHE = [];
$REEMBALAJE_CACHE = [];
$REPALETIZAJE_CACHE = [];
$INPSAG_CACHE = [];
$TCATEGORIA_CACHE = [];
$TCOLOR_CACHE = [];
$TINPSAG_CACHE = [];
$DFINAL_CACHE = [];
$COMPRADOR_CACHE = [];
$ICARGA_CACHE = [];

//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS CONTROLADORES
if ($EMPRESAS  && $TEMPORADAS) {
    $ARRAYEXIEXPORTACION = $EXIEXPORTACION_ADO->listarExiexportacionAgrupadoPorFolioEmpresaTemporada($EMPRESAS,  $TEMPORADAS);
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>Historial Existencia PT</title>
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
                
                function abrirPestana(url) {
                    var win = window.open(url, '_blank');
                    win.focus();
                }
                //FUNCION PARA ABRIR VENTANA QUE SE ENCUENTRA LA OPERACIONES DE DETALLE DE RECEPCION
                function abrirVentana(url) {
                    var opciones =
                        "'directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=1000, height=800'";
                    window.open(url, 'window', opciones);
                }
                
            </script>

</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary">
    <div class="wrapper">
        <!- LLAMADA AL MENU PRINCIPAL DE LA PAGINA-!>
            <?php include_once "../../assest/config/menuExpo.php"; ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div class="container-full">

                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Producto Terminado </h3>
                                <div class="d-inline-block align-items-center">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                            <li class="breadcrumb-item" aria-current="page">Modulo</li>
                                            <li class="breadcrumb-item" aria-current="page">Informes</li>
                                            <li class="breadcrumb-item" aria-current="page">Producto Terminado</li>
                                            <li class="breadcrumb-item" aria-current="page">Existencia</li>
                                            <li class="breadcrumb-item active" aria-current="page"> <a href="#">Historial Existencia PT</a>
                                            </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                        </div>
                    </div>
                    <!-- Main content -->
                    <section class="content">
                        <div class="box">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                        <div class="table-responsive">
                                            <table id="hexistencia" class="table-hover table-bordered" style="width: 300%;">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th>Folio Original</th>
                                                        <th>Folio Nuevo</th>
                                                        <th>Fecha Embalado </th>
                                                        <th>Estado </th>
                                                        <th>Estado Calidad</th>
                                                        <th>Condición </th>
                                                        <th>Código Estandar</th>
                                                        <th>Envase/Estandar</th>
                                                        <th>Tipo Calibre </th>
                                                        <th>CSG</th>
                                                        <th>Productor</th>
                                                        <th>Especies</th>
                                                        <th>Variedad</th>
                                                        <th>Cantidad Envase</th>
                                                        <th>Kilos Neto</th>
                                                        <th>% Deshidratacion</th>
                                                        <th>Kilos Deshidratacion</th>
                                                        <th>Kilos Bruto</th>
                                                        <th>Número Recepción </th>
                                                        <th>Fecha Recepción </th>
                                                        <th>Tipo Recepción </th>
                                                        <th>CSG/CSP Recepción</th>
                                                        <th>Origen Recepción </th>
                                                        <th>Número Guía Recepción </th>
                                                        <th>Fecha Guía Recepción
                                                        <th>Número Repaletizaje </th>
                                                        <th>Fecha Repaletizaje </th>
                                                        <th>Número Proceso </th>
                                                        <th>Fecha Proceso </th>
                                                        <th>Tipo Proceso </th>
                                                        <th>Número Reembalaje </th>
                                                        <th>Fecha Reembalaje </th>
                                                        <th>Tipo Reembalaje </th>                                              
                                                        <th>Número Inspección </th>
                                                        <th>Fecha Inspección </th>
                                                        <th>Tipo Inspección </th>
                                                        <th>Número Despacho </th>
                                                        <th>Fecha Despacho </th>
                                                        <th>Número Guía Despacho </th>
                                                        <th>Tipo Despacho </th>
                                                        <th>CSG/CSP Despacho</th>
                                                        <th>Destino Despacho</th>
                                                        <th>Tipo Manejo</th>
                                                        <th>Tipo Calibre </th>
                                                        <th>Tipo Embalaje </th>
                                                        <th>Stock</th>
                                                        <th>Embolsado</th>
                                                        <th>Gasificacion</th>
                                                        <th>Prefrío</th>
                                                        <th>Tipo Categoria </th>
                                                        <th>Tipo Color </th>      
                                                        <th>Días</th>
                                                        <th>Ingreso</th>
                                                        <th>Modificación</th>
                                                        <th>Empresa</th>
                                                        <th>Planta</th>
                                                        <th>Temporada</th>
                                                        <th>Numero Referencia</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($ARRAYEXIEXPORTACION as $s) : ?>

                                                        <?php $ARRAYEXISTENCIA=$EXIEXPORTACION_ADO->listarExiexportacionEmpresaTemporadaPorFolio($EMPRESAS,  $TEMPORADAS,$s['FOLIO_AUXILIAR_EXIEXPORTACION'] );  ?>                                                                                                                    
                                                        <?php foreach ($ARRAYEXISTENCIA as $r) : ?>
                                                            <?php  $CONTADOR+=1;   ?>
                                                            <?php
                                                            if ($r['ESTADO'] == "0") {
                                                                $ESTADO = "Elimnado";
                                                            }
                                                            if ($r['ESTADO'] == "1") {
                                                                $ESTADO = "Ingresando";
                                                            }
                                                            if ($r['ESTADO'] == "2") {
                                                                $ESTADO = "Disponible";
                                                            }
                                                            if ($r['ESTADO'] == "3") {
                                                                $ESTADO = "En Repaletizaje";
                                                            }
                                                            if ($r['ESTADO'] == "4") {
                                                                $ESTADO = "Repaletizado";
                                                            }
                                                            if ($r['ESTADO'] == "5") {
                                                                $ESTADO = "En Reembalaje";
                                                            }
                                                            if ($r['ESTADO'] == "6") {
                                                                $ESTADO = "Reembalaje";
                                                            }
                                                            if ($r['ESTADO'] == "7") {
                                                                $ESTADO = "En Despacho";
                                                            }
                                                            if ($r['ESTADO'] == "8") {
                                                                $ESTADO = "Despachado";
                                                            }
                                                            if ($r['ESTADO'] == "9") {
                                                                $ESTADO = "En Transito";
                                                            }
                                                            if ($r['ESTADO'] == "10") {
                                                                $ESTADO = "En Inspección Sag";
                                                            }
                                                            if ($r['ESTADO'] == "11") {
                                                                $ESTADO = "Rechazado";
                                                            }
                                                            if ($r['TESTADOSAG'] == null || $r['TESTADOSAG'] == "0") {
                                                                $ESTADOSAG = "Sin Condición";
                                                            }
                                                            if ($r['TESTADOSAG'] == "1") {
                                                                $ESTADOSAG =  "En Inspección";
                                                            }
                                                            if ($r['TESTADOSAG'] == "2") {
                                                                $ESTADOSAG =  "Aprobado Origen";
                                                            }
                                                            if ($r['TESTADOSAG'] == "3") {
                                                                $ESTADOSAG =  "Aprobado USDA";
                                                            }
                                                            if ($r['TESTADOSAG'] == "4") {
                                                                $ESTADOSAG =  "Fumigado";
                                                            }
                                                            if ($r['TESTADOSAG'] == "5") {
                                                                $ESTADOSAG =  "Rechazado";
                                                            }

                                                            if($r['COLOR']=="1"){
                                                                $TRECHAZOCOLOR="badge badge-danger ";
                                                                $COLOR="Rechazado";
                                                            }else if($r['COLOR']=="2"){
                                                                $TRECHAZOCOLOR="badge badge-warning ";
                                                                $COLOR="Objetado";
                                                            }else if($r['COLOR']=="3"){
                                                                $TRECHAZOCOLOR="badge badge-Success ";
                                                                $COLOR="Aprobado";
                                                            }else{
                                                                $TRECHAZOCOLOR="";
                                                                $COLOR="Sin Datos";
                                                            }                                                                                                             
                                                            if ($r['ID_ICARGA']) {
                                                                $ARRAYVERICARGA = obtenerDesdeCache($r['ID_ICARGA'], $ICARGA_CACHE, function ($id) use ($ICARGA_ADO) {
                                                                    return $ICARGA_ADO->verIcarga($id);
                                                                });
                                                                if ($ARRAYVERICARGA) {
                                                                    $NUMEROREFERENCIA = $ARRAYVERICARGA[0]["NREFERENCIA_ICARGA"];
                                                                } else {
                                                                    $NUMEROREFERENCIA = "Sin Datos";
                                                                }
                                                            } else {
                                                                $NUMEROREFERENCIA = "Sin Datos";
                                                            }
                                                            $ARRAYRECEPCION = obtenerDesdeCache($r['ID_RECEPCION'], $RECEPCION_CACHE, function ($id) use ($RECEPCIONPT_ADO) {
                                                                return $RECEPCIONPT_ADO->verRecepcion2($id);
                                                            });
                                                            $ARRAYDESPACHO2 = obtenerDesdeCache($r['ID_DESPACHO2'], $DESPACHO_CACHE, function ($id) use ($DESPACHOPT_ADO) {
                                                                return $DESPACHOPT_ADO->verDespachopt($id);
                                                            });
                                                            if ($ARRAYRECEPCION) {
                                                                $NUMERORECEPCION = $ARRAYRECEPCION[0]["NUMERO_RECEPCION"];
                                                                $FECHARECEPCION = $ARRAYRECEPCION[0]["FECHA"];
                                                                $NUMEROGUIARECEPCION = $ARRAYRECEPCION[0]["NUMERO_GUIA_RECEPCION"];
                                                                $FECHAGUIARECEPCION = $ARRAYRECEPCION[0]["GUIA"];
                                                                if ($ARRAYRECEPCION[0]["TRECEPCION"] == 1) {
                                                                    $TIPORECEPCION = "Desde Productor";
                                                                    $ARRAYPRODUCTOR2 = obtenerDesdeCache($ARRAYRECEPCION[0]['ID_PRODUCTOR'], $PRODUCTOR_CACHE, function ($id) use ($PRODUCTOR_ADO) {
                                                                        return $PRODUCTOR_ADO->verProductor($id);
                                                                    });
                                                                    if ($ARRAYPRODUCTOR2) {
                                                                        $CSGCSPORIGEN=$ARRAYPRODUCTOR2[0]['CSG_PRODUCTOR'];
                                                                        $ORIGEN =  $ARRAYPRODUCTOR2[0]['NOMBRE_PRODUCTOR'];
                                                                    } else {
                                                                        $ORIGEN = "Sin Datos";
                                                                        $CSGCSPORIGEN="Sin Datos";
                                                                    }
                                                                }
                                                                if ($ARRAYRECEPCION[0]["TRECEPCION"] == 2) {
                                                                    $TIPORECEPCION = "Planta Externa";
                                                                    $ARRAYPLANTA2 = obtenerDesdeCache($ARRAYRECEPCION[0]['ID_PLANTA2'], $PLANTA_CACHE, function ($id) use ($PLANTA_ADO) {
                                                                        return $PLANTA_ADO->verPlanta($id);
                                                                    });
                                                                    if ($ARRAYPLANTA2) {
                                                                        $ORIGEN = $ARRAYPLANTA2[0]['NOMBRE_PLANTA'];
                                                                        $CSGCSPORIGEN=$ARRAYPLANTA2[0]['CODIGO_SAG_PLANTA'];
                                                                    } else {
                                                                        $ORIGEN = "Sin Datos";
                                                                        $CSGCSPORIGEN="Sin Datos";
                                                                    }
                                                                }
                                                            }else if($ARRAYDESPACHO2){                                                                
                                                                $NUMERORECEPCION = $ARRAYDESPACHO2[0]["NUMERO_DESPACHO"];
                                                                $FECHARECEPCION = $ARRAYDESPACHO2[0]["FECHA"];                                                                
                                                                $NUMEROGUIARECEPCION = $ARRAYDESPACHO2[0]["NUMERO_GUIA_DESPACHO"];
                                                                $TIPORECEPCION = "Interplanta";
                                                                $FECHAGUIARECEPCION = "";                                                                
                                                                $ARRAYPLANTA2 = obtenerDesdeCache($ARRAYDESPACHO2[0]['ID_PLANTA'], $PLANTA_CACHE, function ($id) use ($PLANTA_ADO) {
                                                                    return $PLANTA_ADO->verPlanta($id);
                                                                });
                                                                if ($ARRAYPLANTA2) {
                                                                    $ORIGEN = $ARRAYPLANTA2[0]['NOMBRE_PLANTA'];
                                                                    $CSGCSPORIGEN=$ARRAYPLANTA2[0]['CODIGO_SAG_PLANTA'];
                                                                } else {
                                                                    $ORIGEN = "Sin Datos";
                                                                    $CSGCSPORIGEN="Sin Datos";
                                                                }                                                        
                                                            } else {
                                                                $NUMERORECEPCION = "Sin Datos";
                                                                $FECHARECEPCION = "";
                                                                $NUMEROGUIARECEPCION = "Sin Datos";
                                                                $FECHAGUIARECEPCION = "";
                                                                $TIPORECEPCION = "Sin Datos";
                                                                $ORIGEN = "Sin Datos";
                                                                $CSGCSPORIGEN = "Sin Datos";
                                                            }
                                                            $ARRAYPROCESO = obtenerDesdeCache($r['ID_PROCESO'], $PROCESO_CACHE, function ($id) use ($PROCESO_ADO) {
                                                                return $PROCESO_ADO->verProceso2($id);
                                                            });
                                                            if ($ARRAYPROCESO) {
                                                                $NUMEROPROCESO = $ARRAYPROCESO[0]["NUMERO_PROCESO"];
                                                                $FECHAPROCESO = $ARRAYPROCESO[0]["FECHA"];
                                                                $ARRAYTPROCESO = obtenerDesdeCache($ARRAYPROCESO[0]["ID_TPROCESO"], $TPROCESO_CACHE, function ($id) use ($TPROCESO_ADO) {
                                                                    return $TPROCESO_ADO->verTproceso($id);
                                                                });
                                                                if ($ARRAYTPROCESO) {
                                                                    $TPROCESO = $ARRAYTPROCESO[0]["NOMBRE_TPROCESO"];
                                                                }
                                                            } else {
                                                                $NUMEROPROCESO = "Sin datos";
                                                                $FECHAPROCESO = "";
                                                                $TPROCESO = "Sin datos";
                                                            }
                                                            $ARRAYREEMBALAJE = obtenerDesdeCache($r['ID_REEMBALAJE'], $REEMBALAJE_CACHE, function ($id) use ($REEMBALAJE_ADO) {
                                                                return $REEMBALAJE_ADO->verReembalaje2($id);
                                                            });
                                                            if ($ARRAYREEMBALAJE) {
                                                                $NUMEROREEMBALEJE = $ARRAYREEMBALAJE[0]["ID_TREEMBALAJE"];
                                                                $FECHAREEMBALEJE = $ARRAYREEMBALAJE[0]["FECHA"];
                                                                $ARRAYTREEMBALAJE = obtenerDesdeCache($ARRAYREEMBALAJE[0]["ID_TREEMBALAJE"], $TREEMBALAJE_CACHE, function ($id) use ($TREEMBALAJE_ADO) {
                                                                    return $TREEMBALAJE_ADO->verTreembalaje($id);
                                                                });
                                                                if ($ARRAYTREEMBALAJE) {
                                                                    $TREEMBALAJE = $ARRAYTREEMBALAJE[0]["NOMBRE_TREEMBALAJE"];
                                                                }
                                                            } else {
                                                                $NUMEROREEMBALEJE = "Sin datos";
                                                                $FECHAREEMBALEJE = "";
                                                                $TREEMBALAJE = "Sin datos";
                                                            }
                                                            $ARRATREPALETIZAJE = obtenerDesdeCache($r['ID_REPALETIZAJE'], $REPALETIZAJE_CACHE, function ($id) use ($REPALETIZAJEEX_ADO) {
                                                                return $REPALETIZAJEEX_ADO->verRepaletizaje2($id);
                                                            });
                                                            if ($ARRATREPALETIZAJE) {
                                                                $FECHAREPALETIZAJE = $ARRATREPALETIZAJE[0]["INGRESO"];
                                                                $NUMEROREPALETIZAJE = $ARRATREPALETIZAJE[0]["NUMERO_REPALETIZAJE"];
                                                            } else {
                                                                $NUMEROREPALETIZAJE = "Sin Datos";
                                                                $FECHAREPALETIZAJE = "";
                                                            }
                                                            $ARRAYINPSAG = obtenerDesdeCache($r['ID_INPSAG'], $INPSAG_CACHE, function ($id) use ($INPSAG_ADO) {
                                                                return $INPSAG_ADO->verInpsag3($id);
                                                            });
                                                            if ($ARRAYINPSAG) {
                                                                $FECHAINPSAG = $ARRAYINPSAG[0]["FECHA"];                                                                
                                                                $NUMEROINPSAG = $ARRAYINPSAG[0]["NUMERO_INPSAG"]."-".$ARRAYINPSAG[0]["CORRELATIVO_INPSAG"];
                                                                $ARRAYTINPSAG = obtenerDesdeCache($ARRAYINPSAG[0]["ID_TINPSAG"], $TINPSAG_CACHE, function ($id) use ($TINPSAG_ADO) {
                                                                    return $TINPSAG_ADO->verTinpsag($id);
                                                                });
                                                                if($ARRAYTINPSAG){
                                                                    $NOMBRETINPSAG= $ARRAYTINPSAG[0]["NOMBRE_TINPSAG"];
                                                                }else{
                                                                    $NOMBRETINPSAG = "Sin Datos";
                                                                }
                                         
                                                            } else {
                                                                $FECHAINPSAG = "";
                                                                $NUMEROINPSAG = "Sin Datos";
                                                                $NOMBRETINPSAG = "Sin Datos";
                                                            }
                                                            $ARRAYVERDESPACHOPT = obtenerDesdeCache($r['ID_DESPACHO'], $DESPACHO_CACHE, function ($id) use ($DESPACHOPT_ADO) {
                                                                return $DESPACHOPT_ADO->verDespachopt2($id);
                                                            });
                                                            $ARRYADESPACHOEX = obtenerDesdeCache($r['ID_DESPACHOEX'], $DESPACHO_CACHE, function ($id) use ($DESPACHOEX_ADO) {
                                                                return $DESPACHOEX_ADO->verDespachoex2($id);
                                                            });
                                                            if ($ARRAYVERDESPACHOPT) {
                                                                $NUMERODESPACHO = $ARRAYVERDESPACHOPT[0]["NUMERO_DESPACHO"];
                                                                $FECHADESPACHO = $ARRAYVERDESPACHOPT[0]["FECHA"];

                                                                if ($ARRAYVERDESPACHOPT[0]['TDESPACHO'] == "1") {
                                                                    $TDESPACHO = "Interplanta";
                                                                    $NUMEROGUIADESPACHO = $ARRAYVERDESPACHOPT[0]["NUMERO_GUIA_DESPACHO"];
                                                                    $ARRAYPLANTA2 = obtenerDesdeCache($ARRAYVERDESPACHOPT[0]['ID_PLANTA2'], $PLANTA_CACHE, function ($id) use ($PLANTA_ADO) {
                                                                        return $PLANTA_ADO->verPlanta($id);
                                                                    });
                                                                    if ($ARRAYPLANTA2) {
                                                                        $DESTINO = $ARRAYPLANTA2[0]['NOMBRE_PLANTA'];
                                                                        $CSGCSPDESTINO=$ARRAYPLANTA2[0]['CODIGO_SAG_PLANTA'];
                                                                    } else {
                                                                        $DESTINO = "Sin Datos";
                                                                        $CSGCSPDESTINO="Sin Datos";
                                                                    }
                                                                }
                                                                if ($ARRAYVERDESPACHOPT[0]['TDESPACHO'] == "2") {
                                                                    $TDESPACHO = "Devolución Productor";
                                                                    $NUMEROGUIADESPACHO = $ARRAYVERDESPACHOPT[0]["NUMERO_GUIA_DESPACHO"];
                                                                    $ARRAYPRODUCTOR = obtenerDesdeCache($ARRAYVERDESPACHOPT[0]['ID_PRODUCTOR'], $PRODUCTOR_CACHE, function ($id) use ($PRODUCTOR_ADO) {
                                                                        return $PRODUCTOR_ADO->verProductor($id);
                                                                    });
                                                                    if ($ARRAYPRODUCTOR) {
                                                                        $CSGCSPDESTINO=$ARRAYPRODUCTOR[0]['CSG_PRODUCTOR'];
                                                                        $DESTINO =  $ARRAYPRODUCTOR[0]['NOMBRE_PRODUCTOR'];
                                                                    } else {
                                                                        $DESTINO = "Sin Datos";
                                                                        $CSGCSPDESTINO="Sin Datos";
                                                                    }
                                                                }
                                                                if ($ARRAYVERDESPACHOPT[0]['TDESPACHO'] == "3") {
                                                                    $TDESPACHO = "Venta";
                                                                    $NUMEROGUIADESPACHO = $ARRAYVERDESPACHOPT[0]["NUMERO_GUIA_DESPACHO"];
                                                                    $ARRAYCOMPRADOR = obtenerDesdeCache($ARRAYVERDESPACHOPT[0]['ID_COMPRADOR'], $COMPRADOR_CACHE, function ($id) use ($COMPRADOR_ADO) {
                                                                        return $COMPRADOR_ADO->verComprador($id);
                                                                    });
                                                                    if ($ARRAYCOMPRADOR) {
                                                                        $DESTINO = $ARRAYCOMPRADOR[0]['NOMBRE_COMPRADOR'];
                                                                        $CSGCSPDESTINO="No Aplica";
                                                                    } else {
                                                                        $DESTINO = "Sin Datos";
                                                                        $CSGCSPDESTINO="Sin Datos";
                                                                    }
                                                                }
                                                                if ($ARRAYVERDESPACHOPT[0]['TDESPACHO'] == "4") {
                                                                    $TDESPACHO = "Despacho de Decarte";
                                                                    $NUMEROGUIADESPACHO = "No Aplica";
                                                                    $CSGCSPDESTINO="No Aplica";
                                                                    $DESTINO = $ARRAYVERDESPACHOPT[0]['REGALO_DESPACHO'];
                                                                }
                                                                if ($ARRAYVERDESPACHOPT[0]['TDESPACHO'] == "5") {
                                                                    $TDESPACHO = "Planta Externa";
                                                                    $NUMEROGUIADESPACHO = $ARRAYVERDESPACHOPT[0]["NUMERO_GUIA_DESPACHO"];
                                                                    $ARRAYPLANTA2 = obtenerDesdeCache($ARRAYVERDESPACHOPT[0]['ID_PLANTA3'], $PLANTA_CACHE, function ($id) use ($PLANTA_ADO) {
                                                                        return $PLANTA_ADO->verPlanta($id);
                                                                    });
                                                                    if ($ARRAYPLANTA2) {
                                                                        $DESTINO = $ARRAYPLANTA2[0]['NOMBRE_PLANTA'];
                                                                        $CSGCSPDESTINO=$ARRAYPLANTA2[0]['CODIGO_SAG_PLANTA'];
                                                                    } else {
                                                                        $DESTINO = "Sin Datos";
                                                                        $CSGCSPDESTINO="Sin Datos";
                                                                    }
                                                                }
                                                            } else if ($ARRYADESPACHOEX) {
                                                                $TDESPACHO = "Exportación";
                                                                $CSGCSPDESTINO="No Aplica";
                                                                $NUMERODESPACHO = $ARRYADESPACHOEX[0]["NUMERO_DESPACHOEX"];
                                                                $NUMEROGUIADESPACHO = $ARRYADESPACHOEX[0]["NUMERO_GUIA_DESPACHOEX"];
                                                                $FECHADESPACHO = $ARRYADESPACHOEX[0]["FECHA"];
                                                                $ARRAYDFINAL = obtenerDesdeCache($ARRYADESPACHOEX[0]['ID_DFINAL'], $DFINAL_CACHE, function ($id) use ($DFINAL_ADO) {
                                                                    return $DFINAL_ADO->verDfinal($id);
                                                                });
                                                                if ($ARRAYDFINAL) {
                                                                    $DESTINO = $ARRAYDFINAL[0]['NOMBRE_DFINAL'];
                                                                } else {
                                                                    $DESTINO = "Sin Datos";
                                                                }
                                                            } else {
                                                                $DESTINO = "Sin datos";
                                                                $TDESPACHO = "Sin datos";
                                                                $FECHADESPACHO = "";
                                                                $NUMERODESPACHO = "Sin Datos";
                                                                $NUMEROGUIADESPACHO = "Sin Datos";
                                                                $CSGCSPDESTINO="Sin Datos";
                                                            }
                                                            $ARRAYVERPRODUCTORID = obtenerDesdeCache($r['ID_PRODUCTOR'], $PRODUCTOR_CACHE, function ($id) use ($PRODUCTOR_ADO) {
                                                                return $PRODUCTOR_ADO->verProductor($id);
                                                            });
                                                            if ($ARRAYVERPRODUCTORID) {

                                                                $CSGPRODUCTOR = $ARRAYVERPRODUCTORID[0]['CSG_PRODUCTOR'];
                                                                $NOMBREPRODUCTOR = $ARRAYVERPRODUCTORID[0]['NOMBRE_PRODUCTOR'];
                                                            } else {
                                                                $CSGPRODUCTOR = "Sin Datos";
                                                                $NOMBREPRODUCTOR = "Sin Datos";
                                                            }
                                                            $ARRAYEVERERECEPCIONID = obtenerDesdeCache($r['ID_ESTANDAR'], $ESTANDAR_CACHE, function ($id) use ($EEXPORTACION_ADO) {
                                                                return $EEXPORTACION_ADO->verEstandar($id);
                                                            });
                                                            if ($ARRAYEVERERECEPCIONID) {
                                                                $CODIGOESTANDAR = $ARRAYEVERERECEPCIONID[0]['CODIGO_ESTANDAR'];
                                                                $NOMBREESTANDAR = $ARRAYEVERERECEPCIONID[0]['NOMBRE_ESTANDAR'];
                                                            } else {
                                                                $CODIGOESTANDAR = "Sin Datos";
                                                                $NOMBREESTANDAR = "Sin Datos";
                                                            }
                                                            $ARRAYVERVESPECIESID = obtenerDesdeCache($r['ID_VESPECIES'], $VESPECIES_CACHE, function ($id) use ($VESPECIES_ADO) {
                                                                return $VESPECIES_ADO->verVespecies($id);
                                                            });
                                                            if ($ARRAYVERVESPECIESID) {
                                                                $NOMBREVESPECIES = $ARRAYVERVESPECIESID[0]['NOMBRE_VESPECIES'];
                                                                $ARRAYVERESPECIESID = obtenerDesdeCache($ARRAYVERVESPECIESID[0]['ID_ESPECIES'], $ESPECIES_CACHE, function ($id) use ($ESPECIES_ADO) {
                                                                    return $ESPECIES_ADO->verEspecies($id);
                                                                });
                                                                if ($ARRAYVERVESPECIESID) {
                                                                    $NOMBRESPECIES = $ARRAYVERESPECIESID[0]['NOMBRE_ESPECIES'];
                                                                } else {
                                                                    $NOMBRESPECIES = "Sin Datos";
                                                                }
                                                            } else {
                                                                $NOMBREVESPECIES = "Sin Datos";
                                                                $NOMBRESPECIES = "Sin Datos";
                                                            }
                                                            $ARRAYTMANEJO = obtenerDesdeCache($r['ID_TMANEJO'], $TMANEJO_CACHE, function ($id) use ($TMANEJO_ADO) {
                                                                return $TMANEJO_ADO->verTmanejo($id);
                                                            });
                                                            if ($ARRAYTMANEJO) {
                                                                $NOMBRETMANEJO = $ARRAYTMANEJO[0]['NOMBRE_TMANEJO'];
                                                            } else {
                                                                $NOMBRETMANEJO = "Sin Datos";
                                                            }
                                                            $ARRAYTCALIBRE = obtenerDesdeCache($r['ID_TCALIBRE'], $TCALIBRE_CACHE, function ($id) use ($TCALIBRE_ADO) {
                                                                return $TCALIBRE_ADO->verCalibre($id);
                                                            });
                                                            if ($ARRAYTCALIBRE) {
                                                                $NOMBRETCALIBRE = $ARRAYTCALIBRE[0]['NOMBRE_TCALIBRE'];
                                                            } else {
                                                                $NOMBRETCALIBRE = "Sin Datos";
                                                            }
                                                            $ARRAYTEMBALAJE = obtenerDesdeCache($r['ID_TEMBALAJE'], $TEMBALAJE_CACHE, function ($id) use ($TEMBALAJE_ADO) {
                                                                return $TEMBALAJE_ADO->verEmbalaje($id);
                                                            });
                                                            if ($ARRAYTEMBALAJE) {
                                                                $NOMBRETEMBALAJE = $ARRAYTEMBALAJE[0]['NOMBRE_TEMBALAJE'];
                                                            } else {
                                                                $NOMBRETEMBALAJE = "Sin Datos";
                                                            }
                                                            $ARRAYTEMBALAJE = obtenerDesdeCache($r['ID_TEMBALAJE'], $TEMBALAJE_CACHE, function ($id) use ($TEMBALAJE_ADO) {
                                                                return $TEMBALAJE_ADO->verEmbalaje($id);
                                                            });
                                                            if ($ARRAYTEMBALAJE) {
                                                                $NOMBRETEMBALAJE = $ARRAYTEMBALAJE[0]['NOMBRE_TEMBALAJE'];
                                                            } else {
                                                                $NOMBRETEMBALAJE = "Sin Datos";
                                                            }
                                                            $ARRAYTCATEGORIA = obtenerDesdeCache($r['ID_TCATEGORIA'], $TCATEGORIA_CACHE, function ($id) use ($TCATEGORIA_ADO) {
                                                                return $TCATEGORIA_ADO->verTcategoria($id);
                                                            });
                                                            if($ARRAYTCATEGORIA){
                                                            $NOMBRETCATEGORIA= $ARRAYTCATEGORIA[0]["NOMBRE_TCATEGORIA"];
                                                            }else{
                                                                $NOMBRETCATEGORIA = "Sin Datos";
                                                            }   
                                                            $ARRAYTCOLOR = obtenerDesdeCache($r['ID_TCOLOR'], $TCOLOR_CACHE, function ($id) use ($TCOLOR_ADO) {
                                                                return $TCOLOR_ADO->verTcolor($id);
                                                            });
                                                            if($ARRAYTCOLOR){
                                                                $NOMBRETCOLOR= $ARRAYTCOLOR[0]["NOMBRE_TCOLOR"];
                                                            }else{
                                                                $NOMBRETCOLOR = "Sin Datos";
                                                            } 
                                                            $ARRAYEMPRESA = obtenerDesdeCache($r['ID_EMPRESA'], $EMPRESA_CACHE, function ($id) use ($EMPRESA_ADO) {
                                                                return $EMPRESA_ADO->verEmpresa($id);
                                                            });
                                                            if ($ARRAYEMPRESA) {
                                                                $NOMBREEMPRESA = $ARRAYEMPRESA[0]['NOMBRE_EMPRESA'];
                                                            } else {
                                                                $NOMBREEMPRESA = "Sin Datos";
                                                            }
                                                            $ARRAYPLANTA = obtenerDesdeCache($r['ID_PLANTA'], $PLANTA_CACHE, function ($id) use ($PLANTA_ADO) {
                                                                return $PLANTA_ADO->verPlanta($id);
                                                            });
                                                            if ($ARRAYPLANTA) {
                                                                $NOMBREPLANTA = $ARRAYPLANTA[0]['NOMBRE_PLANTA'];
                                                            } else {
                                                                $NOMBREPLANTA = "Sin Datos";
                                                            }
                                                            $ARRAYTEMPORADA = obtenerDesdeCache($r['ID_TEMPORADA'], $TEMPORADA_CACHE, function ($id) use ($TEMPORADA_ADO) {
                                                                return $TEMPORADA_ADO->verTemporada($id);
                                                            });
                                                            if ($ARRAYTEMPORADA) {
                                                                $NOMBRETEMPORADA = $ARRAYTEMPORADA[0]['NOMBRE_TEMPORADA'];
                                                            } else {
                                                                $NOMBRETEMPORADA = "Sin Datos";
                                                            }

                                                            if ($r['STOCK'] != "") {
                                                                $STOCK = $r['STOCK'];
                                                            } else if ($r['STOCK'] == "") {
                                                                $STOCK = "Sin Datos";
                                                            } else {
                                                                $STOCK = "Sin Datos";
                                                            }
                                                            if ($r['EMBOLSADO'] == "1") {
                                                                $EMBOLSADO =  "SI";
                                                            }
                                                            if ($r['EMBOLSADO'] == "0") {
                                                                $EMBOLSADO =  "NO";
                                                            }
                                                            if ($r['GASIFICADO'] == "1") {
                                                                $GASIFICADO = "SI";
                                                            } else if ($r['GASIFICADO'] == "0") {
                                                                $GASIFICADO = "NO";
                                                            } else {
                                                                $GASIFICADO = "Sin Datos";
                                                            }
                                                            if ($r['PREFRIO'] == "0") {
                                                                $PREFRIO = "NO";
                                                            } else if ($r['PREFRIO'] == "1") {
                                                                $PREFRIO =  "SI";
                                                            } else {
                                                                $PREFRIO = "Sin Datos";
                                                            }
                                                            ?>
                                                            <tr class="text-center">
                                                                <td>                                                                    
                                                                    <span class="<?php echo $TRECHAZOCOLOR; ?>">
                                                                        <?php echo $r['FOLIO_EXIEXPORTACION']; ?> 
                                                                    </span>
                                                                </td>
                                                                <td>                   
                                                                    <span class="<?php echo $TRECHAZOCOLOR; ?>">
                                                                        <a Onclick="abrirPestana('../../assest/documento/informeTarjasPT.php?parametro=<?php echo $r['FOLIO_AUXILIAR_EXIEXPORTACION']; ?>&&parametro1=<?php echo $r['ID_EMPRESA']; ?>&&parametro2=<?php echo $r['ID_PLANTA']; ?>&&tipo=3');">                                                                        
                                                                            <?php echo $r['FOLIO_AUXILIAR_EXIEXPORTACION']; ?>                                                                                                                                        
                                                                        </a>
                                                                    </span>
                                                                </td>
                                                                <td><?php echo $r['EMBALADO']; ?></td>
                                                                <td><?php echo $ESTADO; ?></td>
                                                                <td><?php echo $COLOR; ?></td>
                                                                <td><?php echo $ESTADOSAG; ?></td>
                                                                <td><?php echo $CODIGOESTANDAR; ?></td>
                                                                <td><?php echo $NOMBREESTANDAR; ?></td>
                                                                <td><?php echo $NOMBRETCALIBRE; ?></td>
                                                                <td><?php echo $CSGPRODUCTOR; ?></td>
                                                                <td><?php echo $NOMBREPRODUCTOR; ?></td>
                                                                <td><?php echo $NOMBRESPECIES; ?></td>
                                                                <td><?php echo $NOMBREVESPECIES; ?></td>
                                                                <td><?php echo $r['ENVASE']; ?></td>
                                                                <td><?php echo $r['NETO']; ?></td>
                                                                <td><?php echo $r['PORCENTAJE']; ?></td>
                                                                <td><?php echo $r['DESHIRATACION']; ?></td>
                                                                <td><?php echo $r['BRUTO']; ?></td>
                                                                <td><?php echo $NUMERORECEPCION; ?></td>
                                                                <td><?php echo $FECHARECEPCION; ?></td>
                                                                <td><?php echo $TIPORECEPCION; ?></td>
                                                                <td><?php echo $CSGCSPORIGEN; ?></td>
                                                                <td><?php echo $ORIGEN; ?></td>
                                                                <td><?php echo $NUMEROGUIARECEPCION; ?></td>
                                                                <td><?php echo $FECHAGUIARECEPCION; ?></td>
                                                                <td><?php echo $NUMEROREPALETIZAJE; ?></td>
                                                                <td><?php echo $FECHAREPALETIZAJE; ?></td>
                                                                <td><?php echo $NUMEROPROCESO; ?></td>
                                                                <td><?php echo $FECHAPROCESO; ?></td>
                                                                <td><?php echo $TPROCESO; ?></td>
                                                                <td><?php echo $NUMEROREEMBALEJE; ?></td>
                                                                <td><?php echo $FECHAREEMBALEJE; ?></td>
                                                                <td><?php echo $TREEMBALAJE; ?></td>
                                                                <td><?php echo $NUMEROINPSAG; ?></td>
                                                                <td><?php echo $FECHAINPSAG; ?></td>
                                                                <td><?php echo $NOMBRETINPSAG; ?></td>
                                                                <td><?php echo $NUMERODESPACHO; ?></td>
                                                                <td><?php echo $FECHADESPACHO; ?></td>
                                                                <td><?php echo $NUMEROGUIADESPACHO; ?></td>
                                                                <td><?php echo $TDESPACHO; ?></td>
                                                                <td><?php echo $CSGCSPDESTINO; ?></td>
                                                                <td><?php echo $DESTINO; ?></td>
                                                                <td><?php echo $NOMBRETMANEJO; ?></td>
                                                                <td><?php echo $NOMBRETCALIBRE; ?></td>
                                                                <td><?php echo $NOMBRETEMBALAJE; ?></td>
                                                                <td><?php echo $STOCK; ?></td>
                                                                <td><?php echo $EMBOLSADO; ?></td>
                                                                <td><?php echo $GASIFICADO; ?></td>
                                                                <td><?php echo $PREFRIO; ?></td>
                                                                <td><?php echo $NOMBRETCATEGORIA; ?></td>
                                                                <td><?php echo $NOMBRETCOLOR; ?></td>
                                                                <td><?php echo $r['DIAS']; ?></td>
                                                                <td><?php echo $r['INGRESO']; ?></td>
                                                                <td><?php echo $r['MODIFICACION']; ?></td>
                                                                <td><?php echo $NOMBREEMPRESA; ?></td>
                                                                <td><?php echo $NOMBREPLANTA; ?></td>
                                                                <td><?php echo $NOMBRETEMPORADA; ?></td>
                                                                <td><?php echo $NUMEROREFERENCIA; ?></td>
                                                            </tr>                                                       
                                                        <?php endforeach; ?>        
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr class="text-center" id="filtro">
                                                        <th>Folio Original</th>
                                                        <th>Folio Nuevo</th>
                                                        <th>Fecha Embalado </th>
                                                        <th>Estado </th>
                                                        <th>Estado Calidad</th>
                                                        <th>Condición </th>
                                                        <th>Código Estandar</th>
                                                        <th>Envase/Estandar</th>
                                                        <th>Tipo Calibre </th>
                                                        <th>CSG</th>
                                                        <th>Productor</th>
                                                        <th>Especies</th>
                                                        <th>Variedad</th>
                                                        <th>Cantidad Envase</th>
                                                        <th>Kilos Neto</th>
                                                        <th>% Deshidratacion</th>
                                                        <th>Kilos Deshidratacion</th>
                                                        <th>Kilos Bruto</th>
                                                        <th>Número Recepción </th>
                                                        <th>Fecha Recepción </th>
                                                        <th>Tipo Recepción </th>
                                                        <th>CSG/CSP Recepción</th>
                                                        <th>Origen Recepción </th>
                                                        <th>Número Guía Recepción </th>
                                                        <th>Fecha Guía Recepción
                                                        <th>Número Repaletizaje </th>
                                                        <th>Fecha Repaletizaje </th>
                                                        <th>Número Proceso </th>
                                                        <th>Fecha Proceso </th>
                                                        <th>Tipo Proceso </th>
                                                        <th>Número Reembalaje </th>
                                                        <th>Fecha Reembalaje </th>
                                                        <th>Tipo Reembalaje </th>                                       
                                                        <th>Número Inspección </th>
                                                        <th>Fecha Inspección </th>
                                                        <th>Tipo Inspección </th>
                                                        <th>Número Despacho </th>
                                                        <th>Fecha Despacho </th>
                                                        <th>Número Guía Despacho </th>
                                                        <th>Tipo Despacho </th>
                                                        <th>CSG/CSP Despacho</th>
                                                        <th>Destino Despacho</th>
                                                        <th>Tipo Manejo</th>
                                                        <th>Tipo Calibre </th>
                                                        <th>Tipo Embalaje </th>
                                                        <th>Stock</th>
                                                        <th>Embolsado</th>
                                                        <th>Gasificacion</th>
                                                        <th>Prefrío</th>
                                                        <th>Tipo Categoria </th>
                                                        <th>Tipo Color </th>      
                                                        <th>Días</th>
                                                        <th>Ingreso</th>
                                                        <th>Modificación</th>
                                                        <th>Empresa</th>
                                                        <th>Planta</th>
                                                        <th>Temporada</th>
                                                        <th>Numero Referencia</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box -->
                    </section>
                    <!-- /.content -->

                </div>
            </div>

            <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -!>
                <?php include_once "../../assest/config/footer.php"; ?>
                <?php include_once "../../assest/config/menuExtraExpo.php"; ?>
    </div>
    <!- LLAMADA URL DE ARCHIVOS DE DISEÑO Y JQUERY E OTROS -!>
        <?php include_once "../../assest/config/urlBase.php"; ?>
</body>

</html>