<?php


include_once "../../assest/config/validarUsuarioFruta.php";

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




//INICIALIZAR ARREGLOS
$ARRAYEXIINDUSTRIAL = "";
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
if ($EMPRESAS  && $PLANTAS && $TEMPORADAS) {
    $ARRAYEXIINDUSTRIAL = $EXIINDUSTRIAL_ADO->listarExiindustrialEmpresaPlantaTemporadaCBX($EMPRESAS, $PLANTAS, $TEMPORADAS);
}


?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>Existencia Producto Industrial</title>
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
                height: 100%;
            }

            .detalle-card h5 {
                font-size: 12px;
                font-weight: 600;
                color: #0f2d4a;
                margin: 0;
                letter-spacing: 0.3px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                text-transform: uppercase;
                padding: 8px 10px;
                border-bottom: 1px solid #dce4ef;
            }

            .detalle-card .titulo-badge {
                font-size: 11px;
                background: #0f4a7a;
                color: #fff;
                padding: 2px 8px;
                border-radius: 4px;
                letter-spacing: 0.3px;
            }

            .detalle-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            .detalle-table th,
            .detalle-table td {
                border: 1px solid #dce4ef;
                padding: 6px 8px;
                font-size: 12px;
                vertical-align: top;
                word-wrap: break-word;
                word-break: break-word;
            }

            .detalle-table th {
                background: #f7f9fc;
                color: #0f4a7a;
                width: 38%;
            }

            .detalle-table td {
                color: #0f2d4a;
            }

            .detalle-badge {
                display: inline-block;
                background: #0f4a7a;
                color: #fff;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: 0.3px;
            }

            .detalle-estado-calidad {
                background: #2e7d32;
            }

            .detalle-modal .modal-footer {
                padding: 10px;
                border-top: 1px solid #d0d7e3;
            }

            .detalle-modal .btn-primary {
                background: #0d6efd;
                border-color: #0b5ed7;
                font-weight: 700;
            }

            .detalle-modal .btn-secondary {
                background: #e7eef7;
                color: #0a2f57;
                border: 1px solid #c5d3e6;
                font-weight: 700;
            }

            .detalle-modal .btn {
                padding: 8px 14px;
                font-weight: 600;
                letter-spacing: 0.2px;
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
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <div class="container-full">
                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Existencia  Industrial</h3>
                                <div class="d-inline-block align-items-center">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                            <li class="breadcrumb-item" aria-current="page">Modulo</li>
                                            <li class="breadcrumb-item" aria-current="page">Existencia</li>
                                            <li class="breadcrumb-item" aria-current="page">Historial</li>
                                            <li class="breadcrumb-item active" aria-current="page"> <a href="#"> Existencia Producto Industrial </a> </li>
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
                                                        <th class="no-export"></th>
                                                        <th>Folio Original</th>
                                                        <th>Folio Nuevo</th>
                                                        <th>Fecha Embalado </th>
                                                        <th>Estado </th>
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
                                                        <th>Fecha Guía Recepción</th>
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
                                                        } elseif ($ARRAYEVERERECEPCIONID2) {
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
                                                            if ($ARRAYVERESPECIESID) {
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
                                                                $ARRAYPRODUCTOR2 = $PRODUCTOR_ADO->verProductor($ARRAYRECEPCION[0]['ID_PRODUCTOR']);
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
                                                                $ARRAYPLANTA2 = $PLANTA_ADO->verPlanta($ARRAYRECEPCION[0]['ID_PLANTA2']);
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
                                                            $ARRAYPLANTA2 = $PLANTA_ADO->verPlanta($ARRAYDESPACHO2[0]['ID_PLANTA']);
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
                                                                $ARRAYPLANTA2 = $PLANTA_ADO->verPlanta($ARRAYVERDESPACHOPT[0]['ID_PLANTA2']);
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
                                                                $ARRAYPRODUCTOR = $PRODUCTOR_ADO->verProductor($ARRAYVERDESPACHOPT[0]['ID_PRODUCTOR']);
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
                                                                $ARRAYCOMPRADOR = $COMPRADOR_ADO->verComprador($ARRAYVERDESPACHOPT[0]['ID_COMPRADOR']);
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
                                                                $ARRAYPLANTA2 = $PLANTA_ADO->verPlanta($ARRAYVERDESPACHOPT[0]['ID_PLANTA3']);
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
                                                        ?>

                                                        <?php
                                                        $ENVASES = isset($r['CANTIDAD_ENVASE_EXIINDUSTRIAL']) ? $r['CANTIDAD_ENVASE_EXIINDUSTRIAL'] : (isset($r['ENVASE']) ? $r['ENVASE'] : 'Sin Datos');
                                                        $ESTADOCALIDAD = isset($r['ESTADO_CALIDAD']) ? $r['ESTADO_CALIDAD'] : 'Sin Datos';
                                                        ?>
                                                        <tr class="text-center">
                                                            <td class="no-export">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-primary"
                                                                    data-toggle="modal"
                                                                    data-target="#detalleExistenciaModal"
                                                                    data-folio="<?php echo htmlspecialchars($r['FOLIO_EXIINDUSTRIAL'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-folio-aux="<?php echo htmlspecialchars($r['FOLIO_AUXILIAR_EXIINDUSTRIAL'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-estado="<?php echo htmlspecialchars($ESTADO, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-estado-calidad="<?php echo htmlspecialchars($ESTADOCALIDAD, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-estandar="<?php echo htmlspecialchars($CODIGOESTANDAR . ' - ' . $NOMBREESTANDAR, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-productor="<?php echo htmlspecialchars($NOMBREPRODUCTOR, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-csg="<?php echo htmlspecialchars($CSGPRODUCTOR, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-especie="<?php echo htmlspecialchars($NOMBRESPECIES, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-variedad="<?php echo htmlspecialchars($NOMBREVESPECIES, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-envases="<?php echo htmlspecialchars($ENVASES, ENT_QUOTES, 'UTF-8'); ?>"
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
                                                                    data-id-despacho="<?php echo htmlspecialchars($r['ID_DESPACHO'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                    data-id-despacho2="<?php echo htmlspecialchars($r['ID_DESPACHO2'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                    <i class="ti-zoom-in"></i>
                                                                </button>
                                                            </td>
                                                            <td><?php echo $r['FOLIO_EXIINDUSTRIAL']; ?> </td>
                                                            <td><?php echo $r['FOLIO_AUXILIAR_EXIINDUSTRIAL']; ?> </td>
                                                            <td><?php echo $r['EMBALADO']; ?> </td>
                                                            <td><?php echo $ESTADO; ?> </td>
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
                                                            <td class="d-none export-only"><?php echo $NOMBREEMPRESA; ?></td>
                                                            <td class="d-none export-only"><?php echo $NOMBREPLANTA; ?></td>
                                                            <td class="d-none export-only"><?php echo $NOMBRETEMPORADA; ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr class="text-center" id="filtro">
                                                        <th class="no-export"></th>
                                                        <th>Folio Original</th>
                                                        <th>Folio Nuevo</th>
                                                        <th>Fecha Embalado </th>
                                                        <th>Estado </th>
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
                                                        <th>Fecha Guía Recepción</th>
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
                        <div class="detalle-resumen-table">
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
                                        <th>Cantidad</th>
                                        <td data-detail="envases"></td>
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
        <?php include_once "../../assest/config/menuExtraFruta.php"; ?>
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
                modal.find('[data-detail="envases"]').text(button.data('envases'));
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
            var modal = document.getElementById('detalleExistenciaModal');
            if (!modal) {
                return;
            }

            var getDetail = function(key) {
                var node = modal.querySelector('[data-detail="' + key + '"]');
                return node ? node.textContent || '' : '';
            };

            var detalle = {
                folio: getDetail('folio'),
                folioAux: getDetail('folio-aux'),
                estado: getDetail('estado'),
                estadoCalidad: getDetail('estado-calidad'),
                estandar: getDetail('estandar'),
                especie: getDetail('especie'),
                productor: getDetail('productor'),
                envases: getDetail('envases'),
                kilos: getDetail('kilos'),
                manejo: getDetail('manejo'),
                recepcion: getDetail('recepcion'),
                guiaRecepcion: getDetail('guia-recepcion'),
                proceso: getDetail('proceso'),
                reembalaje: getDetail('reembalaje'),
                despacho: getDetail('despacho'),
                empresa: getDetail('empresa'),
                planta: getDetail('planta'),
                temporada: getDetail('temporada'),
                ingreso: getDetail('ingreso'),
                modificacion: getDetail('modificacion')
            };

            var now = new Date();
            var fecha = now.toLocaleDateString();
            var hora = now.toLocaleTimeString();
            var logoUrl = LOGO_EMPRESA ? new URL(LOGO_EMPRESA, window.location.href).href : '';
            var tituloEmpresa = NOMBRE_EMPRESA || 'Informe de detalle';

            var contenido = `
                <div class="report">
                    <div class="header">
                        <div class="logo">${logoUrl ? `<img src="${logoUrl}" alt="Logo empresa">` : ''}</div>
                        <div class="title">
                            <h2>${tituloEmpresa}</h2>
                            <p class="subtitle">Historial de existencias industrial</p>
                        </div>
                        <div class="meta">
                            <p><strong>Fecha:</strong> ${fecha}</p>
                            <p><strong>Hora:</strong> ${hora}</p>
                        </div>
                    </div>
                    <div class="section">
                        <h3>Identificación</h3>
                        <table class="info-table">
                            <tbody>
                                <tr><th>Folio original</th><td>${detalle.folio}</td></tr>
                                <tr><th>Folio nuevo</th><td>${detalle.folioAux}</td></tr>
                                <tr><th>Estado</th><td>${detalle.estado}</td></tr>
                                <tr><th>Estado calidad</th><td>${detalle.estadoCalidad}</td></tr>
                                <tr><th>Estandar</th><td>${detalle.estandar}</td></tr>
                                <tr><th>Especie / Variedad</th><td>${detalle.especie}</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="section">
                        <h3>Productor y manejo</h3>
                        <table class="info-table">
                            <tbody>
                                <tr><th>Productor</th><td>${detalle.productor}</td></tr>
                                <tr><th>Cantidad</th><td>${detalle.envases}</td></tr>
                                <tr><th>Kilos</th><td>${detalle.kilos}</td></tr>
                                <tr><th>Manejo</th><td>${detalle.manejo}</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="section">
                        <h3>Movimientos</h3>
                        <table class="info-table">
                            <tbody>
                                <tr><th>Recepción</th><td>${detalle.recepcion}</td></tr>
                                <tr><th>Guía recepción</th><td>${detalle.guiaRecepcion}</td></tr>
                                <tr><th>Proceso</th><td>${detalle.proceso}</td></tr>
                                <tr><th>Reembalaje</th><td>${detalle.reembalaje}</td></tr>
                                <tr><th>Despacho</th><td>${detalle.despacho}</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="section">
                        <h3>Ubicación y fechas</h3>
                        <table class="info-table">
                            <tbody>
                                <tr><th>Empresa</th><td>${detalle.empresa}</td></tr>
                                <tr><th>Planta</th><td>${detalle.planta}</td></tr>
                                <tr><th>Temporada</th><td>${detalle.temporada}</td></tr>
                                <tr><th>Ingreso</th><td>${detalle.ingreso}</td></tr>
                                <tr><th>Modificación</th><td>${detalle.modificacion}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            `;

            var printWindow = window.open('', '', 'width=900,height=700');
            printWindow.document.write('<html><head><title>Detalle existencia</title>');
            printWindow.document.write('<style>@page{size:Letter;margin:12mm 10mm;} body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0;color:#0f375b;} .report{padding:12px 10px;} h2{margin:0;font-size:20px;color:#0f375b;} h3{margin:14px 0 8px;font-size:14px;color:#0f375b;border-bottom:1px solid #c5ddf5;padding-bottom:6px;} p{margin:2px 0;} .subtitle{color:#4f709c;} .header{display:grid;grid-template-columns:120px 1fr auto;grid-gap:10px;align-items:center;border-bottom:2px solid #1b75bb;padding-bottom:10px;margin-bottom:10px;} .logo img{max-height:60px;max-width:120px;object-fit:contain;} .title{align-self:start;} .meta{text-align:right;font-size:11px;color:#4f709c;} .section{margin-bottom:12px;} .info-table{width:100%;border-collapse:collapse;font-size:12px;} .info-table th{width:34%;text-align:left;background:#e8f2fb;color:#0f375b;padding:6px;border:1px solid #d9e6f5;font-weight:700;} .info-table td{padding:6px;border:1px solid #d9e6f5;vertical-align:top;} </style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(contenido);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }
    </script>
</body>

</html>