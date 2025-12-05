<?php


include_once "../../assest/config/validarUsuarioExpo.php";

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES

include_once '../../assest/controlador/EXIINDUSTRIAL_ADO.php';
include_once '../../assest/controlador/EINDUSTRIAL_ADO.php';
include_once '../../assest/controlador/ERECEPCION_ADO.php';
include_once '../../assest/controlador/EEXPORTACION_ADO.php';

include_once '../../assest/controlador/PRODUCTOR_ADO.php';
include_once '../../assest/controlador/VESPECIES_ADO.php';
include_once '../../assest/controlador/ESPECIES_ADO.php';
include_once '../../assest/controlador/FOLIO_ADO.php';
include_once '../../assest/controlador/COMPRADOR_ADO.php';

include_once '../../assest/controlador/TPROCESO_ADO.php';
include_once '../../assest/controlador/TREEMBALAJE_ADO.php';
include_once '../../assest/controlador/TMANEJO_ADO.php';

include_once '../../assest/controlador/RECEPCIONIND_ADO.php';
include_once '../../assest/controlador/PROCESO_ADO.php';
include_once '../../assest/controlador/DESPACHOIND_ADO.php';
include_once '../../assest/controlador/REEMBALAJE_ADO.php';
include_once '../../assest/controlador/RECHAZOMP_ADO.php';
include_once '../../assest/controlador/EMPRESA_ADO.php';
include_once '../../assest/controlador/PLANTA_ADO.php';
include_once '../../assest/controlador/TEMPORADA_ADO.php';

//INCIALIZAR LAS VARIBLES
//INICIALIZAR CONTROLADOR

$EXIINDUSTRIAL_ADO =  new EXIINDUSTRIAL_ADO();

$EINDUSTRIAL_ADO =  new EINDUSTRIAL_ADO();
$ERECEPCION_ADO =  new ERECEPCION_ADO();
$EEXPORTACION_ADO =  new EEXPORTACION_ADO();

$PRODUCTOR_ADO =  new PRODUCTOR_ADO();
$VESPECIES_ADO =  new VESPECIES_ADO();
$ESPECIES_ADO =  new ESPECIES_ADO();
$FOLIO_ADO =  new FOLIO_ADO();
$COMPRADOR_ADO =  new COMPRADOR_ADO();


$TPROCESO_ADO =  new TPROCESO_ADO();
$TREEMBALAJE_ADO =  new TREEMBALAJE_ADO();
$TMANEJO_ADO =  new TMANEJO_ADO();

$RECEPCIONIND_ADO =  new RECEPCIONIND_ADO();
$DESPACHOIND_ADO =  new DESPACHOIND_ADO();
$PROCESO_ADO =  new PROCESO_ADO();
$REEMBALAJE_ADO =  new REEMBALAJE_ADO();
$RECHAZOMP_ADO =  new RECHAZOMP_ADO();
$EMPRESA_ADO = new EMPRESA_ADO();
$PLANTA_ADO = new PLANTA_ADO();
$TEMPORADA_ADO = new TEMPORADA_ADO();

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

$TOTALNETO = "";


//INICIALIZAR ARREGLOS
$ARRAYEXIINDUSTRIAL = "";
$ARRAYTOTALEXIINDUSTRIAL = "";
$ARRAYEVERINDUSTRIALID = "";
$ARRAYVERPRODUCTORID = "";
$ARRAYVERPVESPECIESID = "";
$ARRAYVERVESPECIESID = "";
$ARRAYVERESPECIESID = "";
$ARRAYVERFOLIOID = "";
$ARRAYDESPACHO2="";

//CACHES PARA REDUCIR CONSULTAS REPETIDAS
$PRODUCTOR_CACHE = [];
$VESPECIES_CACHE = [];
$ESPECIES_CACHE = [];
$ESTANDAR_CACHE = [];
$TMANEJO_CACHE = [];
$TPROCESO_CACHE = [];
$TREEMBALAJE_CACHE = [];
$RECEPCION_CACHE = [];
$PROCESO_CACHE = [];
$REEMBALAJE_CACHE = [];
$DESPACHO_CACHE = [];
$EMPRESA_CACHE = [];
$PLANTA_CACHE = [];
$TEMPORADA_CACHE = [];
$COMPRADOR_CACHE = [];

$LOGOEMPRESA = '';
$NOMBREEMPRESA = '';

