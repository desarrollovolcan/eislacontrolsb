<?php

include_once "../../assest/config/validarUsuarioFruta.php";

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES

include_once '../../assest/controlador/PRODUCTOR_ADO.php';
include_once '../../assest/controlador/VESPECIES_ADO.php';
include_once '../../assest/controlador/PROCESO_ADO.php';
include_once '../../assest/controlador/ERECEPCION_ADO.php';
include_once '../../assest/controlador/FOLIO_ADO.php';
include_once '../../assest/controlador/TMANEJO_ADO.php';
include_once '../../assest/controlador/TCALIBRE_ADO.php';
include_once '../../assest/controlador/TEMBALAJE_ADO.php';

include_once '../../assest/controlador/EXIMATERIAPRIMA_ADO.php';
include_once '../../assest/controlador/PCDESPACHOMP_ADO.php';
include_once '../../assest/controlador/RECEPCIONMP_ADO.php';
include_once '../../assest/controlador/ESPECIES_ADO.php';

include_once '../../assest/modelo/PCDESPACHOMP.php';
include_once '../../assest/modelo/EXIMATERIAPRIMA.php';

//INICIALIZAR CONTROLADOR


$PRODUCTOR_ADO =  new PRODUCTOR_ADO();
$VESPECIES_ADO =  new VESPECIES_ADO();
$EXIMATERIAPRIMA_ADO =  new EXIMATERIAPRIMA_ADO();
$FOLIO_ADO =  new FOLIO_ADO();
$TMANEJO_ADO =  new TMANEJO_ADO();
$TCALIBRE_ADO =  new TCALIBRE_ADO();
$TEMBALAJE_ADO =  new TEMBALAJE_ADO();
$RECEPCIONMP_ADO =  new RECEPCIONMP_ADO();
$ESPECIES_ADO =  new ESPECIES_ADO();



$ERECEPCION_ADO =  new ERECEPCION_ADO();
$PCDESPACHOMP_ADO =  new PCDESPACHOMP_ADO();

//INIICIALIZAR MODELO
$PCDESPACHOMP =  new PCDESPACHOMP();
$EXIMATERIAPRIMA =  new EXIMATERIAPRIMA();

// MANEJO DE PETICION AJAX PARA FILTRAR VARIEDADES
if (isset($_POST['ajax_filtrar_variedades'])) {
    $id_estandar = $_POST['id_estandar'];
    $empresa = $_POST['empresa'];
    
    $ARRAYVERESTANDAR = $ERECEPCION_ADO->verEstandar($id_estandar);
    if ($ARRAYVERESTANDAR) {
        $ARRAYVESPECIES = $VESPECIES_ADO->buscarVespeciesPorEspeciesPorEmpresaCBX($ARRAYVERESTANDAR[0]['ID_ESPECIES'], $empresa);
        echo json_encode($ARRAYVESPECIES);
    } else {
        echo json_encode([]);
    }
    exit;
}

//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD

$NUMERO = "";
$NUMEROVER = "";

$IDQUITAR = "";
$FECHAINGRESOPCDESPACHO = "";
$FECHAMODIFCIACIONPCDESPACHO = "";

$IDPCDESPACHO = "";
$MOTIVOPCDESPACHO = "";
$ID_PRODUCTOR = "";
$ID_ESTANDAR = "";
$ID_VARIEDAD = "";
$FECHAPCDESPACHO = "";
$ESTADO = "";

$TOTALENVASE = 0;
$TOTALNETO = 0;

$TOTALENVASEV = 0;
$TOTALNETOV = 0;

$EMPRESA = "";
$PLANTA = "";
$TEMPORADA = "";

$DISABLED0 = "disabled";
$DISABLED = "";
$DISABLED2 = "";
$DISABLED_MOTIVO = ""; // Variable para manejar el motivo por separado
$DISABLED_SELECCION = ""; // Variable para manejar el botón Selección Existencias
$DISABLED3 = "";
$DISABLEDSTYLE0 = "style='background-color: #eeeeee;'";
$DISABLEDSTYLE = "";
$DISABLEDSTYLE2 = "";
$DISABLEDSTYLE_MOTIVO = ""; // Estilo para el motivo

$MENSAJE0 = "";
$MENSAJE = "";
$MENSAJE2 = "";
$MENSAJEVALIDATO = "";

$TOTALENVASE = "";
$TOTALNETO = "";

$IDOP = "";
$OP = "";
$SINO = "";

//INICIALIZAR ARREGLOS



$ARRAYTOMADO = "";
$ARRAYTOMADOPCDESPACHO = "";
$ARRAYTOMADOTOTALES = "";
$ARRAYTOMADOTOTALES2 = "";

$ARRAYEMPRESA = "";
$ARRAYPLANTA = "";
$ARRAYTEMPORADA = "";
$ARRAYEXIEXPORTACION = "";
$ARRAYPRODUCTOR = "";
$ARRAYESTANDAR = "";
$ARRAYVARIEDAD = "";


//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS 
$ARRAYEMPRESA = $EMPRESA_ADO->listarEmpresaCBX();
$ARRAYPLANTA = $PLANTA_ADO->listarPlantaCBX();
$ARRAYTEMPORADA = $TEMPORADA_ADO->listarTemporadaCBX();

$ARRAYPRODUCTOR = $PRODUCTOR_ADO->listarProductorPorEmpresaCBX($EMPRESAS);
$ARRAYESTANDAR = $ERECEPCION_ADO->listarEstandarPorEmpresaCBX($EMPRESAS);
$ARRAYVARIEDAD = $VESPECIES_ADO->listarVespeciesCBX();
$ARRAYFECHAACTUAL = $PCDESPACHOMP_ADO->obtenerFecha();
$FECHAPCDESPACHO = $ARRAYFECHAACTUAL[0]['FECHA'];
include_once "../../assest/config/validarDatosUrl.php";
include_once "../../assest/config/datosUrlD.php";




//OBTENCION DE DATOS ENVIADOR A LA URL

if (isset($_GET["id"])) {
    $id_dato = $_GET["id"];
}else{
    $id_dato = "";
}


