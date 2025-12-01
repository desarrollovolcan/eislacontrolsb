<?php

include_once "../../assest/config/validarUsuarioExpo.php";

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES
include_once '../../assest/controlador/RECEPCIONMP_ADO.php';
include_once '../../assest/controlador/ERECEPCION_ADO.php';
include_once '../../assest/controlador/PRODUCTOR_ADO.php';
include_once '../../assest/controlador/VESPECIES_ADO.php';
include_once '../../assest/controlador/ESPECIES_ADO.php';
include_once '../../assest/controlador/TPROCESO_ADO.php';
include_once '../../assest/controlador/PROCESO_ADO.php';
include_once '../../assest/controlador/DESPACHOMP_ADO.php';
include_once '../../assest/controlador/TMANEJO_ADO.php';
include_once '../../assest/controlador/COMPRADOR_ADO.php';
include_once '../../assest/controlador/TTRATAMIENTO1_ADO.php';
include_once '../../assest/controlador/TTRATAMIENTO2_ADO.php';
include_once '../../assest/controlador/EMPRESA_ADO.php';

include_once '../../assest/controlador/EXIMATERIAPRIMA_ADO.php';

//INCIALIZAR LAS VARIBLES
//INICIALIZAR CONTROLADOR
$RECEPCIONMP_ADO =  new RECEPCIONMP_ADO();
$ERECEPCION_ADO =  new ERECEPCION_ADO();
$PRODUCTOR_ADO =  new PRODUCTOR_ADO();
$VESPECIES_ADO =  new VESPECIES_ADO();
$ESPECIES_ADO =  new ESPECIES_ADO();
$DESPACHOMP_ADO =  new DESPACHOMP_ADO();
$TPROCESO_ADO =  new TPROCESO_ADO();
$PROCESO_ADO =  new PROCESO_ADO();
$TMANEJO_ADO =  new TMANEJO_ADO();
$COMPRADOR_ADO =  new COMPRADOR_ADO();
$TTRATAMIENTO1_ADO =  new TTRATAMIENTO1_ADO();
$TTRATAMIENTO2_ADO =  new TTRATAMIENTO2_ADO();
$EMPRESA_ADO = new EMPRESA_ADO();

$EXIMATERIAPRIMA_ADO =  new EXIMATERIAPRIMA_ADO();

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

$ESTADOS = [
    0 => "Eliminado",
    1 => "Ingresando",
    2 => "Disponible",
    3 => "En Proceso",
    4 => "Procesado",
    5 => "En Repaletizaje",
    6 => "Repaletizado",
    7 => "En Despacho",
    8 => "Despachado",
    9 => "En Transito",
    10 => "En Rechazo",
    11 => "Rechazado",
    12 => "En Levantamiento",
];

$COLORCALIDAD = [
    1 => ['clase' => 'badge badge-danger', 'nombre' => 'Rechazado'],
    2 => ['clase' => 'badge badge-warning', 'nombre' => 'Objetado'],
    3 => ['clase' => 'badge badge-success', 'nombre' => 'Levantado'],
];

//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD
//INICIALIZAR ARREGLOS
$ARRAYEXIMATERIAPRIMA = "";
$ARRAYEMPRESA = [];