if ($EMPRESAS) {
    $ARRAYEMPRESA = $EMPRESA_ADO->verEmpresa($EMPRESAS);
    if ($ARRAYEMPRESA) {
        $LOGOEMPRESA = $ARRAYEMPRESA[0]['LOGO_EMPRESA'];
        $NOMBREEMPRESA = $ARRAYEMPRESA[0]['NOMBRE_EMPRESA'];
    }
}

//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS CONTROLADORES
if ($EMPRESAS   && $TEMPORADAS) {
    $ARRAYEXIINDUSTRIAL = $EXIINDUSTRIAL_ADO->listarExiindustrialEmpresaTemporadaCBX($EMPRESAS, $TEMPORADAS);
}


?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>Historial Existencia IND</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <!- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -!>
                <?php include_once "../../assest/config/urlHead.php"; ?>
    <style>
        .detalle-modal .modal-content {
            border: 1px solid #d0d7e3;
            box-shadow: 0 8px 22px rgba(0, 54, 94, 0.08);
            border-radius: 10px;
        }

        .detalle-modal .modal-header {
            background: #fff;
            color: #0f4a7a;
            border-bottom: 1px solid #d0d7e3;
            padding: 10px 12px;
        }

        .detalle-modal .modal-title {
            font-weight: 700;
            letter-spacing: 0.2px;
            margin: 0;
            color: #0f4a7a;
        }

        .detalle-modal .modal-subtitle {
            font-size: 11px;
            letter-spacing: 0.4px;
            color: #5a6f86;
            margin-bottom: 2px;
            opacity: 0.9;
        }

        .detalle-modal .close {
            color: #0f4a7a;
            opacity: 0.85;
        }

        .detalle-modal .modal-body {
            background: #fff;
            padding: 10px;
        }

        .detalle-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 6px;
            align-items: stretch;
            grid-auto-rows: 1fr;
        }

        .detalle-resumen-table {
            margin-bottom: 8px;
        }

        .detalle-resumen-table .detalle-table {
            table-layout: fixed;
        }

        .detalle-resumen-table thead th {
            background: #f2f6fb;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.4px;
            font-weight: 700;
        }

        .detalle-resumen-table tbody td {
            font-size: 14px;
            font-weight: 700;
        }

        .detalle-card {
            background: #fff;
            border: 1px solid #dce4ef;
            border-radius: 8px;
            padding: 0;
            box-shadow: 0 1px 4px rgba(15, 62, 91, 0.05);
            display: flex;
            flex-direction: column;
        }

        .detalle-card h5 {
            margin: 0;
            padding: 10px;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.4px;
            color: #2c4c67;
            background: #f6f9fc;
            border-bottom: 1px solid #e1e8f0;
            border-radius: 8px 8px 0 0;
        }

        .detalle-card .titulo-badge {
            font-size: 10px;
            padding: 4px 7px;
            border-radius: 10px;
            margin-left: 8px;
            color: #fff;
            background: linear-gradient(135deg, #1b7ac5, #0f4a7a);
            box-shadow: 0 2px 6px rgba(27, 122, 197, 0.35);
        }

        .detalle-table {
            width: 100%;
            margin-bottom: 0;
        }

        .detalle-card .detalle-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .detalle-table th,
        .detalle-table td {
            padding: 8px 12px;
            font-size: 13px;
            border: 0;
            color: #34495e;
            vertical-align: middle;
        }

        .detalle-table tr:nth-child(odd) {
            background: #f9fcff;
        }

        .detalle-card .detalle-table tr:nth-child(even) {
            background: #fdfefe;
        }

        .detalle-table th {
            width: 40%;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.3px;
            color: #5a6f86;
            background: none;
        }

        .detalle-table td {
            font-weight: 700;
            color: #1a2b3c;
        }

        .detalle-table tr:last-child th,
        .detalle-table tr:last-child td {
            border-bottom: none;
        }

        .detalle-badge {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #0f4a7a;
            background: #e9f3fb;
            border: 1px solid #c7d9ed;
        }

        .detalle-estado-calidad {
            background: #fff3e0;
            border-color: #ffd599;
            color: #a8600b;
        }

        .detalle-modal .modal-footer {
            border-top: 1px solid #d0d7e3;
            background: #f9fbfd;
            padding: 8px 12px;
            border-radius: 0 0 10px 10px;
        }

        .detalle-modal .btn-primary {
            background: linear-gradient(135deg, #1b7ac5, #0f4a7a);
            border-color: #0f4a7a;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(27, 122, 197, 0.3);
        }

        .detalle-modal .btn-secondary {
            background: #e3ebf3;
            border-color: #d1dbe6;
            color: #2c4c67;
            font-weight: 700;
        }

        .detalle-modal .btn {
            padding: 8px 16px;
            border-radius: 6px;
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
            <?php include_once "../../assest/config/menuExpo.php"; ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div class="container-full">

                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Granel</h3>
                                <div class="d-inline-block align-items-center">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                            <li class="breadcrumb-item" aria-current="page">Modulo</li>
                                            <li class="breadcrumb-item" aria-current="page">Informes</li>
                                            <li class="breadcrumb-item" aria-current="page">Granel</li>
                                            <li class="breadcrumb-item" aria-current="page">Existencia </li>
                                            <li class="breadcrumb-item active" aria-current="page"> <a href="#"> Historial Existencia IND</a>
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
                                            <table id="hexistencia" class="table-hover table-bordered" style="width: 100%;">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th class="no-export">Detalle</th>
                                                        <th>Folio Original</th>
                                                        <th>Folio Nuevo</th>
                                                        <th>Fecha Embalado </th>
                                                        <th>Estado </th>
                                                        <th>Calidad </th>
                                                        <th>Código Estandar</th>
                                                        <th>Envase/Estandar</th>
                                                        <th>CSG</th>
                                                        <th>Productor</th>
                                                        <th>Especies</th>
                                                        <th>Variedad</th>
                                                        <th>Kilos Neto</th>
                                                        <th>Número Recepción </th>
                                                        <th>Fecha Recepción </th>
                                                        <th>Tipo Recepción </th>
                                                        <th>CSG/CSP Recepción </th>
                                                        <th>Origen Recepción </th>
                                                        <th>Número Guía Recepción </th>
                                                        <th>Fecha Guía Recepción
                                                        <th>Número Proceso </th>
                                                        <th>Fecha Proceso </th>
                                                        <th>Tipo Proceso </th>
                                                        <th>Número Reembalaje </th>
                                                        <th>Fecha Reembalaje </th>
                                                        <th>Tipo Reembalaje </th>
                                                        <th>Número Despacho </th>
                                                        <th>Fecha Despacho </th>
                                                        <th>Número Guía Despacho </th>
                                                        <th>Tipo Despacho </th>
                                                        <th>CSG/CSP Despacho </th>
                                                        <th>Destino Despacho</th>
                                                        <th>Tipo Manejo</th>
                                                        <th>Días</th>
                                                        <th>Ingreso</th>
                                                        <th>Modificación</th>
                                                        <th class="d-none export-only">Empresa</th>
                                                        <th class="d-none export-only">Planta</th>
                                                        <th class="d-none export-only">Temporada</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($ARRAYEXIINDUSTRIAL as $r) : ?>
                                                        <?php

                                                        if ($r['ESTADO'] == "0") {
                                                            $ESTADO = "Eliminado";
                                                        }
                                                        if ($r['ESTADO'] == "1") {
                                                            $ESTADO = "Ingresando";
                                                        }
                                                        if ($r['ESTADO'] == "2") {
                                                            $ESTADO = "Disponible";
                                                        }
                                                        if ($r['ESTADO'] == "3") {
                                                            $ESTADO = "En Despacho";
                                                        }
                                                        if ($r['ESTADO'] == "4") {
                                                            $ESTADO = "Despachado";
                                                        }
                                                        if ($r['ESTADO'] == "5") {
                                                            $ESTADO = "En Transito";
                                                        }
                                                        if ($r['ESTADO'] == "6") {
                                                            $ESTADO = "Repaletizado";
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

                                                        $ARRAYEVERERECEPCIONID = obtenerDesdeCache($r['ID_ESTANDAR'], $ESTANDAR_CACHE, function ($id) use ($EINDUSTRIAL_ADO) {
                                                            return $EINDUSTRIAL_ADO->verEstandar($id);
                                                        });
                                                        $ARRAYEVERERECEPCIONID2 = obtenerDesdeCache($r['ID_ESTANDARMP'], $ESTANDAR_CACHE, function ($id) use ($ERECEPCION_ADO) {
                                                            return $ERECEPCION_ADO->verEstandar($id);
                                                        });
                                                        if ($ARRAYEVERERECEPCIONID) {
                                                            $CODIGOESTANDAR = $ARRAYEVERERECEPCIONID[0]['CODIGO_ESTANDAR'];
                                                            $NOMBREESTANDAR = $ARRAYEVERERECEPCIONID[0]['NOMBRE_ESTANDAR'];
                                                        }else  if ($ARRAYEVERERECEPCIONID2) {
                                                            $CODIGOESTANDAR = $ARRAYEVERERECEPCIONID2[0]['CODIGO_ESTANDAR'];
                                                            $NOMBREESTANDAR = $ARRAYEVERERECEPCIONID2[0]['NOMBRE_ESTANDAR'];
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

                                                        $ARRAYRECEPCION = obtenerDesdeCache($r['ID_RECEPCION'], $RECEPCION_CACHE, function ($id) use ($RECEPCIONIND_ADO) {
                                                            return $RECEPCIONIND_ADO->verRecepcion2($id);
                                                        });
                                                        $ARRAYDESPACHO2 = obtenerDesdeCache($r['ID_DESPACHO2'], $DESPACHO_CACHE, function ($id) use ($DESPACHOIND_ADO) {
                                                            return $DESPACHOIND_ADO->verDespachomp2($id);
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

                                                        $ARRAYVERDESPACHOPT = obtenerDesdeCache($r['ID_DESPACHO'], $DESPACHO_CACHE, function ($id) use ($DESPACHOIND_ADO) {
                                                            return $DESPACHOIND_ADO->verDespachomp2($id);
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
                                                                    $DESTINO = $ARRAYPRODUCTOR[0]['NOMBRE_PRODUCTOR'];
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
                                                                $TDESPACHO = "Despacho de Descarte";
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
                                                        } else {
                                                            $DESTINO = "Sin datos";
                                                            $TDESPACHO = "Sin datos";
                                                            $FECHADESPACHO = "";
                                                            $NUMERODESPACHO = "Sin Datos";
                                                            $NUMEROGUIADESPACHO = "Sin Datos";
                                                            $CSGCSPDESTINO="Sin Datos";
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
                                                        
                                                        $ARRAYTMANEJO = obtenerDesdeCache($r['ID_TMANEJO'], $TMANEJO_CACHE, function ($id) use ($TMANEJO_ADO) {
                                                            return $TMANEJO_ADO->verTmanejo($id);
                                                        });
                                                        if ($ARRAYTMANEJO) {
                                                            $NOMBRETMANEJO = $ARRAYTMANEJO[0]['NOMBRE_TMANEJO'];
                                                        } else {
                                                            $NOMBRETMANEJO = "Sin Datos";
                                                        }

                                                        $ESTADOCALIDAD = "Sin Datos";
                                                        ?>

                                                        <tr class="text-center">
                                                        <td class="no-export">
                                                                <button type="button"
                                                                    class="btn btn-info btn-sm detalle-existencia"
                                                                    data-toggle="modal"
                                                                    data-target="#detalleExistenciaModal"
                                                                    data-folio="<?php echo htmlspecialchars($r['FOLIO_EXIINDUSTRIAL'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-folio-aux="<?php echo htmlspecialchars($r['FOLIO_AUXILIAR_EXIINDUSTRIAL'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-estado="<?php echo htmlspecialchars($ESTADO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-estandar="<?php echo htmlspecialchars($CODIGOESTANDAR . ' - ' . $NOMBREESTANDAR, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-estado-calidad="<?php echo htmlspecialchars($ESTADOCALIDAD, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-productor="<?php echo htmlspecialchars($NOMBREPRODUCTOR, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-csg="<?php echo htmlspecialchars($CSGPRODUCTOR, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-especie="<?php echo htmlspecialchars($NOMBRESPECIES, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-variedad="<?php echo htmlspecialchars($NOMBREVESPECIES, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-neto="<?php echo htmlspecialchars($r['NETO'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-tmanejo="<?php echo htmlspecialchars($NOMBRETMANEJO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-num-recepcion="<?php echo htmlspecialchars($NUMERORECEPCION, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-fecha-recepcion="<?php echo htmlspecialchars($FECHARECEPCION, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-tipo-recepcion="<?php echo htmlspecialchars($TIPORECEPCION, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-origen="<?php echo htmlspecialchars($ORIGEN, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-csg-origen="<?php echo htmlspecialchars($CSGCSPORIGEN, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-num-guia-recepcion="<?php echo htmlspecialchars($NUMEROGUIARECEPCION, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-fecha-guia-recepcion="<?php echo htmlspecialchars($FECHAGUIARECEPCION, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-num-proceso="<?php echo htmlspecialchars($NUMEROPROCESO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-fecha-proceso="<?php echo htmlspecialchars($FECHAPROCESO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-tipo-proceso="<?php echo htmlspecialchars($TPROCESO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-num-reembalaje="<?php echo htmlspecialchars($NUMEROREEMBALEJE, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-fecha-reembalaje="<?php echo htmlspecialchars($FECHAREEMBALEJE, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-tipo-reembalaje="<?php echo htmlspecialchars($TREEMBALAJE, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-num-despacho="<?php echo htmlspecialchars($NUMERODESPACHO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-fecha-despacho="<?php echo htmlspecialchars($FECHADESPACHO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-num-guia-despacho="<?php echo htmlspecialchars($NUMEROGUIADESPACHO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-tipo-despacho="<?php echo htmlspecialchars($TDESPACHO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-destino="<?php echo htmlspecialchars($DESTINO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-csg-destino="<?php echo htmlspecialchars($CSGCSPDESTINO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-empresa="<?php echo htmlspecialchars($NOMBREEMPRESA, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-planta="<?php echo htmlspecialchars($NOMBREPLANTA, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-temporada="<?php echo htmlspecialchars($NOMBRETEMPORADA, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-ingreso="<?php echo htmlspecialchars($r['INGRESO'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-modificacion="<?php echo htmlspecialchars($r['MODIFICACION'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-id-recepcion="<?php echo htmlspecialchars($r['ID_RECEPCION'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-id-proceso="<?php echo htmlspecialchars($r['ID_PROCESO'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-id-reembalaje="<?php echo htmlspecialchars($r['ID_REEMBALAJE'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-id-despacho="<?php echo htmlspecialchars($r['ID_DESPACHO2'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                >
                                                                    Ver detalle
                                                                </button>
                                                            </td>
                                                            <td><?php echo $r['FOLIO_EXIINDUSTRIAL']; ?> </td>
                                                            <td><?php echo $r['FOLIO_AUXILIAR_EXIINDUSTRIAL']; ?> </td>
                                                            <td><?php echo $r['EMBALADO']; ?> </td>
                                                            <td><?php echo $ESTADO; ?> </td>
                                                            <td><?php echo $ESTADOCALIDAD; ?> </td>
                                                            <td><?php echo $CODIGOESTANDAR; ?></td>
                                                            <td><?php echo $NOMBREESTANDAR; ?></td>
                                                            <td><?php echo $CSGPRODUCTOR; ?></td>
                                                            <td><?php echo $NOMBREPRODUCTOR; ?></td>
                                                            <td><?php echo $NOMBRESPECIES; ?></td>
                                                            <td><?php echo $NOMBREVESPECIES; ?></td>
                                                            <td><?php echo $r['NETO']; ?></td>
                                                            <td><?php echo $NUMERORECEPCION; ?></td>
                                                            <td><?php echo $FECHARECEPCION; ?></td>
                                                            <td><?php echo $TIPORECEPCION; ?></td>
                                                            <td><?php echo $CSGCSPORIGEN; ?></td>
                                                            <td><?php echo $ORIGEN; ?></td>
                                                            <td><?php echo $NUMEROGUIARECEPCION; ?></td>
                                                            <td><?php echo $FECHAGUIARECEPCION; ?></td>
                                                            <td><?php echo $NUMEROPROCESO; ?></td>
                                                            <td><?php echo $FECHAPROCESO; ?></td>
                                                            <td><?php echo $TPROCESO; ?></td>
                                                            <td><?php echo $NUMEROREEMBALEJE; ?></td>
                                                            <td><?php echo $FECHAREEMBALEJE; ?></td>
                                                            <td><?php echo $TREEMBALAJE; ?></td>
                                                            <td><?php echo $NUMERODESPACHO; ?></td>
                                                            <td><?php echo $FECHADESPACHO; ?></td>
                                                            <td><?php echo $NUMEROGUIADESPACHO; ?></td>
                                                            <td><?php echo $TDESPACHO; ?></td>
                                                            <td><?php echo $CSGCSPDESTINO; ?></td>
                                                            <td><?php echo $DESTINO; ?></td>
                                                            <td><?php echo $NOMBRETMANEJO; ?></td>
                                                            <td><?php echo $r['DIAS']; ?></td>
                                                            <td><?php echo $r['INGRESO']; ?></td>
                                                            <td><?php echo $r['MODIFICACION']; ?></td>
                                                            <td><?php echo $NOMBREEMPRESA; ?></td>
                                                            <td><?php echo $NOMBREPLANTA; ?></td>
                                                            <td><?php echo $NOMBRETEMPORADA; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                <tr class="text-center" id="filtro">
                                                        <th class="no-export">Detalle</th>
                                                        <th>Folio Original</th>
                                                        <th>Folio Nuevo</th>
                                                        <th>Fecha Embalado </th>
                                                        <th>Estado </th>
                                                        <th>Calidad </th>
                                                        <th>Código Estandar</th>
                                                        <th>Envase/Estandar</th>
                                                        <th>CSG</th>
                                                        <th>Productor</th>
                                                        <th>Especies</th>
                                                        <th>Variedad</th>
                                                        <th>Kilos Neto</th>
                                                        <th>Número Recepción </th>
                                                        <th>Fecha Recepción </th>
                                                        <th>Tipo Recepción </th>
                                                        <th>CSG/CSP Recepción </th>
                                                        <th>Origen Recepción </th>
                                                        <th>Número Guía Recepción </th>
                                                        <th>Fecha Guía Recepción
                                                        <th>Número Proceso </th>
                                                        <th>Fecha Proceso </th>
                                                        <th>Tipo Proceso </th>
                                                        <th>Número Reembalaje </th>
                                                        <th>Fecha Reembalaje </th>
                                                        <th>Tipo Reembalaje </th>
                                                        <th>Número Despacho </th>
                                                        <th>Fecha Despacho </th>
                                                        <th>Número Guía Despacho </th>
                                                        <th>Tipo Despacho </th>
                                                        <th>CSG/CSP Despacho </th>
                                                        <th>Destino Despacho</th>
                                                        <th>Tipo Manejo</th>
                                                        <th>Días</th>
                                                        <th>Ingreso</th>
                                                        <th>Modificación</th>
                                                        <th class="d-none export-only">Empresa</th>
                                                        <th class="d-none export-only">Planta</th>
                                                        <th class="d-none export-only">Temporada</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                </div>
                            </div>
                            <!-- /.box -->

                    </section>
                    <!-- /.content -->

                </div>
            </div>





        <div class="modal fade" id="detalleExistenciaModal" tabindex="-1" role="dialog" aria-labelledby="detalleExistenciaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content detalle-modal">
                    <div class="modal-header">
                        <div>
                            <p class="modal-subtitle mb-0 text-uppercase">Historial de existencia</p>
                            <h4 class="modal-title" id="detalleExistenciaModalLabel">Detalle existencia</h4>
                        </div>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="detalle-resumen-table mb-2">
                            <table class="detalle-table resumen-table">
                                <thead>
                                    <tr>
                                        <th>Folio original</th>
                                        <th>Folio nuevo</th>
                                        <th>Estado</th>
                                        <th>Calidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td data-detail="folio"></td>
                                        <td data-detail="folio-aux"></td>
                                        <td><span class="detalle-badge" data-detail="estado"></span></td>
                                        <td><span class="detalle-badge detalle-estado-calidad" data-detail="estado-calidad"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="detalle-grid mb-1">
                            <div class="detalle-card">
                                <h5>Identificación</h5>
                                <table class="detalle-table">
                                    <tr>
                                        <th>Estandar</th>
                                        <td data-detail="estandar"></td>
                                    </tr>
                                    <tr>
                                        <th>Especie / Variedad</th>
                                        <td data-detail="especie"></td>
                                    </tr>
                                    <tr>
                                        <th>Kilos neto</th>
                                        <td data-detail="kilos"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="detalle-card">
                                <h5>Productor y manejo</h5>
                                <table class="detalle-table">
                                    <tr>
                                        <th>Productor</th>
                                        <td data-detail="productor"></td>
                                    </tr>
                                    <tr>
                                        <th>Manejo</th>
                                        <td data-detail="manejo"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="detalle-card">
                                <h5>Movimientos</h5>
                                <table class="detalle-table">
                                    <tr>
                                        <th>Recepción</th>
                                        <td data-detail="recepcion"></td>
                                    </tr>
                                    <tr>
                                        <th>Guía recepción</th>
                                        <td data-detail="guia-recepcion"></td>
                                    </tr>
                                    <tr>
                                        <th>Proceso</th>
                                        <td data-detail="proceso"></td>
                                    </tr>
                                    <tr>
                                        <th>Reembalaje</th>
                                        <td data-detail="reembalaje"></td>
                                    </tr>
                                    <tr>
                                        <th>Despacho</th>
                                        <td data-detail="despacho"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="detalle-card">
                                <h5>Ubicación y fechas</h5>
                                <table class="detalle-table">
                                    <tr>
                                        <th>Empresa</th>
                                        <td data-detail="empresa"></td>
                                    </tr>
                                    <tr>
                                        <th>Planta</th>
                                        <td data-detail="planta"></td>
                                    </tr>
                                    <tr>
                                        <th>Temporada</th>
                                        <td data-detail="temporada"></td>
                                    </tr>
                                    <tr>
                                        <th>Ingreso</th>
                                        <td data-detail="ingreso"></td>
                                    </tr>
                                    <tr>
                                        <th>Modificación</th>
                                        <td data-detail="modificacion"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="exportDetallePdf()">Imprimir Trazabilidad</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

            <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -!>
                <?php include_once "../../assest/config/footer.php"; ?>
                <?php include_once "../../assest/config/menuExtraExpo.php"; ?>
    </div>
    <!- LLAMADA URL DE ARCHIVOS DE DISEÑO Y JQUERY E OTROS -!>
        <?php include_once "../../assest/config/urlBase.php"; ?>

    <script type="text/javascript">
        const LOGO_EMPRESA = "<?php echo htmlspecialchars($LOGOEMPRESA ?? '', ENT_QUOTES, 'UTF-8'); ?>";
        const NOMBRE_EMPRESA = "<?php echo htmlspecialchars($NOMBREEMPRESA ?? '', ENT_QUOTES, 'UTF-8'); ?>";

        document.addEventListener('DOMContentLoaded', function() {
            function setDetailWithLink(modal, key, text, url) {
                var container = modal.find('[data-detail="' + key + '"]');
                if (!container.length) {
                    return;
                }
                if (url) {
                    var link = $('<a/>', {
                        class: 'mov-link',
                        href: url,
                        target: '_blank',
                        text: text
                    });
                    container.empty().append(link);
                } else {
                    container.text(text);
                }
            }

            $('#detalleExistenciaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('[data-detail="folio"]').text(button.data('folio'));
                modal.find('[data-detail="folio-aux"]').text(button.data('folio-aux'));
                modal.find('[data-detail="estado"]').text(button.data('estado'));
                modal.find('[data-detail="estado-calidad"]').text(button.data('estado-calidad'));
                modal.find('[data-detail="estandar"]').text(button.data('estandar'));
                modal.find('[data-detail="productor"]').text(button.data('productor') + ' (' + button.data('csg') + ')');
                modal.find('[data-detail="especie"]').text(button.data('especie') + ' / ' + button.data('variedad'));
                modal.find('[data-detail="kilos"]').text(button.data('neto'));
                modal.find('[data-detail="manejo"]').text(button.data('tmanejo'));

                var recepcionTexto = button.data('tipo-recepcion') + ' #' + button.data('num-recepcion') + ' (' + button.data('fecha-recepcion') + ') ' + button.data('origen') + ' [' + button.data('csg-origen') + ']';
                var recepcionUrl = button.data('id-recepcion') ? '../../fruta/vista/registroRecepcionind.php?op&id=' + encodeURIComponent(button.data('id-recepcion')) + '&a=ver' : '';
                setDetailWithLink(modal, 'recepcion', recepcionTexto, recepcionUrl);
                modal.find('[data-detail="guia-recepcion"]').text(button.data('num-guia-recepcion') + (button.data('fecha-guia-recepcion') ? ' (' + button.data('fecha-guia-recepcion') + ')' : ''));

                var procesoTexto = button.data('tipo-proceso') + ' #' + button.data('num-proceso') + ' (' + button.data('fecha-proceso') + ')';
                var procesoUrl = button.data('id-proceso') ? '../../fruta/vista/registroProceso.php?op&id=' + encodeURIComponent(button.data('id-proceso')) + '&a=ver' : '';
                setDetailWithLink(modal, 'proceso', procesoTexto, procesoUrl);

                var reembalajeTexto = (button.data('tipo-reembalaje') || 'Sin datos') + ' #' + (button.data('num-reembalaje') || '');
                var reembalajeUrl = button.data('id-reembalaje') ? '../../fruta/vista/registroReembalaje.php?op&id=' + encodeURIComponent(button.data('id-reembalaje')) + '&a=ver' : '';
                setDetailWithLink(modal, 'reembalaje', reembalajeTexto.trim(), reembalajeUrl);

                var despachoTexto = button.data('tipo-despacho') + ' #' + button.data('num-despacho') + ' (' + button.data('fecha-despacho') + ') ' + button.data('destino') + ' [' + button.data('csg-destino') + ']';
                var despachoUrl = button.data('id-despacho') ? '../../fruta/vista/registroDespachomp.php?op&id=' + encodeURIComponent(button.data('id-despacho')) + '&a=ver' : '';
                setDetailWithLink(modal, 'despacho', despachoTexto, despachoUrl);

                modal.find('[data-detail="empresa"]').text(button.data('empresa'));
                modal.find('[data-detail="planta"]').text(button.data('planta'));
                modal.find('[data-detail="temporada"]').text(button.data('temporada'));
                modal.find('[data-detail="ingreso"]').text(button.data('ingreso'));
                modal.find('[data-detail="modificacion"]').text(button.data('modificacion'));
            });
        });

        function exportDetallePdf() {
            var modal = $('#detalleExistenciaModal');
            var doc = new jspdf.jsPDF('p', 'pt', 'letter');
            var logo = LOGO_EMPRESA;
            var nombreEmpresa = NOMBRE_EMPRESA || 'Empresa';

            var x = 40;
            var y = 40;
            if (logo) {
                var img = new Image();
                img.src = 'data:image/png;base64,' + logo;
                doc.addImage(img, 'PNG', x, y, 90, 40);
            }
            doc.setFontSize(14);
            doc.setTextColor(15, 74, 122);
            doc.text(nombreEmpresa, x + 110, y + 15);
            doc.setFontSize(10);
            doc.setTextColor(84, 122, 167);
            doc.text('Trazabilidad - ' + new Date().toLocaleString(), x + 110, y + 35);

            y += 70;
            doc.autoTable({
                startY: y,
                styles: { fontSize: 9, cellPadding: 4, halign: 'left' },
                headStyles: { fillColor: [236, 242, 249], textColor: [15, 74, 122] },
                head: [['Folio original', 'Folio nuevo', 'Estado', 'Calidad']],
                body: [
                    [
                        modal.find('[data-detail="folio"]').text(),
                        modal.find('[data-detail="folio-aux"]').text(),
                        modal.find('[data-detail="estado"]').text(),
                        modal.find('[data-detail="estado-calidad"]').text()
                    ]
                ]
            });

            var sections = [
                {
                    title: 'Identificación',
                    rows: [
                        ['Estandar', modal.find('[data-detail="estandar"]').text()],
                        ['Especie / Variedad', modal.find('[data-detail="especie"]').text()],
                        ['Kilos neto', modal.find('[data-detail="kilos"]').text()]
                    ]
                },
                {
                    title: 'Productor y manejo',
                    rows: [
                        ['Productor', modal.find('[data-detail="productor"]').text()],
                        ['Manejo', modal.find('[data-detail="manejo"]').text()]
                    ]
                },
                {
                    title: 'Movimientos',
                    rows: [
                        ['Recepción', modal.find('[data-detail="recepcion"]').text()],
                        ['Guía recepción', modal.find('[data-detail="guia-recepcion"]').text()],
                        ['Proceso', modal.find('[data-detail="proceso"]').text()],
                        ['Reembalaje', modal.find('[data-detail="reembalaje"]').text()],
                        ['Despacho', modal.find('[data-detail="despacho"]').text()]
                    ]
                },
                {
                    title: 'Ubicación y fechas',
                    rows: [
                        ['Empresa', modal.find('[data-detail="empresa"]').text()],
                        ['Planta', modal.find('[data-detail="planta"]').text()],
                        ['Temporada', modal.find('[data-detail="temporada"]').text()],
                        ['Ingreso', modal.find('[data-detail="ingreso"]').text()],
                        ['Modificación', modal.find('[data-detail="modificacion"]').text()]
                    ]
                }
            ];

            sections.forEach(function(section) {
                doc.autoTable({
                    startY: doc.lastAutoTable ? doc.lastAutoTable.finalY + 12 : undefined,
                    styles: { fontSize: 9, cellPadding: 4, halign: 'left' },
                    headStyles: { fillColor: [245, 248, 251], textColor: [15, 74, 122] },
                    head: [[section.title, '']],
                    body: section.rows
                });
            });

            doc.save('detalle_trazabilidad_industrial.pdf');
        }
    </script>
 </body>

 </html>