if (isset($_GET["a"])) {
    $accion_dato = $_GET["a"];
}else{
    $accion_dato = "";
}
//PARA OPERACIONES DE EDICION , VISUALIZACION Y CREACION
if (isset($id_dato) && isset($accion_dato)) {
    //ALMACENAR DATOS DE VARIABLES DE LA URL
    $IDOP = $id_dato;
    $OP = $accion_dato;

    $ARRAYTOMADO = $EXIMATERIAPRIMA_ADO->buscarPorPcdespacho2($IDOP);
    $ARRAYTOMADOTOTALES = $EXIMATERIAPRIMA_ADO->obtenerTotalesPorPcdespacho($EMPRESAS, $PLANTAS, $TEMPORADAS, $IDOP);
    $ARRAYTOMADOTOTALES2 = $EXIMATERIAPRIMA_ADO->obtenerTotalesPorPcdespacho($EMPRESAS, $PLANTAS, $TEMPORADAS, $IDOP);

    $TOTALENVASE = $ARRAYTOMADOTOTALES[0]['ENVASE'];
    $TOTALNETO = $ARRAYTOMADOTOTALES[0]['NETO'];

    $TOTALENVASEV = $ARRAYTOMADOTOTALES2[0]['ENVASE'];
    $TOTALNETOV = $ARRAYTOMADOTOTALES2[0]['NETO'];




    //IDENTIFICACIONES DE OPERACIONES
    //crear =  OBTENCION DE DATOS INICIALES PARA PODER CREAR LA RECEPCION
    if ($OP == "crear") {
        //OBTENCION DE INFORMACIOND DE LA FILA DEL REGISTRO
        //ALMACENAR INFORMACION EN ARREGLO
        //LLAMADA A LA FUNCION DE CONTROLADOR verPlanta(ID), 
        //SE LE PASE UNO DE LOS DATOS OBTENIDO PREVIAMENTE A TRAVEZ DE LA URL
        $DISABLED2 = "disabled"; // Campos básicos bloqueados una vez creado
        $DISABLED_MOTIVO = ""; // Motivo siempre editable hasta cerrar
        $DISABLED_SELECCION = ""; // Botón selección habilitado para crear/editar
        $DISABLED3 = "disabled";
        $DISABLEDMENU = "disabled";
        $DISABLEDSTYLE = "style='background-color: #eeeeee;'";
        $DISABLEDSTYLE_MOTIVO = "";
        //OBTENCIONS DE LOS DATODS DE LA COLUMNAS DE LA FILA OBTENIDA
        $ARRAYPCDESPACHO = $PCDESPACHOMP_ADO->verPcdespacho($IDOP);
        //PASAR DATOS OBTENIDOS A VARIABLES QUE SE VISUALIZAR EN EL FORMULARIO DE LA VISTA
        foreach ($ARRAYPCDESPACHO as $r) :
            $IDPCDESPACHO = $IDOP;
            $NUMEROVER = "" . $r['NUMERO_PCDESPACHO'];
            $EMPRESA = "" . $r['ID_EMPRESA'];
            $PLANTA = "" . $r['ID_PLANTA'];
            $TEMPORADA = "" . $r['ID_TEMPORADA'];
            $MOTIVOPCDESPACHO = "" . $r['MOTIVO_PCDESPACHO'];
            $FECHAPCDESPACHO = "" . $r['FECHA_PCDESPACHO'];
            $FECHAINGRESOPCDESPACHO = "" . $r['INGRESO'];
            $FECHAMODIFCIACIONPCDESPACHO = "" . $r['MODIFICACION'];
            $ID_PRODUCTOR = "" . $r['ID_PRODUCTOR']; 
            $ID_ESTANDAR = "" . $r['ID_ESTANDAR']; 
            $ID_VARIEDAD = "" . $r['ID_VARIEDAD']; 
            $ESTADO = "" . $r['ESTADO'];
            
            // Si está cerrado (ESTADO = 0), deshabilitar también el motivo y selección
            if ($ESTADO == 0) {
                $DISABLED_MOTIVO = "disabled";
                $DISABLED_SELECCION = "disabled";
                $DISABLEDSTYLE_MOTIVO = "style='background-color: #eeeeee;'";
            }
        endforeach;
    }

    //editar =  OBTENCION DE DATOS PARA LA EDICION DE REGISTRO
    if ($OP == "editar") {

        //OBTENCION DE INFORMACIOND DE LA FILA DEL REGISTRO
        //ALMACENAR INFORMACION EN ARREGLO
        //LLAMADA A LA FUNCION DE CONTROLADOR verPlanta(ID), 
        //SE LE PASE UNO DE LOS DATOS OBTENIDO PREVIAMENTE A TRAVEZ DE LA URL
        $DISABLED2 = "disabled"; // Campos básicos bloqueados una vez creado
        $DISABLED_MOTIVO = ""; // Motivo siempre editable hasta cerrar
        $DISABLED_SELECCION = ""; // Botón selección habilitado para crear/editar
        $DISABLED3 = "disabled";
        $DISABLEDMENU = "disabled";
        $DISABLEDSTYLE = "style='background-color: #eeeeee;'";
        $DISABLEDSTYLE_MOTIVO = "";
        //OBTENCIONS DE LOS DATODS DE LA COLUMNAS DE LA FILA OBTENIDA
        $ARRAYPCDESPACHO = $PCDESPACHOMP_ADO->verPcdespacho($IDOP);
        //PASAR DATOS OBTENIDOS A VARIABLES QUE SE VISUALIZAR EN EL FORMULARIO DE LA VISTA

        foreach ($ARRAYPCDESPACHO as $r) :
            $IDPCDESPACHO = $IDOP;
            $NUMEROVER = "" . $r['NUMERO_PCDESPACHO'];
            $EMPRESA = "" . $r['ID_EMPRESA'];
            $PLANTA = "" . $r['ID_PLANTA'];
            $TEMPORADA = "" . $r['ID_TEMPORADA'];
            $MOTIVOPCDESPACHO = "" . $r['MOTIVO_PCDESPACHO'];
            $FECHAPCDESPACHO = "" . $r['FECHA_PCDESPACHO'];
            $FECHAINGRESOPCDESPACHO = "" . $r['INGRESO'];
            $FECHAMODIFCIACIONPCDESPACHO = "" . $r['MODIFICACION'];
            $ID_PRODUCTOR = "" . $r['ID_PRODUCTOR']; 
            $ID_ESTANDAR = "" . $r['ID_ESTANDAR']; 
            $ID_VARIEDAD = "" . $r['ID_VARIEDAD']; 
            $ESTADO = "" . $r['ESTADO'];
            
            // Si está cerrado (ESTADO = 0), deshabilitar también el motivo y selección
            if ($ESTADO == 0) {
                $DISABLED_MOTIVO = "disabled";
                $DISABLED_SELECCION = "disabled";
                $DISABLEDSTYLE_MOTIVO = "style='background-color: #eeeeee;'";
            }
        endforeach;
    }

    //ver =  OBTENCION DE DATOS PARA LA VISUALIZACION DEL REGISTRO
    if ($OP == "ver") {
        //DESABILITAR INPUT DEL FORMULARIO
        //PARA QUE NO MODIFIQUE NIGUNA INFORMACION, OBJETIVO ES VISUALIZAR INFORMACION
        $DISABLED = "disabled";
        $DISABLED2 = "disabled";
        $DISABLED_MOTIVO = "disabled";
        $DISABLED_SELECCION = "disabled"; // En modo ver, botón siempre deshabilitado
        $DISABLED3 = "disabled";
        $DISABLEDMENU = "disabled";
        $DISABLEDSTYLE = "style='background-color: #eeeeee;'";
        $DISABLEDSTYLE_MOTIVO = "style='background-color: #eeeeee;'";
        //OBTENCIONS DE LOS DATODS DE LA COLUMNAS DE LA FILA OBTENIDA
        $ARRAYPCDESPACHO = $PCDESPACHOMP_ADO->verPcdespacho($IDOP);
        //PASAR DATOS OBTENIDOS A VARIABLES QUE SE VISUALIZAR EN EL FORMULARIO DE LA VISTA

        foreach ($ARRAYPCDESPACHO as $r) :
            $IDPCDESPACHO = $IDOP;
            $NUMEROVER = "" . $r['NUMERO_PCDESPACHO'];
            $EMPRESA = "" . $r['ID_EMPRESA'];
            $PLANTA = "" . $r['ID_PLANTA'];
            $TEMPORADA = "" . $r['ID_TEMPORADA'];
            $MOTIVOPCDESPACHO = "" . $r['MOTIVO_PCDESPACHO'];
            $FECHAPCDESPACHO = "" . $r['FECHA_PCDESPACHO'];
            $FECHAINGRESOPCDESPACHO = "" . $r['INGRESO'];
            $FECHAMODIFCIACIONPCDESPACHO = "" . $r['MODIFICACION'];
            $ID_PRODUCTOR = "" . $r['ID_PRODUCTOR']; 
            $ID_ESTANDAR = "" . $r['ID_ESTANDAR']; 
            $ID_VARIEDAD = "" . $r['ID_VARIEDAD']; 
            $ESTADO = "" . $r['ESTADO'];
        endforeach;
    }
}
if ($_POST) {

    if (isset($_REQUEST['EMPRESA'])) {
        $EMPRESA = "" . $_REQUEST['EMPRESA'];
    }

    if (isset($_REQUEST['PLANTA'])) {
        $PLANTA = "" . $_REQUEST['PLANTA'];
    }

    if (isset($_REQUEST['TEMPORADA'])) {
        $TEMPORADA = "" . $_REQUEST['TEMPORADA'];
    }

    if (isset($_REQUEST['MOTIVOPCDESPACHO'])) {
        $MOTIVOPCDESPACHO = "" . $_REQUEST['MOTIVOPCDESPACHO'];
    }
    if (isset($_REQUEST['ID_PRODUCTOR'])) {
        $ID_PRODUCTOR = "" . $_REQUEST['ID_PRODUCTOR'];
    }
    if (isset($_REQUEST['ID_ESTANDAR'])) {
        $ID_ESTANDAR = "" . $_REQUEST['ID_ESTANDAR'];
    }
    if (isset($_REQUEST['ID_VARIEDAD'])) {
        $ID_VARIEDAD = "" . $_REQUEST['ID_VARIEDAD'];
    }
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>Registro Planificador Carga MP</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <!- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -!>
        <?php include_once "../../assest/config/urlHead.php"; ?>
        <!- FUNCIONES BASES -!>
            <script type="text/javascript">
                function validacion() {


                    FECHAPCDESPACHO = document.getElementById("FECHAPCDESPACHO").value;
                    ID_PRODUCTOR = document.getElementById("ID_PRODUCTOR").selectedIndex;
                    ID_ESTANDAR = document.getElementById("ID_ESTANDAR").selectedIndex;
                    ID_VARIEDAD = document.getElementById("ID_VARIEDAD").selectedIndex;
                    MOTIVOPCDESPACHO = document.getElementById("MOTIVOPCDESPACHO").value;

                    document.getElementById('val_fecha').innerHTML = "";
                    document.getElementById('val_productor').innerHTML = "";
                    document.getElementById('val_estandar').innerHTML = "";
                    document.getElementById('val_variedad').innerHTML = "";
                    document.getElementById('val_motivo').innerHTML = "";


                    if (FECHAPCDESPACHO == null || FECHAPCDESPACHO.length == 0 || /^\s+$/.test(FECHAPCDESPACHO)) {
                        document.form_reg_dato.FECHAPCDESPACHO.focus();
                        document.form_reg_dato.FECHAPCDESPACHO.style.borderColor = "#FF0000";
                        document.getElementById('val_fecha').innerHTML = "NO A INGRESADO DATO";
                        return false;
                    }
                    document.form_reg_dato.FECHAPCDESPACHO.style.borderColor = "#4AF575";

                    if (ID_PRODUCTOR == null || ID_PRODUCTOR == 0) {
                            document.form_reg_dato.ID_PRODUCTOR.focus();
                            document.form_reg_dato.ID_PRODUCTOR.style.borderColor = "#FF0000";
                            document.getElementById('val_productor').innerHTML = "NO HA SELECIONADO PRODUCTOR";
                            return false
                        }
                        document.form_reg_dato.ID_PRODUCTOR.style.borderColor = "#4AF575";

                    if (ID_ESTANDAR == null || ID_ESTANDAR == 0) {
                            document.form_reg_dato.ID_ESTANDAR.focus();
                            document.form_reg_dato.ID_ESTANDAR.style.borderColor = "#FF0000";
                            document.getElementById('val_estandar').innerHTML = "NO HA SELECIONADO ESTANDAR";
                            return false
                        }
                        document.form_reg_dato.ID_ESTANDAR.style.borderColor = "#4AF575";

                    if (ID_VARIEDAD == null || ID_VARIEDAD == 0) {
                            document.form_reg_dato.ID_VARIEDAD.focus();
                            document.form_reg_dato.ID_VARIEDAD.style.borderColor = "#FF0000";
                            document.getElementById('val_variedad').innerHTML = "NO HA SELECIONADO VARIEDAD";
                            return false
                        }
                        document.form_reg_dato.ID_VARIEDAD.style.borderColor = "#4AF575";
                    

                    if (MOTIVOPCDESPACHO == null || MOTIVOPCDESPACHO.length == 0 || /^\s+$/.test(MOTIVOPCDESPACHO)) {
                        document.form_reg_dato.MOTIVOPCDESPACHO.focus();
                        document.form_reg_dato.MOTIVOPCDESPACHO.style.borderColor = "#FF0000";
                        document.getElementById('val_motivo').innerHTML = "NO A INGRESADO DATO";
                        return false;
                    }
                    document.form_reg_dato.MOTIVOPCDESPACHO.style.borderColor = "#4AF575";


                }
                //REDIRECCIONAR A LA PAGINA SELECIONADA
                function irPagina(url) {
                    location.href = "" + url;
                }
            

                //FUNCION PARA REALIZAR UNA ACTUALIZACION DEL FORMULARIO DE REGISTRO DE RECEPCION
                function refrescar() {
                    document.getElementById("form_reg_dato").submit();
                }

                //FUNCION PARA ABRIR VENTANA QUE SE ENCUENTRA LA OPERACIONES DE DETALLE DE RECEPCION
                function abrirVentana(url) {
                    var opciones =
                        "'directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=1600, height=1000'";
                    window.open(url, 'window', opciones);
                }

                function abrirPestana(url) {
                    var win = window.open(url, '_blank');
                    win.focus();
                }

                //FUNCION PARA FILTRAR VARIEDADES SEGUN ESTANDAR SELECCIONADO
                function filtrarVariedades() {
                    var estandarId = document.getElementById("ID_ESTANDAR").value;
                    var variedadSelect = document.getElementById("ID_VARIEDAD");
                    
                    // Limpiar el select de variedades
                    variedadSelect.innerHTML = '<option value="">Selecciona una Variedad</option>';
                    
                    if (estandarId != "") {
                        // Hacer petición AJAX al mismo archivo
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "registroPcMP.php", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState == 4 && xhr.status == 200) {
                                try {
                                    var variedades = JSON.parse(xhr.responseText);
                                    
                                    // Agregar las variedades al select
                                    for (var i = 0; i < variedades.length; i++) {
                                        var option = document.createElement("option");
                                        option.value = variedades[i].ID_VESPECIES;
                                        option.text = variedades[i].NOMBRE_VESPECIES;
                                        variedadSelect.appendChild(option);
                                    }
                                    
                                    // Reinicializar Select2 si está siendo usado
                                    if (typeof $('#ID_VARIEDAD').select2 === 'function') {
                                        $('#ID_VARIEDAD').select2();
                                    }
                                } catch (e) {
                                    console.error("Error al procesar respuesta:", e);
                                }
                            }
                        };
                        
                        xhr.send("ajax_filtrar_variedades=1&id_estandar=" + estandarId + "&empresa=" + document.getElementById("EMPRESA").value);
                    }
                }
            </script>