//CACHES PARA REDUCIR CONSULTAS
$PRODUCTOR_CACHE = [];
$VESPECIES_CACHE = [];
$ESPECIES_CACHE = [];
$ESTANDAR_CACHE = [];
$RECEPCION_CACHE = [];
$PROCESO_CACHE = [];
$TPROCESO_CACHE = [];
$DESPACHO_CACHE = [];
$DESPACHO_INTERPLANTA_CACHE = [];
$PLANTA_CACHE = [];
$EMPRESA_CACHE = [];
$TEMPORADA_CACHE = [];
$TMANEJO_CACHE = [];
$TRATAMIENTO1_CACHE = [];
$TRATAMIENTO2_CACHE = [];
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
    $ARRAYEXIMATERIAPRIMA = $EXIMATERIAPRIMA_ADO->listarEximateriaprimaEmpresaTemporada($EMPRESAS,  $TEMPORADAS);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title> Historial Existencia MP</title>
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

        .detalle-resumen {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 8px;
            margin: 0;
        }

        .detalle-resumen .resumen-wrapper {
            display: flex;
            width: 100%;
            height: 100%;
        }

        .detalle-resumen .resumen-item {
            background: #f7f9fc;
            border: 1px solid #dbe3ef;
            border-radius: 8px;
            padding: 8px 10px 10px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            height: 100%;
            min-height: 150px;
            justify-content: space-between;
        }

        .detalle-resumen .resumen-inline {
            flex-direction: row;
            align-items: flex-start;
            gap: 8px;
            flex-wrap: wrap;
        }

        .detalle-resumen .estado-line {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
            flex: 1 1 180px;
            min-width: 0;
        }

        .detalle-resumen .resumen-label {
            font-size: 11px;
            letter-spacing: 0.2px;
            color: #4b6280;
            text-transform: uppercase;
            margin: 0;
        }

        .detalle-resumen .resumen-valor {
            font-size: 15px;
            font-weight: 700;
            color: #0f2d4a;
            margin: 0;
            word-break: break-word;
        }

        .detalle-resumen .resumen-btn {
            align-self: flex-start;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
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
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .detalle-table {
            width: 100%;
            margin: 0;
            table-layout: fixed;
        }

        .detalle-card .detalle-table {
            flex: 1;
        }

        .detalle-table th,
        .detalle-table td {
            padding: 6px 10px;
            font-size: 12px;
            color: #12263f;
            vertical-align: top;
            white-space: normal;
            word-break: break-word;
        }

        .badge-amarillo {
            background: #f7c948;
            color: #7a4b00;
        }

        .badge-azul {
            background: #e5efff;
            color: #0f4a7a;
        }

        .badge-celeste {
            background: #d5f5ff;
            color: #0b5b73;
        }

        .detalle-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .detalle-table th {
            width: 46%;
            padding: 6px 8px;
            font-size: 12px;
            font-weight: 600;
            color: #304a6b;
            background: #f5f7fb;
            border-bottom: 1px solid #e7ecf4;
        }

        .detalle-table td {
            padding: 6px 8px;
            font-size: 12.5px;
            color: #0f2d4a;
            border-bottom: 1px solid #eef2f7;
            word-break: break-word;
        }

        .detalle-table tr:last-child th,
        .detalle-table tr:last-child td {
            border-bottom: none;
        }

        .detalle-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            background: #e7f0fb;
            color: #0f4a7a;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            border: 1px solid #c7d6ea;
        }

        .detalle-estado-calidad {
            background: #f4f6fa;
            color: #0f2d4a;
            border-color: #d6deea;
        }

        .detalle-modal .modal-footer {
            border-top: 1px solid #d0d7e3;
            padding: 12px 14px;
            background: #fff;
        }

        .detalle-modal .btn-primary {
            background: #0c63a8;
            border: 1px solid #0a588f;
            box-shadow: none;
            font-weight: 700;
        }

        .detalle-modal .btn-secondary {
            color: #0f2d4a !important;
            border: 1px solid #b7c7de;
            background: #e8f0f9;
            font-weight: 700;
            box-shadow: none;
        }

        .detalle-modal .btn {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
        }

        .mov-link {
            color: #0c63a8;
            font-weight: 700;
            text-decoration: none;
        }

        .mov-link:hover,
        .mov-link:focus {
            text-decoration: underline;
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
                            <h3 class="page-title">Historial Existencias</h3>
                            <div class="d-inline-block align-items-center">
                                <nav>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                        <li class="breadcrumb-item" aria-current="page">Modulo</li>
                                        <li class="breadcrumb-item" aria-current="page">Existencia</li>
                                        <li class="breadcrumb-item active" aria-current="page"> <a href="#">Historial Existencia Materia Prima </a> </li>
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
                                                    <th>Fecha Cosecha </th>
                                                    <th>Estado </th>
                                                    <th>Estado Calidad</th>
                                                    <th>CSG</th>
                                                    <th>Productor</th>
                                                    <th class="d-none export-only">Especies</th>
                                                    <th>Variedad</th>
                                                    <th>Cantidad Envase</th>
                                                    <th>Kilos Neto</th>
                                                    <th>Número Recepción </th>
                                                    <th>Número Proceso </th>
                                                    <th>Número Despacho </th>
                                                    <th>Tipo Manejo</th>
                                                    <th>Gasificación</th>
                                                    <th>Días</th>
                                                    <th>Ingreso</th>
                                                    <th>Modificación</th>
                                                    <th class="d-none export-only">Empresa</th>
                                                    <th class="d-none export-only">Planta</th>
                                                    <th class="d-none export-only">Temporada</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($ARRAYEXIMATERIAPRIMA as $r) : ?>
                                                    <?php
                                                    $estado = $ESTADOS[$r['ESTADO']] ?? 'Sin Datos';
                                                    $color = $COLORCALIDAD[$r['COLOR']] ?? ['clase' => '', 'nombre' => 'Sin Datos'];

                                                    $recepcion = obtenerDesdeCache($r['ID_RECEPCION'], $RECEPCION_CACHE, fn($idRecepcion) => $RECEPCIONMP_ADO->verRecepcion2($idRecepcion));
                                                    $despachoInterplanta = obtenerDesdeCache($r['ID_DESPACHO2'], $DESPACHO_INTERPLANTA_CACHE, fn($idDespacho) => $DESPACHOMP_ADO->verDespachomp2($idDespacho));
                                                    $numRecepcion = "Sin Datos";
                                                    $fechaRecepcion = "";
                                                    $numGuiaRecepcion = "Sin Datos";
                                                    $fechaGuiaRecepcion = "";
                                                    $tipoRecepcion = "Sin Datos";
                                                    $origenRecepcion = "Sin Datos";
                                                    $csgOrigen = "Sin Datos";

                                                    if ($recepcion) {
                                                        $numRecepcion = $recepcion[0]["NUMERO_RECEPCION"];
                                                        $fechaRecepcion = $recepcion[0]["FECHA"];
                                                        $numGuiaRecepcion = $recepcion[0]["NUMERO_GUIA_RECEPCION"];
                                                        $fechaGuiaRecepcion = $recepcion[0]["GUIA"];
                                                        if ($recepcion[0]["TRECEPCION"] == 1) {
                                                            $tipoRecepcion = "Desde Productor";
                                                            $productorOrigen = obtenerDesdeCache($recepcion[0]['ID_PRODUCTOR'], $PRODUCTOR_CACHE, fn($idProductor) => $PRODUCTOR_ADO->verProductor($idProductor));
                                                            if ($productorOrigen) {
                                                                $csgOrigen = $productorOrigen[0]['CSG_PRODUCTOR'];
                                                                $origenRecepcion = $productorOrigen[0]['NOMBRE_PRODUCTOR'];
                                                            }
                                                        } elseif ($recepcion[0]["TRECEPCION"] == 2) {
                                                            $tipoRecepcion = "Planta Externa";
                                                            $plantaOrigen = obtenerDesdeCache($recepcion[0]['ID_PLANTA2'], $PLANTA_CACHE, fn($idPlanta) => $PLANTA_ADO->verPlanta($idPlanta));
                                                            if ($plantaOrigen) {
                                                                $origenRecepcion = $plantaOrigen[0]['NOMBRE_PLANTA'];
                                                                $csgOrigen = $plantaOrigen[0]['CODIGO_SAG_PLANTA'];
                                                            }
                                                        } elseif ($recepcion[0]["TRECEPCION"] == 3) {
                                                            $tipoRecepcion = "Desde Productor BDH";
                                                            $productorOrigen = obtenerDesdeCache($recepcion[0]['ID_PRODUCTOR'], $PRODUCTOR_CACHE, fn($idProductor) => $PRODUCTOR_ADO->verProductor($idProductor));
                                                            if ($productorOrigen) {
                                                                $csgOrigen = $productorOrigen[0]['CSG_PRODUCTOR'];
                                                                $origenRecepcion = $productorOrigen[0]['NOMBRE_PRODUCTOR'];
                                                            }
                                                        }
                                                    } elseif ($despachoInterplanta) {
                                                        $numRecepcion = $despachoInterplanta[0]["NUMERO_DESPACHO"];
                                                        $fechaRecepcion = $despachoInterplanta[0]["FECHA"];
                                                        $numGuiaRecepcion = $despachoInterplanta[0]["NUMERO_GUIA_DESPACHO"];
                                                        $tipoRecepcion = "Interplanta";
                                                        $plantaOrigen = obtenerDesdeCache($despachoInterplanta[0]['ID_PLANTA'], $PLANTA_CACHE, fn($idPlanta) => $PLANTA_ADO->verPlanta($idPlanta));
                                                        if ($plantaOrigen) {
                                                            $origenRecepcion = $plantaOrigen[0]['NOMBRE_PLANTA'];
                                                            $csgOrigen = $plantaOrigen[0]['CODIGO_SAG_PLANTA'];
                                                        }
                                                    }

                                                    $proceso = obtenerDesdeCache($r['ID_PROCESO'], $PROCESO_CACHE, fn($idProceso) => $PROCESO_ADO->verProceso2($idProceso));
                                                    $numProceso = $proceso[0]["NUMERO_PROCESO"] ?? "Sin datos";
                                                    $fechaProceso = $proceso[0]["FECHA"] ?? "";
                                                    $tipoProceso = "Sin datos";
                                                    if ($proceso) {
                                                        $tipoProcesoInfo = obtenerDesdeCache($proceso[0]["ID_TPROCESO"], $TPROCESO_CACHE, fn($idTipoProceso) => $TPROCESO_ADO->verTproceso($idTipoProceso));
                                                        if ($tipoProcesoInfo) {
                                                            $tipoProceso = $tipoProcesoInfo[0]["NOMBRE_TPROCESO"];
                                                        }
                                                    }

                                                    $despacho = obtenerDesdeCache($r['ID_DESPACHO'], $DESPACHO_CACHE, fn($idDespacho) => $DESPACHOMP_ADO->verDespachomp2($idDespacho));
                                                    $numDespacho = "Sin Datos";
                                                    $fechaDespacho = "";
                                                    $numGuiaDespacho = "Sin Datos";
                                                    $tipoDespacho = "Sin datos";
                                                    $destino = "Sin datos";
                                                    $csgDestino = "Sin Datos";
                                                    if ($despacho) {
                                                        $numDespacho = $despacho[0]["NUMERO_DESPACHO"];
                                                        $fechaDespacho = $despacho[0]["FECHA"];
                                                        $numGuiaDespacho = $despacho[0]["NUMERO_GUIA_DESPACHO"];
                                                        if ($despacho[0]['TDESPACHO'] == "1") {
                                                            $tipoDespacho = "Interplanta";
                                                            $plantaDestino = obtenerDesdeCache($despacho[0]['ID_PLANTA2'], $PLANTA_CACHE, fn($idPlanta) => $PLANTA_ADO->verPlanta($idPlanta));
                                                            if ($plantaDestino) {
                                                                $destino = $plantaDestino[0]['NOMBRE_PLANTA'];
                                                                $csgDestino = $plantaDestino[0]['CODIGO_SAG_PLANTA'];
                                                            }
                                                        } elseif ($despacho[0]['TDESPACHO'] == "2") {
                                                            $tipoDespacho = "Devolución Productor";
                                                            $productorDestino = obtenerDesdeCache($despacho[0]['ID_PRODUCTOR'], $PRODUCTOR_CACHE, fn($idProductor) => $PRODUCTOR_ADO->verProductor($idProductor));
                                                            if ($productorDestino) {
                                                                $destino = $productorDestino[0]['NOMBRE_PRODUCTOR'];
                                                                $csgDestino = $productorDestino[0]['CSG_PRODUCTOR'];
                                                            }
                                                        } elseif ($despacho[0]['TDESPACHO'] == "3") {
                                                            $tipoDespacho = "Venta";
                                                            $comprador = obtenerDesdeCache($despacho[0]['ID_COMPRADOR'], $COMPRADOR_CACHE, fn($idComprador) => $COMPRADOR_ADO->verComprador($idComprador));
                                                            if ($comprador) {
                                                                $destino = $comprador[0]['NOMBRE_COMPRADOR'];
                                                                $csgDestino = "No Aplica";
                                                            }
                                                        } elseif ($despacho[0]['TDESPACHO'] == "4") {
                                                            $tipoDespacho = "Despacho de Descarte";
                                                            $destino = $despacho[0]['REGALO_DESPACHO'];
                                                            $csgDestino = "No Aplica";
                                                            $numGuiaDespacho = "No Aplica";
                                                        } elseif ($despacho[0]['TDESPACHO'] == "5") {
                                                            $tipoDespacho = "Planta Externa";
                                                            $plantaDestino = obtenerDesdeCache($despacho[0]['ID_PLANTA3'], $PLANTA_CACHE, fn($idPlanta) => $PLANTA_ADO->verPlanta($idPlanta));
                                                            if ($plantaDestino) {
                                                                $destino = $plantaDestino[0]['NOMBRE_PLANTA'];
                                                                $csgDestino = $plantaDestino[0]['CODIGO_SAG_PLANTA'];
                                                            }
                                                        }
                                                    }

                                                    $productor = obtenerDesdeCache($r['ID_PRODUCTOR'], $PRODUCTOR_CACHE, fn($idProductor) => $PRODUCTOR_ADO->verProductor($idProductor));
                                                    $csgProductor = $productor[0]['CSG_PRODUCTOR'] ?? "Sin Datos";
                                                    $nombreProductor = $productor[0]['NOMBRE_PRODUCTOR'] ?? "Sin Datos";

                                                    $estandar = obtenerDesdeCache($r['ID_ESTANDAR'], $ESTANDAR_CACHE, fn($idEstandar) => $ERECEPCION_ADO->verEstandar($idEstandar));
                                                    $codigoEstandar = $estandar[0]['CODIGO_ESTANDAR'] ?? "Sin Datos";
                                                    $nombreEstandar = $estandar[0]['NOMBRE_ESTANDAR'] ?? "Sin Datos";

                                                    $vespecies = obtenerDesdeCache($r['ID_VESPECIES'], $VESPECIES_CACHE, fn($idVespecies) => $VESPECIES_ADO->verVespecies($idVespecies));
                                                    $nombreVariedad = $vespecies[0]['NOMBRE_VESPECIES'] ?? "Sin Datos";
                                                    $especies = null;
                                                    if ($vespecies) {
                                                        $especies = obtenerDesdeCache($vespecies[0]['ID_ESPECIES'], $ESPECIES_CACHE, fn($idEspecie) => $ESPECIES_ADO->verEspecies($idEspecie));
                                                    }
                                                    $nombreEspecie = $especies[0]['NOMBRE_ESPECIES'] ?? "Sin Datos";

                                                    $tManejo = obtenerDesdeCache($r['ID_TMANEJO'], $TMANEJO_CACHE, fn($idManejo) => $TMANEJO_ADO->verTmanejo($idManejo));
                                                    $nombreTManejo = $tManejo[0]['NOMBRE_TMANEJO'] ?? "Sin Datos";

                                                    $tratamiento1 = obtenerDesdeCache($r['ID_TTRATAMIENTO1'], $TRATAMIENTO1_CACHE, fn($idTratamiento) => $TTRATAMIENTO1_ADO->verTtratamiento($idTratamiento));
                                                    $nombreTratamiento1 = $tratamiento1[0]["NOMBRE_TTRATAMIENTO"] ?? "Sin Datos";

                                                    $tratamiento2 = obtenerDesdeCache($r['ID_TTRATAMIENTO2'], $TRATAMIENTO2_CACHE, fn($idTratamiento) => $TTRATAMIENTO2_ADO->verTtratamiento($idTratamiento));
                                                    $nombreTratamiento2 = $tratamiento2[0]["NOMBRE_TTRATAMIENTO"] ?? "Sin Datos";

                                                    $empresa = obtenerDesdeCache($r['ID_EMPRESA'], $EMPRESA_CACHE, fn($idEmpresa) => $EMPRESA_ADO->verEmpresa($idEmpresa));
                                                    $nombreEmpresa = $empresa[0]['NOMBRE_EMPRESA'] ?? "Sin Datos";

                                                    $planta = obtenerDesdeCache($r['ID_PLANTA'], $PLANTA_CACHE, fn($idPlanta) => $PLANTA_ADO->verPlanta($idPlanta));
                                                    $nombrePlanta = $planta[0]['NOMBRE_PLANTA'] ?? "Sin Datos";

                                                    $temporada = obtenerDesdeCache($r['ID_TEMPORADA'], $TEMPORADA_CACHE, fn($idTemporada) => $TEMPORADA_ADO->verTemporada($idTemporada));
                                                    $nombreTemporada = $temporada[0]['NOMBRE_TEMPORADA'] ?? "Sin Datos";

                                                    $gasificado = $r['GASIFICADO'] === "1" ? "SI" : ($r['GASIFICADO'] === "0" ? "NO" : "Sin Datos");

                                                    ?>
                                                    <tr class="text-center">
                                                        <td>
                                                            <button type="button" class="btn btn-info btn-sm detalle-existencia" data-toggle="modal" data-target="#detalleExistenciaModal"
                                                                data-folio="<?php echo htmlspecialchars($r['FOLIO_EXIMATERIAPRIMA'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-folio-aux="<?php echo htmlspecialchars($r['FOLIO_AUXILIAR_EXIMATERIAPRIMA'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-estado="<?php echo htmlspecialchars($estado, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-estado-calidad="<?php echo htmlspecialchars($color['nombre'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-estandar="<?php echo htmlspecialchars($codigoEstandar . ' - ' . $nombreEstandar, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-csg="<?php echo htmlspecialchars($csgProductor, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-productor="<?php echo htmlspecialchars($nombreProductor, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-especie="<?php echo htmlspecialchars($nombreEspecie, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-variedad="<?php echo htmlspecialchars($nombreVariedad, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-envases="<?php echo htmlspecialchars($r['ENVASE'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-neto="<?php echo htmlspecialchars($r['NETO'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-promedio="<?php echo htmlspecialchars($r['PROMEDIO'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-bruto="<?php echo htmlspecialchars($r['BRUTO'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-num-recepcion="<?php echo htmlspecialchars($numRecepcion, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-id-recepcion="<?php echo htmlspecialchars($r['ID_RECEPCION'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-fecha-recepcion="<?php echo htmlspecialchars($fechaRecepcion, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-tipo-recepcion="<?php echo htmlspecialchars($tipoRecepcion, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-csg-origen="<?php echo htmlspecialchars($csgOrigen, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-origen="<?php echo htmlspecialchars($origenRecepcion, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-num-guia-recepcion="<?php echo htmlspecialchars($numGuiaRecepcion, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-fecha-guia-recepcion="<?php echo htmlspecialchars($fechaGuiaRecepcion, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-num-proceso="<?php echo htmlspecialchars($numProceso, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-id-proceso="<?php echo htmlspecialchars($r['ID_PROCESO'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-fecha-proceso="<?php echo htmlspecialchars($fechaProceso, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-tipo-proceso="<?php echo htmlspecialchars($tipoProceso, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-num-despacho="<?php echo htmlspecialchars($numDespacho, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-id-despacho="<?php echo htmlspecialchars($r['ID_DESPACHO'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-fecha-despacho="<?php echo htmlspecialchars($fechaDespacho, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-num-guia-despacho="<?php echo htmlspecialchars($numGuiaDespacho, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-tipo-despacho="<?php echo htmlspecialchars($tipoDespacho, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-csg-destino="<?php echo htmlspecialchars($csgDestino, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-destino="<?php echo htmlspecialchars($destino, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-tmanejo="<?php echo htmlspecialchars($nombreTManejo, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-tratamiento1="<?php echo htmlspecialchars($nombreTratamiento1, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-tratamiento2="<?php echo htmlspecialchars($nombreTratamiento2, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-gasificado="<?php echo htmlspecialchars($gasificado, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-empresa="<?php echo htmlspecialchars($nombreEmpresa, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-planta="<?php echo htmlspecialchars($nombrePlanta, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-temporada="<?php echo htmlspecialchars($nombreTemporada, ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-ingreso="<?php echo htmlspecialchars($r['INGRESO'], ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-modificacion="<?php echo htmlspecialchars($r['MODIFICACION'], ENT_QUOTES, 'UTF-8'); ?>">
                                                                Ver detalle
                                                            </button>
                                                        </td>
                                                        <td>
                                                            <span class="<?php echo $color['clase']; ?>">
                                                                <?php echo $r['FOLIO_EXIMATERIAPRIMA']; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="<?php echo $color['clase']; ?>">
                                                                <?php echo $r['FOLIO_AUXILIAR_EXIMATERIAPRIMA']; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $r['COSECHA']; ?></td>
                                                        <td><?php echo $estado; ?></td>
                                                        <td><?php echo $color['nombre']; ?></td>
                                                        <td><?php echo $csgProductor; ?></td>
                                                        <td><?php echo $nombreProductor; ?></td>
                                                        <td class="d-none export-only"><?php echo $nombreEspecie; ?></td>
                                                        <td><?php echo $nombreVariedad; ?></td>
                                                        <td><?php echo $r['ENVASE']; ?></td>
                                                        <td><?php echo $r['NETO']; ?></td>
                                                        <td><?php echo $numRecepcion; ?></td>
                                                        <td><?php echo $numProceso; ?></td>
                                                        <td><?php echo $numDespacho; ?></td>
                                                        <td><?php echo $nombreTManejo; ?></td>
                                                        <td><?php echo $gasificado; ?></td>
                                                        <td><?php echo $r['DIAS']; ?></td>
                                                        <td><?php echo $r['INGRESO']; ?></td>
                                                        <td><?php echo $r['MODIFICACION']; ?></td>
                                                        <td class="d-none export-only"><?php echo $nombreEmpresa; ?></td>
                                                        <td class="d-none export-only"><?php echo $nombrePlanta; ?></td>
                                                        <td class="d-none export-only"><?php echo $nombreTemporada; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="text-center" id="filtro">
                                                    <th class="no-export">Detalle</th>
                                                    <th>Folio Original</th>
                                                    <th>Folio Nuevo</th>
                                                    <th>Fecha Cosecha </th>
                                                    <th>Estado </th>
                                                    <th>Estado Calidad</th>
                                                    <th>CSG</th>
                                                    <th>Productor</th>
                                                    <th class="d-none export-only">Especies</th>
                                                    <th>Variedad</th>
                                                    <th>Cantidad Envase</th>
                                                    <th>Kilos Neto</th>
                                                    <th>Número Recepción </th>
                                                    <th>Número Proceso </th>
                                                    <th>Número Despacho </th>
                                                    <th>Tipo Manejo</th>
                                                    <th>Gasificación</th>
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
                        <div class="detalle-resumen mb-2">
                            <div class="resumen-wrapper">
                                <div class="resumen-item w-100">
                                    <p class="resumen-label">Folio original</p>
                                    <p class="resumen-valor" data-detail="folio"></p>
                                    <button type="button" class="btn btn-outline-primary resumen-btn" onclick="imprimirTarja()">Imprimir Trazabilidad</button>
                                </div>
                            </div>
                            <div class="resumen-wrapper">
                                <div class="resumen-item w-100">
                                    <p class="resumen-label">Folio nuevo</p>
                                    <p class="resumen-valor" data-detail="folio-aux"></p>
                                </div>
                            </div>
                            <div class="resumen-wrapper">
                                <div class="resumen-item w-100">
                                    <p class="resumen-label mb-0">Estado</p>
                                    <div class="estado-line mb-0">
                                        <span class="detalle-badge" data-detail="estado"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="resumen-wrapper">
                                <div class="resumen-item w-100">
                                    <p class="resumen-label mb-0">Calidad</p>
                                    <div class="estado-line mb-0">
                                        <span class="detalle-badge detalle-estado-calidad" data-detail="estado-calidad"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="detalle-grid mb-1">
                            <div class="detalle-card">
                                <h5>Identificación <span class="titulo-badge badge-azul">Detalle</span></h5>
                                <table class="detalle-table">
                                    <tr>
                                        <th>Estandar</th>
                                        <td data-detail="estandar"></td>
                                    </tr>
                                    <tr>
                                        <th>Especie / Variedad</th>
                                        <td data-detail="especie"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="detalle-card">
                                <h5>Productor y manejo <span class="titulo-badge badge-amarillo">Proceso</span></h5>
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
                                        <th>Kilos</th>
                                        <td data-detail="kilos"></td>
                                    </tr>
                                    <tr>
                                        <th>Manejo</th>
                                        <td data-detail="manejo"></td>
                                    </tr>
                                    <tr>
                                        <th>Gasificación</th>
                                        <td data-detail="gasificado"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="detalle-card">
                                <h5>Movimientos <span class="titulo-badge badge-azul">Exportación</span></h5>
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
                                        <th>Despacho</th>
                                        <td data-detail="despacho"></td>
                                    </tr>
                                    <tr>
                                        <th>Tratamiento 1</th>
                                        <td data-detail="tratamiento1"></td>
                                    </tr>
                                    <tr>
                                        <th>Tratamiento 2</th>
                                        <td data-detail="tratamiento2"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="detalle-card">
                                <h5>Ubicación y fechas <span class="titulo-badge badge-celeste">Inspección</span></h5>
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
                        <button type="button" class="btn btn-primary" onclick="exportDetallePdf()">Descargar PDF</button>
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
                modal.find('[data-detail="envases"]').text(button.data('envases'));
                modal.find('[data-detail="kilos"]').text('Neto: ' + button.data('neto') + ' | Promedio: ' + button.data('promedio') + ' | Bruto: ' + button.data('bruto'));
                modal.find('[data-detail="manejo"]').text(button.data('tmanejo'));
                modal.find('[data-detail="gasificado"]').text(button.data('gasificado'));
                var recepcionTexto = button.data('tipo-recepcion') + ' #' + button.data('num-recepcion') + ' (' + button.data('fecha-recepcion') + ') ' + button.data('origen') + ' [' + button.data('csg-origen') + ']';
                var recepcionUrl = button.data('id-recepcion') ? '../../fruta/vista/registroRecepcionmp.php?op&id=' + encodeURIComponent(button.data('id-recepcion')) + '&a=ver' : '';
                setDetailWithLink(modal, 'recepcion', recepcionTexto, recepcionUrl);
                modal.find('[data-detail="guia-recepcion"]').text(button.data('num-guia-recepcion') + (button.data('fecha-guia-recepcion') ? ' (' + button.data('fecha-guia-recepcion') + ')' : ''));
                var procesoTexto = button.data('tipo-proceso') + ' #' + button.data('num-proceso') + ' (' + button.data('fecha-proceso') + ')';
                var procesoUrl = button.data('id-proceso') ? '../../fruta/vista/registroProceso.php?op&id=' + encodeURIComponent(button.data('id-proceso')) + '&a=ver' : '';
                setDetailWithLink(modal, 'proceso', procesoTexto, procesoUrl);
                var despachoTexto = button.data('tipo-despacho') + ' #' + button.data('num-despacho') + ' (' + button.data('fecha-despacho') + ') ' + button.data('destino') + ' [' + button.data('csg-destino') + ']';
                var despachoUrl = button.data('id-despacho') ? '../../fruta/vista/registroDespachomp.php?op&id=' + encodeURIComponent(button.data('id-despacho')) + '&a=ver' : '';
                setDetailWithLink(modal, 'despacho', despachoTexto, despachoUrl);
                modal.find('[data-detail="tratamiento1"]').text(button.data('tratamiento1'));
                modal.find('[data-detail="tratamiento2"]').text(button.data('tratamiento2'));
                modal.find('[data-detail="empresa"]').text(button.data('empresa'));
                modal.find('[data-detail="planta"]').text(button.data('planta'));
                modal.find('[data-detail="temporada"]').text(button.data('temporada'));
                modal.find('[data-detail="ingreso"]').text(button.data('ingreso'));
                modal.find('[data-detail="modificacion"]').text(button.data('modificacion'));
            });
        });

        function imprimirTarja() {
            exportDetallePdf();
        }

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
                gasificado: getDetail('gasificado'),
                recepcion: getDetail('recepcion'),
                guiaRecepcion: getDetail('guia-recepcion'),
                proceso: getDetail('proceso'),
                despacho: getDetail('despacho'),
                tratamiento1: getDetail('tratamiento1'),
                tratamiento2: getDetail('tratamiento2'),
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
                            <p class="subtitle">Historial de existencias de materia prima</p>
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
                                <tr><th>Gasificación</th><td>${detalle.gasificado}</td></tr>
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
                                <tr><th>Despacho</th><td>${detalle.despacho}</td></tr>
                                <tr><th>Tratamiento 1</th><td>${detalle.tratamiento1}</td></tr>
                                <tr><th>Tratamiento 2</th><td>${detalle.tratamiento2}</td></tr>
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