</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary" >
    <div class="wrapper">
        <!- LLAMADA AL MENU PRINCIPAL DE LA PAGINA-!>
            <?php include_once "../../assest/config/menuFruta.php";  ?>
            <div class="content-wrapper">
                <div class="container-full">
                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Frigorifico</h3>
                                <div class="d-inline-block align-items-center">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"> <a href="index.php"> <i class="mdi mdi-home-outline"></i></a></li>
                                            <li class="breadcrumb-item" aria-current="page">Módulo</li>
                                            <li class="breadcrumb-item" aria-current="page">Frigorifico</li>
                                            <li class="breadcrumb-item" aria-current="page">Planificador Carga MP</li>
                                            <li class="breadcrumb-item active" aria-current="page"> <a href="#">Registro PC MP</a>  </li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                            <?php include_once "../../assest/config/verIndicadorEconomico.php"; ?>
                        </div>
                    </div>
                    <!-- Main content -->
                    <section class="content">
                        <form class="form" role="form" method="post" name="form_reg_dato" id="form_reg_dato">
                            <div class="box">
                                 <div class="box-header with-border bg-primary">                                   
                                    <h4 class="box-title">Registro de Planificador de Carga MP</h4>                                        
                                </div>
                                <div class="box-body ">
                                    <div class="row">
                                        <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 col-xs-6">

                                            <input type="hidden" class="form-control" placeholder="ID EMPRESA" id="EMPRESA" name="EMPRESA" value="<?php echo $EMPRESAS; ?>" />
                                            <input type="hidden" class="form-control" placeholder="ID PLANTA" id="PLANTA" name="PLANTA" value="<?php echo $PLANTAS; ?>" />
                                            <input type="hidden" class="form-control" placeholder="ID TEMPORADA" id="TEMPORADA" name="TEMPORADA" value="<?php echo $TEMPORADAS; ?>" />

                                            <input type="hidden" class="form-control" placeholder="ID EMPRESA" id="EMPRESAE" name="EMPRESAE" value="<?php echo $EMPRESA; ?>" />
                                            <input type="hidden" class="form-control" placeholder="ID PLANTA" id="PLANTAE" name="PLANTAE" value="<?php echo $PLANTA; ?>" />
                                            <input type="hidden" class="form-control" placeholder="ID TEMPORADA" id="TEMPORADAE" name="TEMPORADAE" value="<?php echo $TEMPORADA; ?>" />

                                            <input type="hidden" class="form-control" id="TOTALENVASE" name="TOTALENVASE" value="<?php echo $TOTALENVASE; ?>" />
                                            <input type="hidden" class="form-control" id="TOTALNETO" name="TOTALNETO" value="<?php echo $TOTALNETO; ?>" />
                                            <input type="hidden" class="form-control" placeholder="ID PDDESPACHO" id="IDP" name="IDP" value="<?php echo $IDOP; ?>" />
                                            <input type="hidden" class="form-control" placeholder="OP PDDESPACHO" id="OPP" name="OPP" value="<?php echo $OP; ?>" />
                                            <input type="hidden" class="form-control" placeholder="URL PDDESPACHO" id="URLP" name="URLP" value="registroPcdespacho" />

                                            <label>Número Planifica. Carga </label>
                                            <input type="text" class="form-control" placeholder="Número Planificador Carga" id="NUMEROVER" name="NUMEROVER" value="<?php echo $NUMEROVER; ?>" <?php echo $DISABLED0; ?> <?php echo $DISABLEDSTYLE0; ?> />
                                            <label id="val_id" class="validacion"> </label>
                                        </div>
                                        <div class="col-xxl-6 col-xl-1 col-lg-1 col-md-6 col-sm-6 col-6 col-xs-6">
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Fecha Ingreso</label>
                                                <input type="hidden" class="form-control" placeholder="Fecha Ingreso " id="FECHAINGRESOPCDESPACHOE" name="FECHAINGRESOPCDESPACHOE" value="<?php echo $FECHAINGRESOPCDESPACHO; ?>" />
                                                <input type="date" class="form-control" style="background-color: #eeeeee;" placeholder="FECHA RECEPCION" id="FECHAINGRESOPCDESPACHO" name="FECHAINGRESOPCDESPACHO" value="<?php echo $FECHAINGRESOPCDESPACHO; ?>" disabled />
                                                <label id="val_fechai" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Fecha Modificación</label>
                                                <input type="hidden" class="form-control" placeholder="Fecha Modificacion " id="FECHAMODIFCIACIONPCDESPACHOE" name="FECHAMODIFCIACIONPCDESPACHOE" value="<?php echo $FECHAMODIFCIACIONPCDESPACHO; ?>" />
                                                <input type="date" class="form-control " style="background-color: #eeeeee;" placeholder="FECHA MODIFICACION" id="FECHAMODIFCIACIONPCDESPACHO" name="FECHAMODIFCIACIONPCDESPACHO" value="<?php echo $FECHAMODIFCIACIONPCDESPACHO; ?>" disabled />
                                                <label id="val_fecham" class="validacion"> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xxl-2 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <label>Fecha PC</label>
                                            <input type="hidden" class="form-control" placeholder="Fecha PC Despacho" id="FECHAPCDESPACHOE" name="FECHAPCDESPACHOE" value="<?php echo $FECHAPCDESPACHO; ?>" />
                                            <input type="date" class="form-control" placeholder="Fecha PC Despacho" id="FECHAPCDESPACHO" name="FECHAPCDESPACHO" value="<?php echo $FECHAPCDESPACHO; ?>" <?php echo $DISABLED2; ?> <?php echo $DISABLEDSTYLE; ?> />
                                            <label id="val_fecha" class="validacion"> </label>
                                        </div>                                        
                                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 col-xs-12">
                                            <div class="form-group">
                                                <label>Productor</label>
                                                <input type="hidden" class="form-control" placeholder="ID_PRODUCTOR" id="ID_PRODUCTORE" name="ID_PRODUCTORE" value="<?php echo $ID_PRODUCTOR; ?>" />
                                                <select class="form-control select2" id="ID_PRODUCTOR" name="ID_PRODUCTOR" style="width: 100%; <?php if($DISABLED2 == 'disabled') echo 'background-color: #eeeeee;'; ?>" <?php echo $DISABLED2; ?> >
                                                    <option value="">Selecciona un Productor</option>
                                                    <?php if ($ARRAYPRODUCTOR) { ?>
                                                        <?php foreach ($ARRAYPRODUCTOR as $r) : ?>
                                                            <option value="<?php echo $r['ID_PRODUCTOR']; ?>" <?php if ($ID_PRODUCTOR == $r['ID_PRODUCTOR']) { echo "selected"; } ?>>
                                                                (CSG:<?php echo $r['CSG_PRODUCTOR']; ?>) <?php echo $r['NOMBRE_PRODUCTOR']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php } ?>
                                                </select>
                                                <label id="val_productor" class="validacion"> </label> 
                                            </div> 
                                        </div>
                                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 col-xs-12">
                                            <div class="form-group">
                                                <label>Estándar</label>
                                                <input type="hidden" class="form-control" placeholder="ID_ESTANDAR" id="ID_ESTANDARE" name="ID_ESTANDARE" value="<?php echo $ID_ESTANDAR; ?>" />
                                                <select class="form-control select2" id="ID_ESTANDAR" name="ID_ESTANDAR" style="width: 100%; <?php if($DISABLED2 == 'disabled') echo 'background-color: #eeeeee;'; ?>" <?php echo $DISABLED2; ?> onchange="filtrarVariedades()">
                                                    <option value="">Selecciona un Estándar</option>
                                                    <?php if ($ARRAYESTANDAR) { ?>
                                                        <?php foreach ($ARRAYESTANDAR as $r) : ?>
                                                            <option value="<?php echo $r['ID_ESTANDAR']; ?>" <?php if ($ID_ESTANDAR == $r['ID_ESTANDAR']) { echo "selected"; } ?>>
                                                                <?php echo $r['CODIGO_ESTANDAR']; ?> - <?php echo $r['NOMBRE_ESTANDAR']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php } ?>
                                                </select>
                                                <label id="val_estandar" class="validacion"> </label> 
                                            </div> 
                                        </div>
                                        <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12 col-xs-12">
                                            <div class="form-group">
                                                <label>Variedad</label>
                                                <input type="hidden" class="form-control" placeholder="ID_VARIEDAD" id="ID_VARIEDADE" name="ID_VARIEDADE" value="<?php echo $ID_VARIEDAD; ?>" />
                                                <select class="form-control select2" id="ID_VARIEDAD" name="ID_VARIEDAD" style="width: 100%; <?php if($DISABLED2 == 'disabled') echo 'background-color: #eeeeee;'; ?>" <?php echo $DISABLED2; ?> >
                                                    <option value="">Selecciona una Variedad</option>
                                                    <?php if ($ARRAYVARIEDAD) { ?>
                                                        <?php foreach ($ARRAYVARIEDAD as $r) : ?>
                                                            <option value="<?php echo $r['ID_VESPECIES']; ?>" <?php if ($ID_VARIEDAD == $r['ID_VESPECIES']) { echo "selected"; } ?>>
                                                                <?php echo $r['NOMBRE_VESPECIES']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php } ?>
                                                </select>
                                                <label id="val_variedad" class="validacion"> </label> 
                                            </div> 
                                        </div>
                                        <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                            <label>Motivo</label>
                                            <input type="hidden" class="form-control" placeholder="Fecha PC Despacho" id="MOTIVOPCDESPACHOE" name="MOTIVOPCDESPACHOE" value="<?php echo $MOTIVOPCDESPACHO; ?>" />
                                            <textarea class="form-control" rows="1" placeholder="Ingrese Nota, Observaciónes u Otro" id="MOTIVOPCDESPACHO" name="MOTIVOPCDESPACHO" <?php echo $DISABLED_MOTIVO; ?> <?php echo $DISABLEDSTYLE_MOTIVO; ?>><?php echo $MOTIVOPCDESPACHO; ?></textarea>
                                            <label id="val_motivo" class="validacion"> </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.box-body -->                          
                                <div class="box-footer">
                                    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="toolbar">
                                        <div class="btn-group  col-xxl-4 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12" role="group" aria-label="Acciones generales">
                                            <?php if ($OP == "") { ?>
                                                <button type="button" class="btn btn-warning " data-toggle="tooltip" title="Cancelar" name="CANCELAR" value="CANCELAR" Onclick="irPagina('registroPcMP.php');">
                                                    <i class="ti-trash"></i> Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-primary" data-toggle="tooltip" title="Guardar" name="CREAR" value="CREAR"   onclick="return validacion()">
                                                    <i class="ti-save-alt"></i> Guardar
                                                </button>
                                            <?php } ?>
                                            <?php if ($OP != "") { ?>
                                                <button type="button" class="btn  btn-success " data-toggle="tooltip" title="Volver" name="VOLVER" value="VOLVER" Onclick="irPagina('listarPcMP.php'); ">
                                                    <i class="ti-back-left "></i> Volver
                                                </button>
                                                <button type="submit" class="btn btn-warning " data-toggle="tooltip" title="Guardar" name="GUARDAR" value="GUARDAR"  <?php echo $DISABLED2; ?> onclick="return validacion()">
                                                    <i class="ti-pencil-alt"></i> Guardar
                                                </button>
                                                <button type="submit" class="btn btn-danger " data-toggle="tooltip" title="Cerrar" name="CERRAR" value="CERRAR"  <?php echo $DISABLED2; ?> onclick="return validacion()">
                                                    <i class="ti-save-alt"></i> Cerrar
                                                </button>
                                            <?php } ?>
                                        </div>
                                        <div class="btn-group  col-xxl-4 col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12  float-right">
                                            <?php if ($OP != ""): ?>
                                                <button type="button" class="btn btn-info  " data-toggle="tooltip" title="Informe" id="defecto" name="tarjas" Onclick="abrirPestana('../../assest/documento/informePcdespacho.php?parametro=<?php echo $IDOP; ?>&&usuario=<?php echo $IDUSUARIOS; ?>');">
                                                    <i class="fa fa-file-pdf-o"></i> Informe
                                                </button>
                                                <button type="button" class="btn btn-info  " data-toggle="tooltip" title="Packing List" id="defecto" name="tarjas" Onclick="abrirPestana('../../assest/documento/informePCdespachoPtPackingList.php?parametro=<?php echo $IDOP; ?>&&usuario=<?php echo $IDUSUARIOS; ?>');">
                                                    <i class="fa fa-file-pdf-o"></i> Packing List
                                                </button>
                                                <button type="button" class="btn btn-info  " data-toggle="tooltip" title="Comercial" id="defecto" name="tarjas" Onclick="abrirPestana('../../assest/documento/informePCdespachoPtComercial.php?parametro=<?php echo $IDOP; ?>&&usuario=<?php echo $IDUSUARIOS; ?>');">
                                                    <i class="fa fa-file-pdf-o"></i> Comercial
                                                </button>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--.row -->
                        </form>

                        <?php if (isset($_GET['op'])): ?>
                            <div class="card">
                                <div class="card-header bg-info">
                                    <h4 class="card-title">Detalle Planificador Carga</h4>
                                </div>
                                <div class="card-header">
                                        <div class="form-row align-items-center">
                                            <form method="post" id="form1">
                                                <input type="hidden" class="form-control" placeholder="ID DESPACHO" id="IDP" name="IDP" value="<?php echo $IDOP; ?>" />
                                                <input type="hidden" class="form-control" placeholder="OP DESPACHO" id="OPP" name="OPP" value="<?php echo $OP; ?>" />
                                                <input type="hidden" class="form-control" placeholder="URL DESPACHO" id="URLP" name="URLP" value="registroPcMP" />
                                                <input type="hidden" class="form-control" placeholder="URL SELECCIONAR" id="URLD" name="URLD" value="registroSelecionExistenciaMPPcdespachoMP" />
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-success btn-block mb-2" data-toggle="tooltip" title="Seleccion Existencia" id="SELECIONOCDURL" name="SELECIONOCDURL"
                                                        <?php echo $DISABLED_SELECCION; ?>  <?php   if ($ESTADO == 0) {   echo "disabled style='background-color: #eeeeee;'"; } ?>  > 
                                                        Seleccion Existencias
                                                    </button>
                                                </div>
                                            </form>
                                            <div class="col-auto">
                                                <label class="sr-only" for=""></label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Total Envase</div>
                                                    </div>
                                                    <input type="hidden" class="form-control" id="TOTALENVASE" name="TOTALENVASE" value="<?php echo $TOTALENVASE; ?>" />
                                                    <input type="text" class="form-control" placeholder="Total Envase" id="TOTALENVASEV" name="TOTALENVASEV" value="<?php echo $TOTALENVASEV; ?>" disabled />
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <label class="sr-only" for=""></label>
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Total Neto</div>
                                                    </div>
                                                    <input type="hidden" class="form-control" id="TOTALNETO" name="TOTALNETO" value="<?php echo $TOTALNETO; ?>" />
                                                    <input type="text" class="form-control" placeholder="Total Neto" id="TOTALENVASEV" name="TOTALENVASEV" value="<?php echo $TOTALNETOV; ?>" disabled />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <label id="val_validacion" class="validacion"><?php echo $MENSAJE; ?> </label>                            
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="detalle" class="table-hover " style="width: 100%;">
                                                    <thead>
                                                        <tr class="text-center">
                                                            <th>Folio Original</th>
                                                            <th class="text-center">Operaciones</th>
                                                            <th>Fecha Cosecha</th>
                                                            <th>Días</th>
                                                            <th>Código Estandar</th>
                                                            <th>Envase/Estandar</th>
                                                            <th>Gasificación</th>
                                                            <th>CSG</th>
                                                            <th>Productor</th>
                                                            <th>Variedad</th>
                                                            <th>Cantidad Envase</th>
                                                            <th>Kilos Neto</th>
                                                            <th>Número Recepción</th>
                                                            <th>Fecha Recepción</th>
                                                            <th>Número Guía Recepción</th>
                                                            <th>Tipo Manejo</th>
                                                            <th>Gasificacion</th>
                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($ARRAYTOMADO) { ?>
                                                            <?php foreach ($ARRAYTOMADO as $r) : ?>
                                                                <?php
                                                                // Obtener información del productor
                                                                $ARRAYVERPRODUCTORID = $PRODUCTOR_ADO->verProductor($r['ID_PRODUCTOR']);
                                                                if ($ARRAYVERPRODUCTORID) {
                                                                    $CSGPRODUCTOR = $ARRAYVERPRODUCTORID[0]['CSG_PRODUCTOR'];
                                                                    $NOMBREPRODUCTOR = $ARRAYVERPRODUCTORID[0]['NOMBRE_PRODUCTOR'];
                                                                } else {
                                                                    $CSGPRODUCTOR = "Sin Datos";
                                                                    $NOMBREPRODUCTOR = "Sin Datos";
                                                                }
                                                                
                                                                // Obtener información del estándar
                                                                $ARRAYEVERERECEPCIONID = $ERECEPCION_ADO->verEstandar($r['ID_ESTANDAR']);
                                                                if ($ARRAYEVERERECEPCIONID) {
                                                                    $CODIGOESTANDAR = $ARRAYEVERERECEPCIONID[0]['CODIGO_ESTANDAR'];
                                                                    $NOMBREESTANDAR = $ARRAYEVERERECEPCIONID[0]['NOMBRE_ESTANDAR'];
                                                                } else {
                                                                    $CODIGOESTANDAR = "Sin Datos";
                                                                    $NOMBREESTANDAR = "Sin Datos";
                                                                }
                                                                
                                                                // Obtener información de la variedad
                                                                $ARRAYVERVESPECIESID = $VESPECIES_ADO->verVespecies($r['ID_VESPECIES']);
                                                                if ($ARRAYVERVESPECIESID) {
                                                                    $NOMBREVESPECIES = $ARRAYVERVESPECIESID[0]['NOMBRE_VESPECIES'];
                                                                } else {
                                                                    $NOMBREVESPECIES = "Sin Datos";
                                                                }
                                                                
                                                                // Obtener información de la recepción
                                                                $ARRAYRECEPCION = $RECEPCIONMP_ADO->verRecepcion2($r['ID_RECEPCION']);
                                                                if ($ARRAYRECEPCION) {
                                                                    $NUMERORECEPCION = $ARRAYRECEPCION[0]["NUMERO_RECEPCION"];
                                                                    $FECHARECEPCION = $ARRAYRECEPCION[0]["FECHA"];
                                                                    $NUMEROGUIARECEPCION = $ARRAYRECEPCION[0]["NUMERO_GUIA_RECEPCION"];
                                                                } else {
                                                                    $NUMERORECEPCION = "Sin Datos";
                                                                    $FECHARECEPCION = "";
                                                                    $NUMEROGUIARECEPCION = "Sin Datos";
                                                                }
                                                                
                                                                // Obtener información del tipo de manejo
                                                                $ARRAYTMANEJO = $TMANEJO_ADO->verTmanejo($r['ID_TMANEJO']);
                                                                if ($ARRAYTMANEJO) {
                                                                    $NOMBRETMANEJO = $ARRAYTMANEJO[0]['NOMBRE_TMANEJO'];
                                                                } else {
                                                                    $NOMBRETMANEJO = "Sin Datos";
                                                                }
                                                                
                                                                // Gasificación
                                                                if ($r['GASIFICADO'] == "1") {
                                                                    $GASIFICADO = "SI";
                                                                } else if ($r['GASIFICADO'] == "0") {
                                                                    $GASIFICADO = "NO";
                                                                } else {
                                                                    $GASIFICADO = "Sin Datos";
                                                                }
                                                                ?>
                                                                <tr class="text-center">
                                                                    <td><?php echo isset($r['FOLIO_AUXILIAR_EXIMATERIAPRIMA']) ? $r['FOLIO_AUXILIAR_EXIMATERIAPRIMA'] : 'Sin Datos'; ?></td>
                                                                    <td>
                                                                        <form method="post" id="form1">
                                                                            <input type="hidden" class="form-control" id="IDQUITAR" name="IDQUITAR" value="<?php echo $r['ID_EXIMATERIAPRIMA']; ?>" />
                                                                            <div class="btn-group btn-rounded btn-block col-10" role="group" aria-label="Operaciones Detalle">
                                                                                <button type="submit" class="btn btn-sm btn-danger" id="QUITAR" name="QUITAR" data-toggle="tooltip" title="Quitar Existencia"
                                                                                    <?php echo $DISABLED2; ?> <?php if ($ESTADO == 0) { echo "disabled"; } ?>> 
                                                                                    <i class="ti-close"></i><br> Quitar
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </td>
                                                                    <td><?php echo $r['COSECHA']; ?></td>
                                                                    <td><?php echo $r['DIAS']; ?></td>
                                                                    <td><?php echo $CODIGOESTANDAR; ?></td>
                                                                    <td><?php echo $NOMBREESTANDAR; ?></td>
                                                                    <td><?php echo $GASIFICADO; ?></td>
                                                                    <td><?php echo $CSGPRODUCTOR; ?></td>
                                                                    <td><?php echo $NOMBREPRODUCTOR; ?></td>
                                                                    <td><?php echo $NOMBREVESPECIES; ?></td>
                                                                    <td><?php echo $r['ENVASE']; ?></td>
                                                                    <td><?php echo $r['NETO']; ?></td>
                                                                    <td><?php echo $NUMERORECEPCION; ?></td>
                                                                    <td><?php echo $FECHARECEPCION; ?></td>
                                                                    <td><?php echo $NUMEROGUIARECEPCION; ?></td>
                                                                    <td><?php echo $NOMBRETMANEJO; ?></td>
                                                                    <td><?php echo $GASIFICADO; ?></td>
                                                                    
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    </section>
                    <!-- /.content -->
                </div>
            </div>
            <!- LLAMADA ARCHIVO DEL DISEÑO DEL FOOTER Y MENU USUARIO -!>
                <?php include_once "../../assest/config/footer.php"; ?>
                <?php include_once "../../assest/config/menuExtraFruta.php"; ?>
    </div>
    <!- LLAMADA URL DE ARCHIVOS DE DISEÑO Y JQUERY E OTROS -!>
        <?php include_once "../../assest/config/urlBase.php"; ?>

        <?php             
            //OPERACIONES
            //OPERACION DE REGISTRO DE FILA
            if (isset($_REQUEST['CREAR'])) {
                $ARRAYNUMERO = $PCDESPACHOMP_ADO->obtenerNumero($_REQUEST['EMPRESA'], $_REQUEST['PLANTA'], $_REQUEST['TEMPORADA']);
                $NUMERO = $ARRAYNUMERO[0]['NUMERO'] + 1;
                $PCDESPACHOMP->__SET('NUMERO_PCDESPACHO', $NUMERO);
                $PCDESPACHOMP->__SET('FECHA_PCDESPACHO', $_REQUEST['FECHAPCDESPACHO']);
                $PCDESPACHOMP->__SET('MOTIVO_PCDESPACHO', $_REQUEST['MOTIVOPCDESPACHO']);
                $PCDESPACHOMP->__SET('ID_PRODUCTOR', $_REQUEST['ID_PRODUCTOR']);
                $PCDESPACHOMP->__SET('ID_ESTANDAR', $_REQUEST['ID_ESTANDAR']);
                $PCDESPACHOMP->__SET('ID_VARIEDAD', $_REQUEST['ID_VARIEDAD']);
                $PCDESPACHOMP->__SET('ID_EMPRESA', $_REQUEST['EMPRESA']);
                $PCDESPACHOMP->__SET('ID_PLANTA', $_REQUEST['PLANTA']);
                $PCDESPACHOMP->__SET('ID_TEMPORADA', $_REQUEST['TEMPORADA']);
                $PCDESPACHOMP->__SET('ID_USUARIOI', $IDUSUARIOS);
                $PCDESPACHOMP->__SET('ID_USUARIOM', $IDUSUARIOS);
                //LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                $PCDESPACHOMP_ADO->agregarPcdespacho($PCDESPACHOMP);

                $ARRYAOBTENERID = $PCDESPACHOMP_ADO->obtenerId(
                    $_REQUEST['FECHAPCDESPACHO'],
                    $_REQUEST['MOTIVOPCDESPACHO'],
                    $_REQUEST['EMPRESA'],
                    $_REQUEST['PLANTA'],
                    $_REQUEST['TEMPORADA']
                );

                $AUSUARIO_ADO->agregarAusuario2($NUMERO,1,1,"".$_SESSION["NOMBRE_USUARIO"].", Registro de PC","fruta_pcdespacho_mp",$ARRYAOBTENERID[0]['ID_PCDESPACHO'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],$_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );

                //REDIRECCIONAR A PAGINA registroPcMP.php
                $id_dato = $ARRYAOBTENERID[0]['ID_PCDESPACHO'];
                $accion_dato = "crear";
                echo '<script>
                    Swal.fire({
                        icon:"success",
                        title:"Registro Creado",
                        text:"El registro de PC se ha creado correctamente",
                        showConfirmButton: true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm:false
                    }).then((result)=>{
                            location.href = "registroPcMP.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                        
                    })
                </script>';
                // echo "<script type='text/javascript'> location.href ='registroPcMP.php?op';</script>";
            }
            //OPERACION EDICION DE FILA
            if (isset($_REQUEST['GUARDAR'])) {
                $PCDESPACHOMP->__SET('CANTIDAD_ENVASE_PCDESPACHO', $_REQUEST['TOTALENVASE']);
                $PCDESPACHOMP->__SET('KILOS_NETO_PCDESPACHO', $_REQUEST['TOTALNETO']);
                $PCDESPACHOMP->__SET('FECHA_PCDESPACHO', $_REQUEST['FECHAPCDESPACHO']);
                $PCDESPACHOMP->__SET('MOTIVO_PCDESPACHO', $_REQUEST['MOTIVOPCDESPACHO']);
                $PCDESPACHOMP->__SET('ID_PRODUCTOR', $_REQUEST['ID_PRODUCTORE']);
                $PCDESPACHOMP->__SET('ID_ESTANDAR', $_REQUEST['ID_ESTANDARE']);
                $PCDESPACHOMP->__SET('ID_VARIEDAD', $_REQUEST['ID_VARIEDADE']);
                $PCDESPACHOMP->__SET('ID_USUARIOM', $IDUSUARIOS);
                $PCDESPACHOMP->__SET('ID_PCDESPACHO', $_REQUEST['IDP']);
                //LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                $PCDESPACHOMP_ADO->actualizarPcdespacho($PCDESPACHOMP);

                $AUSUARIO_ADO->agregarAusuario2($NUMEROVER,1,2,"".$_SESSION["NOMBRE_USUARIO"].", Modificación de PC","fruta_pcdespacho_mp",$_REQUEST['IDP'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],$_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );

                
                if ($accion_dato == "crear") {
                    $id_dato = $_REQUEST['IDP'];
                    $accion_dato = "crear";
                    echo '<script>
                        Swal.fire({
                            icon:"info",
                            title:"Registro Modificado",
                            text:"El registro de PC se ha modificada correctamente",
                            showConfirmButton: true,
                            confirmButtonText:"Cerrar",
                            closeOnConfirm:false
                        }).then((result)=>{
                            location.href = "registroPcMP.php?op&id='.$id_dato.'&a='.$accion_dato.'";                        
                        })
                    </script>';
                }
                if ($accion_dato == "editar") {
                    $id_dato = $_REQUEST['IDP'];
                    $accion_dato = "editar";
                    echo '<script>
                        Swal.fire({
                            icon:"info",
                            title:"Registro Modificado",
                            text:"El registro de PC se ha modificada correctamente",
                            showConfirmButton: true,
                            confirmButtonText:"Cerrar",
                            closeOnConfirm:false
                        }).then((result)=>{
                            location.href = "registroPcMP.php?op&id='.$id_dato.'&a='.$accion_dato.'";                        
                        })
                    </script>';
                }
            }
            //OPERACION EDICION DE FILA
            if (isset($_REQUEST['CERRAR'])) {
                $ARRAYTOMADO = $EXIMATERIAPRIMA_ADO->buscarPorPcdespacho2($_REQUEST['IDP']);

                if ($ARRAYTOMADO) {
                    $SINO = "0";
                    $MENSAJE = "";
                } else {
                    $SINO = "1";
                    echo '<script>
                            Swal.fire({
                                icon:"warning",
                                title:"Accion restringida",
                                text:"Tiene que haber al menos un registro de existencia selecionado",
                                showConfirmButton: true,
                                confirmButtonText:"Cerrar",
                                closeOnConfirm:false
                            })
                        </script>';
                }

                if ($SINO == "0") {
                    $PCDESPACHOMP->__SET('CANTIDAD_ENVASE_PCDESPACHO', $_REQUEST['TOTALENVASE']);
                    $PCDESPACHOMP->__SET('KILOS_NETO_PCDESPACHO', $_REQUEST['TOTALNETO']);
                    $PCDESPACHOMP->__SET('FECHA_PCDESPACHO', $_REQUEST['FECHAPCDESPACHO']);
                    $PCDESPACHOMP->__SET('MOTIVO_PCDESPACHO', $_REQUEST['MOTIVOPCDESPACHO']);
                    $PCDESPACHOMP->__SET('ID_PRODUCTOR', $_REQUEST['ID_PRODUCTORE']);
                    $PCDESPACHOMP->__SET('ID_ESTANDAR', $_REQUEST['ID_ESTANDARE']);
                    $PCDESPACHOMP->__SET('ID_VARIEDAD', $_REQUEST['ID_VARIEDADE']);
                    $PCDESPACHOMP->__SET('ID_USUARIOM', $IDUSUARIOS);
                    $PCDESPACHOMP->__SET('ID_PCDESPACHO', $_REQUEST['IDP']);
                    //LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                    $PCDESPACHOMP_ADO->actualizarPcdespacho($PCDESPACHOMP);

                    $PCDESPACHOMP->__SET('ID_PCDESPACHO', $_REQUEST['IDP']);
                    // LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                    $PCDESPACHOMP_ADO->confirmado($PCDESPACHOMP);

                    $PCDESPACHOMP->__SET('ID_PCDESPACHO', $_REQUEST['IDP']);
                    // LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                    $PCDESPACHOMP_ADO->cerrado($PCDESPACHOMP);

                    $AUSUARIO_ADO->agregarAusuario2($NUMEROVER,1,3,"".$_SESSION["NOMBRE_USUARIO"].", Cerrar PC ","fruta_pcdespacho_mp",$_REQUEST['IDP'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],$_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );

                    //REDIRECCIONAR A PAGINA registroPcMP.php 
                    //SEGUNE EL TIPO DE OPERACIONS QUE SE INDENTIFIQUE EN LA URL

                    if ($accion_dato == "crear") {
                        $id_dato = $_REQUEST['IDP'];
                        $accion_dato = "ver";
                        echo '<script>
                            Swal.fire({
                                icon:"info",
                                title:"Registro Cerrado",
                                text:"Este PC se encuentra cerrada y no puede ser modificada.",
                                showConfirmButton: true,
                                confirmButtonText:"Cerrar",
                                closeOnConfirm:false
                            }).then((result)=>{
                                location.href = "registroPcMP.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
                            })
                        </script>';
                    }
                    if ($accion_dato == "editar") {
                        $id_dato = $_REQUEST['IDP'];
                        $accion_dato = "ver";
                        echo '<script>
                            Swal.fire({
                                icon:"info",
                                title:"Registro Cerrado",
                                text:"Este PC se encuentra cerrada y no puede ser modificada.",
                                showConfirmButton: true,
                                confirmButtonText:"Cerrar",
                                closeOnConfirm:false
                            }).then((result)=>{
                                location.href = "registroPcMP.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
                            })
                        </script>';
                    }     
                }
            }
            if (isset($_REQUEST['QUITAR'])) {
                $IDQUITAR = $_REQUEST['IDQUITAR'];
                $EXIMATERIAPRIMA->__SET('ID_EXIMATERIAPRIMA', $IDQUITAR);
                // LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                $EXIMATERIAPRIMA_ADO->actualizarDeselecionarPCCambiarEstado($EXIMATERIAPRIMA);
                
                $AUSUARIO_ADO->agregarAusuario2("NULL",1,2,"".$_SESSION["NOMBRE_USUARIO"].", Se Quito la Existencia de PC.","fruta_eximateriaprima", "NULL" ,$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'], $_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );  
                
                echo '<script>
                    Swal.fire({
                        icon:"error",
                        title:"Accion realizada",
                        text:"Se ha quitado la existencia del PC.",
                        showConfirmButton: true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm:false
                    }).then((result)=>{
                        location.href = "registroPcMP.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
                    })
                </script>';
            }
        ?>
</body>

</html>