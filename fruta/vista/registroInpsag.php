<?php

include_once "../../assest/config/validarUsuarioFruta.php";

//LLAMADA ARCHIVOS NECESARIOS PARA LAS OPERACIONES

include_once '../../assest/controlador/EEXPORTACION_ADO.php';
include_once '../../assest/controlador/EXIEXPORTACION_ADO.php';
include_once '../../assest/controlador/INPSAG_ADO.php';
include_once '../../assest/controlador/TINPSAG_ADO.php';
include_once '../../assest/controlador/AUSUARIO_ADO.php';

include_once '../../assest/controlador/VESPECIES_ADO.php';
include_once '../../assest/controlador/PRODUCTOR_ADO.php';
include_once '../../assest/controlador/INPECTOR_ADO.php';
include_once '../../assest/controlador/CONTRAPARTE_ADO.php';
include_once '../../assest/controlador/PAIS_ADO.php';
include_once '../../assest/controlador/TMANEJO_ADO.php';
include_once '../../assest/controlador/TCALIBRE_ADO.php';
include_once '../../assest/controlador/TEMBALAJE_ADO.php';


include_once '../../assest/modelo/INPSAG.php';
include_once '../../assest/modelo/EXIEXPORTACION.php';


//INCIALIZAR LAS VARIBLES
//INICIALIZAR CONTROLADOR
$VESPECIES_ADO =  new VESPECIES_ADO();
$PRODUCTOR_ADO = new PRODUCTOR_ADO();

$EEXPORTACION_ADO = new EEXPORTACION_ADO();
$EXIEXPORTACION_ADO = new EXIEXPORTACION_ADO();
$INPSAG_ADO =  new INPSAG_ADO();
$TINPSAG_ADO =  new TINPSAG_ADO();
$AUSUARIO_ADO = new AUSUARIO_ADO();
$INPECTOR_ADO =  new INPECTOR_ADO();
$CONTRAPARTE_ADO =  new CONTRAPARTE_ADO();
$PAIS_ADO =  new PAIS_ADO();
$TMANEJO_ADO =  new TMANEJO_ADO();
$TCALIBRE_ADO =  new TCALIBRE_ADO();
$TEMBALAJE_ADO =  new TEMBALAJE_ADO();

//INIICIALIZAR MODELO EXIEXPORTACION
$INPSAG =  new INPSAG();
$EXIEXPORTACION =  new EXIEXPORTACION();

//INCIALIZAR VARIBALES A OCUPAR PARA LA FUNCIONALIDAD

$NUMERO = "";
$NUMEROVER = "";
$IDINPSAG = "";
$FECHAINGRESOINPSAG = "";
$FECHAMODIFCIACIONINPSAG = "";
$CORRELATIVOINPSAG="";
$TINPSAG = "";
$TMANEJO = "";
$ID_TMANEJO = "";
$TESTADOSAG = "";

$CANTIDADENVASEINPSAG = "";
$KILOSNETOINPSAG = "";
$KILOSBRUTOINPSAG = "";

$FECHAINPSAG = "";
$OBSERVACIONINPSAG = "";
$LOTE = "";

$ESTADO = "";
$TOTALENVASE = "";
$TOTALNETO = "";
$TOTALBRUTO = "";
$TOTALENVASE2 = "";
$TOTALNETO2 = "";
$TOTALBRUTO2 = "";


$IDEXIEXPORTACIONQUITAR = "";
$FOLIOEXIEXPORTACIONQUITAR = "";
$EMPRESA = "";
$PLANTA = "";
$TEMPORADA = "";
$INPECTOR = "";
$CONTRAPARTE = "";
$PAIS1 = "";
$PAIS2 = "";
$PAIS3 = "";
$PAIS4 = "";
$CIF = "";

$IDEMPRESA = "";
$IDPLANTA = "";
$IDTEMPORADA = "";



$SINO = "";
$IDOP = "";
$OP = "";
$ID = "";

$EEXPORTACION = "";
$VESPECIES = "";
$CALIBRE = "";
$PRODUCTOR = "";

$DISABLED = "";
$DISABLEDC = "";
$DISABLEDT = "";
$DISABLED2 = "";
$DISABLED3 = "";
$DISABLED4 = "";
$DISABLEDSTYLE = "";

$MENSAJE = "";
$MENSAJEVALIDATO = "";
$FOLIOAGREGAR = "";


//INICIALIZAR ARREGLOS

$ARRAYEMPRESA = "";
$ARRAYPLANTA = "";
$ARRAYTEMPORADA = "";
$ARRAYTINPSAG = "";

$ARRAYINPSAG = "";
$ARRAYINPSAG2 = "";
$ARRAYPVESPECIES = "";
$ARRAYESTANDAR = "";
$ARRAYFECHAACTUAL = "";
$ARRAYCONTRAPARTE = "";
$ARRAYINPECTOR = "";
$ARRAYPAIS = "";
$ARRAYNUMERO = "";


$ARRAYTOMADO = "";
$ARRAYSAGAPROTOMADO = "";
$ARRAYSAGNOAPROTOMADO = "";


//DEFINIR ARREGLOS CON LOS DATOS OBTENIDOS DE LAS FUNCIONES DE LOS CONTROLADORES
$ARRAYTINPSAG = $TINPSAG_ADO->listarTinpsagCBX();
$ARRAYTMANEJO = $TMANEJO_ADO->listarTmanejoCBX();
$ARRAYCONTRAPARTE =  $CONTRAPARTE_ADO->listarContrapartePorEmpresaCBX($EMPRESAS);
$ARRAYINPECTOR = $INPECTOR_ADO->listarInpectorPorEmpresaCBX($EMPRESAS);
$ARRAYPAIS = $PAIS_ADO->listarPaisCBX();
$ARRAYFECHAACTUAL = $INPSAG_ADO->obtenerFecha();
$FECHAINPSAG = $ARRAYFECHAACTUAL[0]['FECHA'];
include_once "../../assest/config/validarDatosUrl.php";
include_once "../../assest/config/datosUrlD.php";


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


//OBTENCION DE DATOS ENVIADOR A LA URL
//PARA OPERACIONES DE EDICION , VISUALIZACION Y CREACION
if (isset($id_dato) && isset($accion_dato)) {
    //ALMACENAR DATOS DE VARIABLES DE LA URL
    $IDOP = $id_dato;
    $OP = $accion_dato;

    $ARRAYTOMADO = $EXIEXPORTACION_ADO->buscarPorSag2($IDOP);
    $ARRAYDESPACHOTOTAL = $EXIEXPORTACION_ADO->obtenerTotalesInspSag($IDOP);
    $ARRAYDESPACHOTOTAL2 = $EXIEXPORTACION_ADO->obtenerTotalesInspSag2($IDOP);

    $TOTALENVASE = $ARRAYDESPACHOTOTAL[0]['ENVASE'];
    $TOTALNETO = $ARRAYDESPACHOTOTAL[0]['NETO'];
    $TOTALBRUTO = $ARRAYDESPACHOTOTAL[0]['BRUTO'];
    $TOTALENVASE2 = $ARRAYDESPACHOTOTAL2[0]['ENVASE'];
    $TOTALNETO2 = $ARRAYDESPACHOTOTAL2[0]['NETO'];
    $TOTALBRUTO2 = $ARRAYDESPACHOTOTAL2[0]['BRUTO'];
    if(empty($ARRAYTOMADO)){
        $DISABLEDT="disabled";
    }else{
        $DISABLEDT="";
    }



    //FUNCION PARA LA OBTENCION DE LOS TOTALES DEL DETALLE ASOCIADO A INPSAG

    //IDENTIFICACIONES DE OPERACIONES
    //crear =  OBTENCION DE DATOS INICIALES PARA PODER CREAR LA INPSAG
    if ($OP == "crear") {
        //OBTENCION DE INFORMACIOND DE LA FILA DEL REGISTRO
        //ALMACENAR INFORMACION EN ARREGLO
        //LLAMADA A LA FUNCION DE CONTROLADOR verPlanta(ID), 
        //SE LE PASE UNO DE LOS DATOS OBTENIDO PREVIAMENTE A TRAVEZ DE LA URL
        $DISABLED = "disabled";
        $DISABLED2 = "";
        $DISABLED3 = "disabled";
        $DISABLED4 = "";
        $DISABLEDMENU = "disabled";
        $DISABLEDSTYLE = "style='background-color: #eeeeee;'";
        //OBTENCIONS DE LOS DATODS DE LA COLUMNAS DE LA FILA OBTENIDA
        $ARRAYINPSAG = $INPSAG_ADO->verInpsag($IDOP);
        //PASAR DATOS OBTENIDOS A VARIABLES QUE SE VISUALIZAR EN EL FORMULARIO DE LA VISTA
        foreach ($ARRAYINPSAG as $r) :
            $NUMEROVER = "" . $r['NUMERO_INPSAG'];
            $IDINPSAG = $IDOP;
            $FECHAINPSAG = "" . $r['FECHA_INPSAG'];
            $CORRELATIVOINPSAG = "" . $r['CORRELATIVO_INPSAG'];
            if( strlen($CORRELATIVOINPSAG)==0){
                $DISABLEDC="disabled";
            }else{
                $DISABLEDC="";
            }

            if (isset($_REQUEST['AGREGARFOLIO'])) {
                $folioSolicitado = isset($_REQUEST['FOLIOAGREGAR']) ? trim($_REQUEST['FOLIOAGREGAR']) : '';
                $id_dato = $_REQUEST['IDP'];
                $accion_dato = $_REQUEST['OPP'];

                if ($folioSolicitado === '') {
                    echo '<script>
                        Swal.fire({
                            icon:"warning",
                            title:"Folio requerido",
                            text:"Debe ingresar un folio para agregarlo a la inspección.",
                            showConfirmButton: true,
                            confirmButtonText:"Cerrar",
                            closeOnConfirm:false
                        }).then((result)=>{
                            location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                        })
                    </script>';
                } else {
                    $detalleFolio = $EXIEXPORTACION_ADO->obtenerDetallePorPlantaTemporadaFolio($PLANTAS, $TEMPORADAS, $folioSolicitado);
                    if (!$detalleFolio) {
                        echo '<script>
                            Swal.fire({
                                icon:"warning",
                                title:"Folio no encontrado",
                                text:"El folio ingresado no existe en la planta y temporada seleccionadas.",
                                showConfirmButton: true,
                                confirmButtonText:"Cerrar",
                                closeOnConfirm:false
                            }).then((result)=>{
                                location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                            })
                        </script>';
                    } else {
                        $folioDatos = $detalleFolio[0];
                        if ($folioDatos['ID_INPSAG'] !== null) {
                            echo '<script>
                                Swal.fire({
                                    icon:"warning",
                                    title:"Folio con inspección",
                                    text:"El folio ya está asignado a una inspección, no se puede volver a ingresar.",
                                    showConfirmButton: true,
                                    confirmButtonText:"Cerrar",
                                    closeOnConfirm:false
                                }).then((result)=>{
                                    location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                                })
                            </script>';
                        } elseif ($folioDatos['TESTADOSAG'] !== null) {
                            echo '<script>
                                Swal.fire({
                                    icon:"warning",
                                    title:"Condición SAG",
                                    text:"El folio tiene una condición SAG y no puede agregarse a la inspección.",
                                    showConfirmButton: true,
                                    confirmButtonText:"Cerrar",
                                    closeOnConfirm:false
                                }).then((result)=>{
                                    location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                                })
                            </script>';
                        } elseif ($folioDatos['ID_DESPACHOEX'] !== null || $folioDatos['ID_DESPACHO'] !== null || $folioDatos['ID_DESPACHO2'] !== null) {
                            echo '<script>
                                Swal.fire({
                                    icon:"warning",
                                    title:"Folio despachado",
                                    text:"El folio ya fue despachado y no puede agregarse a la inspección.",
                                    showConfirmButton: true,
                                    confirmButtonText:"Cerrar",
                                    closeOnConfirm:false
                                }).then((result)=>{
                                    location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                                })
                            </script>';
                        } elseif ($folioDatos['ESTADO'] != 2 || $folioDatos['ESTADO_REGISTRO'] != 1) {
                            echo '<script>
                                Swal.fire({
                                    icon:"warning",
                                    title:"Folio no vigente",
                                    text:"El folio no está vigente, por lo que no puede añadirse a la inspección.",
                                    showConfirmButton: true,
                                    confirmButtonText:"Cerrar",
                                    closeOnConfirm:false
                                }).then((result)=>{
                                    location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                                })
                            </script>';
                        } else {
                            $existenciasDisponibles = $EXIEXPORTACION_ADO->buscarPorPlantaTemporadaEstadoSagNullInpsagFolio($PLANTAS, $TEMPORADAS, $folioSolicitado);

                            if ($existenciasDisponibles) {
                                foreach ($existenciasDisponibles as $existencia) :
                                    $EXIEXPORTACION->__SET('ID_INPSAG', $id_dato);
                                    $EXIEXPORTACION->__SET('ID_EXIEXPORTACION', $existencia['ID_EXIEXPORTACION']);
                                    $EXIEXPORTACION_ADO->actualizarSelecionarSagCambiarEstado($EXIEXPORTACION);

                                    $AUSUARIO_ADO->agregarAusuario2("NULL",1,2,"".$_SESSION["NOMBRE_USUARIO"].", Se agrego la Existencia de producto terminado a la Inspección SAG por folio.","fruta_exiexportacion", "NULL" ,$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'], $_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );
                                endforeach;

                                echo '<script>
                                    Swal.fire({
                                        icon:"success",
                                        title:"Folio agregado",
                                        text:"Se agregó el folio a la inspección.",
                                        showConfirmButton: true,
                                        confirmButtonText:"Volver a Inspección",
                                        closeOnConfirm:false
                                    }).then((result)=>{
                                        location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                                    })
                                </script>';
                            } else {
                                echo '<script>
                                    Swal.fire({
                                        icon:"warning",
                                        title:"Folio sin existencias",
                                        text:"El folio no tiene existencias disponibles para agregar.",
                                        showConfirmButton: true,
                                        confirmButtonText:"Cerrar",
                                        closeOnConfirm:false
                                    }).then((result)=>{
                                        location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";
                                    })
                                </script>';
                            }
                        }
                    }
                }
            }
            $FECHAINGRESOINPSAG = "" . $r['FECHA_INGRESOR'];
            $FECHAMODIFCIACIONINPSAG = "" . $r['FECHA_MODIFICACIONR'];
            $OBSERVACIONINPSAG = "" . $r['OBSERVACION_INPSAG'];
            $LOTE = ""; // Inicializar vacío para nuevo registro
            $CIF = "" . $r['CIF_INPSAG'];
            $TESTADOSAG = "" . $r['TESTADOSAG'];
            $ESTADO = "" . $r['ESTADO'];
            $INPECTOR = "" . $r['ID_INPECTOR'];
            $CONTRAPARTE = "" . $r['ID_CONTRAPARTE'];
            $TINPSAG = "" . $r['ID_TINPSAG'];
            $TMANEJO = "" . $r['ID_MANEJO'];
            $PAIS1 = "" . $r['ID_PAIS1'];
            $PAIS2 = "" . $r['ID_PAIS2'];
            $PAIS3 = "" . $r['ID_PAIS3'];
            $PAIS4 = "" . $r['ID_PAIS4'];
            $PLANTA = "" . $r['ID_PLANTA'];
            $TEMPORADA = "" . $r['ID_TEMPORADA'];            
            $ARRAYCONTRAPARTE =  $CONTRAPARTE_ADO->listarContraparteCBX();
            $ARRAYINPECTOR = $INPECTOR_ADO->listarInpectorCBX();
        endforeach;
    }

    //editar =  OBTENCION DE DATOS PARA LA EDICION DE REGISTRO
    if ($OP == "editar") {

        //OBTENCION DE INFORMACIOND DE LA FILA DEL REGISTRO
        //ALMACENAR INFORMACION EN ARREGLO
        //LLAMADA A LA FUNCION DE CONTROLADOR verPlanta(ID), 
        //SE LE PASE UNO DE LOS DATOS OBTENIDO PREVIAMENTE A TRAVEZ DE LA URL
        $DISABLED = "disabled";
        $DISABLED2 = "";
        $DISABLED3 = "disabled";
        $DISABLED4 = "";
        $DISABLEDMENU = "disabled";
        $DISABLEDSTYLE = "style='background-color: #eeeeee;'";
        $ARRAYINPSAG = $INPSAG_ADO->verInpsag($IDOP);
        //OBTENCIONS DE LOS DATODS DE LA COLUMNAS DE LA FILA OBTENIDA
        //PASAR DATOS OBTENIDOS A VARIABLES QUE SE VISUALIZAR EN EL FORMULARIO DE LA VISTA
        foreach ($ARRAYINPSAG as $r) :
            $NUMEROVER = "" . $r['NUMERO_INPSAG'];
            $IDINPSAG = $IDOP;
            $CORRELATIVOINPSAG = "" . $r['CORRELATIVO_INPSAG'];
            if( strlen($CORRELATIVOINPSAG)==0){
                $DISABLEDC="disabled";
            }else{
                $DISABLEDC="";
            }
            $FECHAINPSAG = "" . $r['FECHA_INPSAG'];
            $FECHAINGRESOINPSAG = "" . $r['FECHA_INGRESOR'];
            $FECHAMODIFCIACIONINPSAG = "" . $r['FECHA_MODIFICACIONR'];
            $OBSERVACIONINPSAG = "" . $r['OBSERVACION_INPSAG'];
            $LOTE = ""; // Inicializar vacío para edición
            $CIF = "" . $r['CIF_INPSAG'];
            $TESTADOSAG = "" . $r['TESTADOSAG'];
            $ESTADO = "" . $r['ESTADO'];
            $INPECTOR = "" . $r['ID_INPECTOR'];
            $CONTRAPARTE = "" . $r['ID_CONTRAPARTE'];
            $TINPSAG = "" . $r['ID_TINPSAG'];
            $TMANEJO = "" . $r['ID_MANEJO'];
            $PAIS1 = "" . $r['ID_PAIS1'];
            $PAIS2 = "" . $r['ID_PAIS2'];
            $PAIS3 = "" . $r['ID_PAIS3'];
            $PAIS4 = "" . $r['ID_PAIS4'];
            $PLANTA = "" . $r['ID_PLANTA'];
            $TEMPORADA = "" . $r['ID_TEMPORADA'];
            $ARRAYCONTRAPARTE =  $CONTRAPARTE_ADO->listarContraparteCBX();
            $ARRAYINPECTOR = $INPECTOR_ADO->listarInpectorCBX();
        endforeach;
    }

    //ver =  OBTENCION DE DATOS PARA LA VISUALIZACION DEL REGISTRO
    if ($OP == "ver") {
        //DESABILITAR INPUT DEL FORMULARIO
        //PARA QUE NO MODIFIQUE NIGUNA INFORMACION, OBJETIVO ES VISUALIZAR INFORMACION
        $DISABLED = "disabled";
        $DISABLED2 = "disabled";
        $DISABLED3 = "disabled";
        $DISABLED4 = "disabled";
        $DISABLEDMENU = "disabled";
        $DISABLEDSTYLE = "style='background-color: #eeeeee;'";
        //OBTENCION DE INFORMACIOND DE LA FILA DEL REGISTRO
        //ALMACENAR INFORMACION EN ARREGLO
        //LLAMADA A LA FUNCION DE CONTROLADOR verPlanta(ID), 
        //SE LE PASE UNO DE LOS DATOS OBTENIDO PREVIAMENTE A TRAVEZ DE LA URL
        $ARRAYINPSAG = $INPSAG_ADO->verInpsag($IDOP);
        //OBTENCIONS DE LOS DATODS DE LA COLUMNAS DE LA FILA OBTENIDA
        //PASAR DATOS OBTENIDOS A VARIABLES QUE SE VISUALIZAR EN EL FORMULARIO DE LA VISTA

        foreach ($ARRAYINPSAG as $r) :
            $NUMEROVER = "" . $r['NUMERO_INPSAG'];
            $IDINPSAG = $IDOP;
            $CORRELATIVOINPSAG = "" . $r['CORRELATIVO_INPSAG'];
            if( strlen($CORRELATIVOINPSAG)==0){
                $DISABLEDC="disabled";
            }else{
                $DISABLEDC="";
            }
            $FECHAINPSAG = "" . $r['FECHA_INPSAG'];
            $FECHAINGRESOINPSAG = "" . $r['FECHA_INGRESOR'];
            $FECHAMODIFCIACIONINPSAG = "" . $r['FECHA_MODIFICACIONR'];
            $OBSERVACIONINPSAG = "" . $r['OBSERVACION_INPSAG'];
            $LOTE = ""; // Inicializar vacío para visualización
            $CIF = "" . $r['CIF_INPSAG'];
            $TESTADOSAG = "" . $r['TESTADOSAG'];
            $ESTADO = "" . $r['ESTADO'];
            $INPECTOR = "" . $r['ID_INPECTOR'];
            $CONTRAPARTE = "" . $r['ID_CONTRAPARTE'];
            $TINPSAG = "" . $r['ID_TINPSAG'];
            $TMANEJO = "" . $r['ID_MANEJO'];
            $PAIS1 = "" . $r['ID_PAIS1'];
            $PAIS2 = "" . $r['ID_PAIS2'];
            $PAIS3 = "" . $r['ID_PAIS3'];
            $PAIS4 = "" . $r['ID_PAIS4'];
            $PLANTA = "" . $r['ID_PLANTA'];
            $TEMPORADA = "" . $r['ID_TEMPORADA'];
            $ARRAYCONTRAPARTE =  $CONTRAPARTE_ADO->listarContraparteCBX();
            $ARRAYINPECTOR = $INPECTOR_ADO->listarInpectorCBX();
        endforeach;
    }
}
//PROCESO PARA OBTENER LOS DATOS DEL FORMULARIO  Y MANTENERLO AL ACTUALIZACION QUE REALIZA EL SELECT DE CONDUCTOR
if (isset($_POST)) {

    if (isset($_REQUEST['FOLIOAGREGAR'])) {
        $FOLIOAGREGAR = "" . $_REQUEST['FOLIOAGREGAR'];
    }

    if (isset($_REQUEST['FECHAINPSAG'])) {
        $FECHAINPSAG = "" . $_REQUEST['FECHAINPSAG'];
    }
    
    if (isset($_REQUEST['CORRELATIVOINPSAG'])) {
        $CORRELATIVOINPSAG = "" . $_REQUEST['CORRELATIVOINPSAG'];
    }
    if (isset($_REQUEST['LOTE'])) {
        $LOTE = "" . $_REQUEST['LOTE'];
    }
    if (isset($_REQUEST['TINPSAG'])) {
        $TINPSAG = "" . $_REQUEST['TINPSAG'];
    }
    if (isset($_REQUEST['TMANEJO'])) {
        $TMANEJO = "" . $_REQUEST['TMANEJO'];
    }
    if (isset($_REQUEST['TESTADOSAG'])) {
        $TESTADOSAG = "" . $_REQUEST['TESTADOSAG'];
    }
    if (isset($_REQUEST['INPECTOR'])) {
        $INPECTOR = "" . $_REQUEST['INPECTOR'];
    }
    if (isset($_REQUEST['CIF'])) {
        $CIF = "" . $_REQUEST['CIF'];
    }
    if (isset($_REQUEST['CONTRAPARTE'])) {
        $CONTRAPARTE = "" . $_REQUEST['CONTRAPARTE'];
    }
    if (isset($_REQUEST['FECHAINGRESOINPSAG'])) {
        $FECHAINGRESOINPSAG = "" . $_REQUEST['FECHAINGRESOINPSAG'];
    }
    if (isset($_REQUEST['FECHAMODIFCIACIONINPSAG'])) {
        $FECHAMODIFCIACIONINPSAG = "" . $_REQUEST['FECHAMODIFCIACIONINPSAG'];
    }
    if (isset($_REQUEST['PAIS1'])) {
        $PAIS1 = "" . $_REQUEST['PAIS1'];
    }
    if (isset($_REQUEST['PAIS2'])) {
        $PAIS2 = "" . $_REQUEST['PAIS2'];
    }
    if (isset($_REQUEST['PAIS3'])) {
        $PAIS3 = "" . $_REQUEST['PAIS3'];
    }
    if (isset($_REQUEST['PAIS4'])) {
        $PAIS4 = "" . $_REQUEST['PAIS4'];
    }
    if (isset($_REQUEST['PLANTA'])) {
        $PLANTA = "" . $_REQUEST['PLANTA'];
    }
    if (isset($_REQUEST['TEMPORADA'])) {
        $TEMPORADA = "" . $_REQUEST['TEMPORADA'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Registro Inspección</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="">
    <meta name="author" content="">
    <!- LLAMADA DE LOS ARCHIVOS NECESARIOS PARA DISEÑO Y FUNCIONES BASE DE LA VISTA -!>
        <?php include_once "../../assest/config/urlHead.php"; ?>
        <!- FUNCIONES BASES -!>
            <script type="text/javascript">
                //VALIDACION DE FORMULARIO
                function validacion() {
                     



                    FECHAINPSAG = document.getElementById("FECHAINPSAG").value;
                    CORRELATIVOINPSAG = document.getElementById("CORRELATIVOINPSAG").value;


                    
                    TINPSAG = document.getElementById("TINPSAG").selectedIndex;
                    TMANEJO = document.getElementById("TMANEJO").selectedIndex;
                    /* Validación de TESTADOSAG global eliminada (ahora por fila) */




                    if (INPECTOR == null || INPECTOR == 0) {
                        document.form_reg_dato.INPECTOR.focus();
                        document.form_reg_dato.INPECTOR.style.borderColor = "#FF0000";
                        document.getElementById('val_inpector').innerHTML = "NO HA SELECIONADO ALTERNATIVA";
                        return false
                    }
                    document.form_reg_dato.INPECTOR.style.borderColor = "#4AF575";

                    /*
                                        if (CIF == null || CIF == 0) {
                                            document.form_reg_dato.CIF.focus();
                                            document.form_reg_dato.INPECTOR.style.borderColor = "#FF0000";
                                            document.getElementById('val_cif').innerHTML = "NO HA INGRESADO DATOS";
                                            return false
                                        }
                                        document.form_reg_dato.CIF.style.borderColor = "#4AF575";
                                        */
                                       

                    if (CONTRAPARTE == null || CONTRAPARTE == 0) {
                        document.form_reg_dato.CONTRAPARTE.focus();
                        document.form_reg_dato.CONTRAPARTE.style.borderColor = "#FF0000";
                        document.getElementById('val_contraparte').innerHTML = "NO HA SELECIONADO ALTERNATIVA";
                        return false
                    }
                    document.form_reg_dato.CONTRAPARTE.style.borderColor = "#4AF575";

                    if (PAIS1 == null || PAIS1 == 0) {
                        document.form_reg_dato.PAIS1.focus();
                        document.form_reg_dato.PAIS1.style.borderColor = "#FF0000";
                        document.getElementById('val_pais1').innerHTML = "NO HA SELECIONADO ALTERNATIVA";
                        return false
                    }
                    document.form_reg_dato.PAIS1.style.borderColor = "#4AF575";

                    if (PAIS2) {
                        if (PAIS2 == PAIS1) {
                            document.form_reg_dato.PAIS2.focus();
                            document.form_reg_dato.PAIS2.style.borderColor = "#FF0000";
                            document.getElementById('val_pais2').innerHTML = "LA SELECCION TIENE QUE SER DISTINTO A LOS OTROS";
                            return false
                        }
                        document.form_reg_dato.PAIS2.style.borderColor = "#4AF575";
                    }
                    if (PAIS3) {
                        if (PAIS3 == PAIS1 || PAIS3 == PAIS2 || PAIS3 == PAIS4) {
                            document.form_reg_dato.PAIS3.focus();
                            document.form_reg_dato.PAIS3.style.borderColor = "#FF0000";
                            document.getElementById('val_pais3').innerHTML = "LA SELECCION TIENE QUE SER DISTINTO A LOS OTROS";
                            return false
                        }
                        document.form_reg_dato.PAIS3.style.borderColor = "#4AF575";
                    }
                    if (PAIS4) {
                        if (PAIS4 == PAIS1 || PAIS4 == PAIS2 || PAIS4 == PAIS3) {
                            document.form_reg_dato.PAIS4.focus();
                            document.form_reg_dato.PAIS4.style.borderColor = "#FF0000";
                            document.getElementById('val_pais4').innerHTML = "LA SELECCION TIENE QUE SER DISTINTO A LOS OTROS";
                            return false
                        }
                        document.form_reg_dato.PAIS4.style.borderColor = "#4AF575";
                    }



                    /*
                    if (OBSERVACIONINPSAG == null || OBSERVACIONINPSAG.length == 0 || /^\s+$/.test(OBSERVACIONINPSAG)) {
                        document.form_reg_dato.OBSERVACIONINPSAG.focus();
                        document.form_reg_dato.OBSERVACIONINPSAG.style.borderColor = "#FF0000";
                        document.getElementById('val_observacion').innerHTML = "NO A INGRESADO DATO";
                        return false
                    }
                    document.form_reg_dato.OBSERVACIONINPSAG.style.borderColor = "#4AF575"; 
                    */
                }

                //FUNCION PARA REALIZAR UNA ACTUALIZACION DEL FORMULARIO DE REGISTRO DE INPSAG
                function refrescar() {
                    document.getElementById("form_reg_dato").submit();
                }

                //FUNCION PARA ABRIR VENTANA QUE SE ENCUENTRA LA OPERACIONES DE DETALLE DE INPSAG
                function abrirVentana(url) {
                    var opciones =
                        "'directories=no, location=no, menubar=no, scrollbars=yes, statusbar=no, tittlebar=no, width=1600, height=1000'";
                    window.open(url, 'window', opciones);
                }

                //REDIRECCIONAR A LA PAGINA SELECIONADA
                function irPagina(url) {
                    location.href = "" + url;
                }

                function abrirPestana(url) {
                    var win = window.open(url, '_blank');
                    win.focus();
                }

                // FUNCIÓN PARA ACTUALIZAR EL LOTE VIA AJAX
                function actualizarLote(idExiexportacion, valorLote) {
                    if (valorLote.trim() === '') {
                        valorLote = null;
                    }
                    
                    console.log('Actualizando lote:', idExiexportacion, valorLote);
                    
                    $.ajax({
                        url: '',
                        type: 'POST',
                        data: {
                            ACTUALIZAR_LOTE: true,
                            ID_EXIEXPORTACION: idExiexportacion,
                            LOTE: valorLote
                        },
                        success: function(response) {
                            //console.log('Respuesta del servidor:', response);
                            console.log('Lote actualizado correctamente');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error AJAX:', status, error);
                            console.error('Respuesta:', xhr.responseText);
                            alert('Error al actualizar el lote: ' + error);
                        }
                    });
                }
             
            </script>

</head>

<body class="hold-transition light-skin fixed sidebar-mini theme-primary" >
    <div class="wrapper">
        <!- LLAMADA AL MENU PRINCIPAL DE LA PAGINA-!>
            <?php include_once "../../assest/config/menuFruta.php";
            ?>
            <div class="content-wrapper">
                <div class="container-full">
                    <!-- Content Header (Page header) -->
                    <div class="content-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h3 class="page-title">Operaciones SAG </h3>
                                <div class="d-inline-block align-items-center">
                                    <nav>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="index.php"><i class="mdi mdi-home-outline"></i></a></li>
                                            <li class="breadcrumb-item" aria-current="page">Módulo</li>
                                            <li class="breadcrumb-item" aria-current="page">Operaciones SAG</li>
                                            <li class="breadcrumb-item" aria-current="page">Inspección SAG</li>
                                            <li class="breadcrumb-item active" aria-current="page"> <a href="#"> Registro Inspección </a>  </li>
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
                            <div class="box-header with-border bg-primary">                                   
                                <h4 class="box-title">Registro de Inspección</h4>                                        
                            </div>
                            <form class="form" role="form" method="post" name="form_reg_dato" id="form_reg_dato">
                                <div class="box-body ">
                                    <div class="row">
                                        <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <input type="hidden" class="form-control" placeholder="ID EMPRESA" id="EMPRESA" name="EMPRESA" value="<?php echo $EMPRESAS; ?>" />
                                                <input type="hidden" class="form-control" placeholder="ID PLANTA" id="PLANTA" name="PLANTA" value="<?php echo $PLANTAS; ?>" />
                                                <input type="hidden" class="form-control" placeholder="ID TEMPORADA" id="TEMPORADA" name="TEMPORADA" value="<?php echo $TEMPORADAS; ?>" />
                                                <input type="hidden" class="form-control" placeholder="ID EMPRESA" id="EMPRESAE" name="EMPRESAE" value="<?php echo $EMPRESA; ?>" />
                                                <input type="hidden" class="form-control" placeholder="ID PLANTA" id="PLANTAE" name="PLANTAE" value="<?php echo $PLANTA; ?>" />
                                                <input type="hidden" class="form-control" placeholder="ID TEMPORADA" id="TEMPORADAE" name="TEMPORADAE" value="<?php echo $TEMPORADA; ?>" />

                                                <input type="hidden" class="form-control" id="TOTALENVASE" name="TOTALENVASE" value="<?php echo $TOTALENVASE; ?>" />
                                                <input type="hidden" class="form-control" id="TOTALNETO" name="TOTALNETO" value="<?php echo $TOTALNETO; ?>" />
                                                <input type="hidden" class="form-control" id="TOTALBRUTO" name="TOTALBRUTO" value="<?php echo $TOTALBRUTO; ?>" />

                                                <input type="hidden" class="form-control" placeholder="ID DESPACHOEX" id="IDP" name="IDP" value="<?php echo $IDOP; ?>" />
                                                <input type="hidden" class="form-control" placeholder="OP DESPACHOEX" id="OPP" name="OPP" value="<?php echo $OP; ?>" />
                                                <input type="hidden" class="form-control" placeholder="URL DESPACHOEX" id="URLP" name="URLP" value="registroInpsag" />

                                                
                                                <input type="hidden" class="form-control" id="ESTADO_INPSAG" name="ESTADO_INPSAG" value="<?php echo $ESTADO; ?>" />
<label>Número Inspección</label>
                                                <input type="hidden" class="form-control" placeholder="Número Inspección" id="ID" name="ID" value="<?php echo $IDINPSAG; ?>" />
                                                <input type="text" class="form-control" style="background-color: #eeeeee;" placeholder="Id Inpsag" id="IDINPSAG" name="IDINPSAG" value="<?php echo $NUMEROVER; ?>" disabled />
                                                <label id="val_id" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-6 col-xl-1 col-lg-1 col-md-6 col-sm-6 col-6 col-xs-6">
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Fecha Ingreso</label>
                                                <input type="hidden" class="form-control" placeholder="FECHA MODIFICACION" id="FECHAINGRESOINPSAGE" name="FECHAINGRESOINPSAGE" value="<?php echo $FECHAINGRESOINPSAG; ?>" />
                                                <input type="date" class="form-control" style="background-color: #eeeeee;" placeholder="Fecha Ingreso" id="FECHAINGRESOINPSAG" name="FECHAINGRESOINPSAG" value="<?php echo $FECHAINGRESOINPSAG; ?>" disabled />
                                                <label id="val_fechai" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-4 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Fecha Modificación</label>
                                                <input type="hidden" class="form-control" placeholder="FECHA MODIFICACION" id="FECHAMODIFCIACIONINPSAGE" name="FECHAMODIFCIACIONINPSAGE" value="<?php echo $FECHAMODIFCIACIONINPSAG; ?>" />
                                                <input type="date" class="form-control" style="background-color: #eeeeee;" placeholder="Fecha Modificación Inspección" id="FECHAMODIFCIACIONINPSAG" name="FECHAMODIFCIACIONINPSAG" value="<?php echo $FECHAMODIFCIACIONINPSAG; ?>" disabled />
                                                <label id="val_fecham" class="validacion"> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xxl-2 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Fecha Inspección</label>
                                                <input type="hidden" class="form-control" placeholder="Fecha Inspección Sag" id="FECHAINPSAGE" name="FECHAINPSAGE" value="<?php echo $FECHAINPSAG; ?>" />
                                                <input type="date" class="form-control"  placeholder="Fecha Inspección " id="FECHAINPSAG" name="FECHAINPSAG" value="<?php echo $FECHAINPSAG; ?>" <?php echo $DISABLED2; ?>  />
                                                <label id="val_fechar" class="validacion"> </label>
                                            </div>
                                        </div> 
                                        <div class="col-xxl-2 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Correlativo de Inspección</label>
                                                <input type="hidden" class="form-control" placeholder="Correlativo de Inspección" id="CORRELATIVOINPSAGE" name="CORRELATIVOINPSAGE" value="<?php echo $CORRELATIVOINPSAG; ?>" />
                                                <input type="number" class="form-control"  placeholder="Correlativo de Inspección " id="CORRELATIVOINPSAG" name="CORRELATIVOINPSAG" value="<?php echo $CORRELATIVOINPSAG; ?>" <?php echo $DISABLED2; ?>  />
                                                <label id="val_correlativo" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12">
                                            <div class="form-group">
                                                <label>Tipo Inspección </label>
                                                <input type="hidden" class="form-control" placeholder="TINPSAGE" id="TINPSAGE" name="TINPSAGE" value="<?php echo $TINPSAG; ?>" />
                                                <select class="form-control select2" id="TINPSAG" name="TINPSAG" style="width: 100%;" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?>>
                                                    <option></option>
                                                    <?php foreach ($ARRAYTINPSAG as $r) : ?>
                                                        <?php if ($ARRAYTINPSAG) {    ?>
                                                            <option value="<?php echo $r['ID_TINPSAG']; ?>" <?php if ($TINPSAG == $r['ID_TINPSAG']) {  echo "selected";  } ?>>
                                                                <?php echo $r['NOMBRE_TINPSAG'] ?>
                                                            </option>
                                                        <?php } else { ?>
                                                            <option>No Hay Datos Registrados </option>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label id="val_tinpsag" class="validacion"> </label>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12">
                                            <div class="form-group">
                                                <label>Tipo Manejo </label>
                                                <input type="hidden" class="form-control" placeholder="TMANEJOE" id="TMANEJOE" name="TMANEJOE" value="<?php echo $TMANEJO; ?>" />
                                                <select class="form-control select2" id="TMANEJO" name="TMANEJO" style="width: 100%;" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?>>
                                                    <option></option>
                                                    <?php foreach ($ARRAYTMANEJO as $r) : ?>
                                                        <?php if ($ARRAYTMANEJO) {    ?>
                                                            <option value="<?php echo $r['ID_TMANEJO']; ?>" <?php if ($TMANEJO == $r['ID_TMANEJO']) {  echo "selected";  } ?>>
                                                                <?php echo $r['NOMBRE_TMANEJO'] ?>
                                                            </option>
                                                        <?php } else { ?>
                                                            <option>No Hay Datos Registrados </option>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label id="val_tmanejo" class="validacion"> </label>
                                            </div>
                                        </div>

                                        <div class="col-xxl-3 col-xl-4 col-lg-6 col-md-12 col-sm-12 col-12 col-xs-12">
                                            </div>
                                        <div class="col-xxl-3 col-xl-5 col-lg-9 col-md-9 col-sm-9 col-9 col-xs-9">
                                            <div class="form-group">
                                                <label>Inspector</label>
                                                <input type="hidden" class="form-control" placeholder="INPECTORE" id="INPECTORE" name="INPECTORE" value="<?php echo $INPECTOR; ?>" />
                                                <select class="form-control select2" id="INPECTOR" name="INPECTOR" style="width: 100%;" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?>>
                                                    <option></option>
                                                    <?php foreach ($ARRAYINPECTOR as $r) : ?>
                                                        <?php if ($ARRAYINPECTOR) {    ?>
                                                            <option value="<?php echo $r['ID_INPECTOR']; ?>" <?php if ($INPECTOR == $r['ID_INPECTOR']) {  echo "selected"; } ?>> 
                                                                <?php echo $r['NOMBRE_INPECTOR'] ?>
                                                            </option>
                                                        <?php } else { ?>
                                                            <option>No Hay Datos Registrados </option>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label id="val_inpector" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-1 col-xl-1 col-lg-3 col-md-3 col-sm-3 col-3 col-xs-3">
                                            <div class="form-group">
                                                <br>
                                                <button type="button" class="btn btn-success btn-block" data-toggle="tooltip" title="Agregar Inpector" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?> id="defecto" name="pop" Onclick="abrirVentana('registroPopInpector.php' ); ">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Valor SIF </label>
                                                <input type="hidden" class="form-control" placeholder="CIFE" id="CIFE" name="CIFE" value="<?php echo $CIF; ?>" />
                                                <input type="number" class="form-control" placeholder="Valor SIF" id="CIF" name="CIF" value="<?php echo $CIF; ?>" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?> />
                                                <label id="val_cif" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-3 col-xl-5 col-lg-9 col-md-9 col-sm-9 col-9 col-xs-9">
                                            <div class="form-group">
                                                <label>Contraparte</label>
                                                <input type="hidden" class="form-control" placeholder="CONTRAPARTEE" id="CONTRAPARTEE" name="CONTRAPARTEE" value="<?php echo $CONTRAPARTE; ?>" />
                                                <select class="form-control select2" id="CONTRAPARTE" name="CONTRAPARTE" style="width: 100%;" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?>>
                                                    <option></option>
                                                    <?php foreach ($ARRAYCONTRAPARTE as $r) : ?>
                                                        <?php if ($ARRAYCONTRAPARTE) {    ?>
                                                            <option value="<?php echo $r['ID_CONTRAPARTE']; ?>" <?php if ($CONTRAPARTE == $r['ID_CONTRAPARTE']) {  echo "selected";  } ?>> 
                                                                <?php echo $r['NOMBRE_CONTRAPARTE'] ?> 
                                                        </option>
                                                        <?php } else { ?>
                                                            <option>No Hay Datos Registrados </option>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label id="val_contraparte" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-1 col-xl-1 col-lg-3 col-md-3 col-sm-3 col-3 col-xs-3">
                                            <div class="form-group">
                                                <br>
                                                <button type="button" class="btn btn-success btn-block" data-toggle="tooltip" title="Agregar Contraparte" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?> id="defecto" name="pop" Onclick="abrirVentana('registroPopContraparte.php' ); ">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Pais 1</label>
                                                <input type="hidden" class="form-control" placeholder="PAIS1E" id="PAIS1E" name="PAIS1E" value="<?php echo $PAIS1; ?>" />
                                                <select class="form-control select2" id="PAIS1" name="PAIS1" style="width: 100%;" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?>>
                                                    <option></option>
                                                    <?php foreach ($ARRAYPAIS as $r) : ?>
                                                        <?php if ($ARRAYPAIS) {    ?>
                                                            <option value="<?php echo $r['ID_PAIS']; ?>" <?php if ($PAIS1 == $r['ID_PAIS']) {  echo "selected";  } ?>> 
                                                                <?php echo $r['NOMBRE_PAIS'] ?> 
                                                            </option>
                                                        <?php } else { ?>
                                                            <option>No Hay Datos Registrados </option>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label id="val_pais1" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Pais 2</label>
                                                <input type="hidden" class="form-control" placeholder="PAIS2E" id="PAIS2E" name="PAIS2E" value="<?php echo $PAIS2; ?>" />
                                                <select class="form-control select2" id="PAIS2" name="PAIS2" style="width: 100%;" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?>>
                                                    <option></option>
                                                    <?php foreach ($ARRAYPAIS as $r) : ?>
                                                        <?php if ($ARRAYPAIS) {    ?>
                                                            <option value="<?php echo $r['ID_PAIS']; ?>" <?php if ($PAIS2 == $r['ID_PAIS']) { echo "selected";  } ?>>
                                                                <?php echo $r['NOMBRE_PAIS'] ?> 
                                                            </option>
                                                        <?php } else { ?>
                                                            <option>No Hay Datos Registrados </option>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label id="val_pais2" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Pais 3</label>
                                                <input type="hidden" class="form-control" placeholder="PAIS3E" id="PAIS3E" name="PAIS3E" value="<?php echo $PAIS3; ?>" />
                                                <select class="form-control select2" id="PAIS3" name="PAIS3" style="width: 100%;" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?>>
                                                    <option></option>
                                                    <?php foreach ($ARRAYPAIS as $r) : ?>
                                                        <?php if ($ARRAYPAIS) {    ?>
                                                            <option value="<?php echo $r['ID_PAIS']; ?>" <?php if ($PAIS3 == $r['ID_PAIS']) { echo "selected";  } ?>>
                                                                <?php echo $r['NOMBRE_PAIS'] ?>
                                                            </option>
                                                        <?php } else { ?>
                                                            <option>No Hay Datos Registrados </option>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label id="val_pais3" class="validacion"> </label>
                                            </div>
                                        </div>
                                        <div class="col-xxl-2 col-xl-4 col-lg-6 col-md-6 col-sm-6 col-6 col-xs-6">
                                            <div class="form-group">
                                                <label>Pais 4</label>
                                                <input type="hidden" class="form-control" placeholder="PAIS4E" id="PAIS4E" name="PAIS4E" value="<?php echo $PAIS4; ?>" />
                                                <select class="form-control select2" id="PAIS4" name="PAIS4" style="width: 100%;" <?php echo $DISABLED; ?> <?php echo $DISABLED3; ?>>
                                                    <option></option>
                                                    <?php foreach ($ARRAYPAIS as $r) : ?>
                                                        <?php if ($ARRAYPAIS) {    ?>
                                                            <option value="<?php echo $r['ID_PAIS']; ?>" <?php if ($PAIS4 == $r['ID_PAIS']) { echo "selected";  } ?>> 
                                                                <?php echo $r['NOMBRE_PAIS'] ?> 
                                                            </option>
                                                        <?php } else { ?>
                                                            <option>No Hay Datos Registrados </option>
                                                        <?php } ?>
                                                    <?php endforeach; ?>
                                                </select>
                                                <label id="val_pais4" class="validacion"> </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12">
                                            <div class="form-group">
                                                <label>Observaciónes </label>
                                                <input type="hidden" class="form-control" placeholder="TRANSPORTE" id="OBSERVACIONINPSAGE" name="OBSERVACIONINPSAGE" value="<?php echo $OBSERVACIONINPSAG; ?>" />
                                                <textarea class="form-control" rows="1"  placeholder="Ingrese Nota, Observaciónes u Otro" id="OBSERVACIONINPSAG" name="OBSERVACIONINPSAG" <?php echo $DISABLED2; ?> ><?php echo $OBSERVACIONINPSAG; ?></textarea>
                                                <label id="val_observacion" class="validacion"> </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.row -->
                                <!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="toolbar">
                                        <div class="btn-group  col-xxl-4 col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12" role="group" aria-label="Acciones generales">
                                            <?php if ($OP == "") { ?>
                                                <button type="button" class="btn btn-warning " data-toggle="tooltip" title="Cancelar" name="CANCELAR" value="CANCELAR" Onclick="irPagina('registroInpsag.php');">
                                                    <i class="ti-trash"></i> Cancelar
                                                </button>
                                                <button type="submit" class="btn btn-primary" data-toggle="tooltip" title="Guardar" name="CREAR" value="CREAR"  onclick="return validacion()">
                                                    <i class="ti-save-alt"></i> Guardar
                                                </button>
                                            <?php } ?>
                                            <?php if ($OP != "") { ?>
                                                <button type="button" class="btn  btn-success " data-toggle="tooltip" title="Volver" name="VOLVER" value="VOLVER" Onclick="irPagina('listarInpsag.php'); ">
                                                    <i class="ti-back-left "></i> Volver
                                                </button>
                                                <button type="submit" class="btn btn-warning " data-toggle="tooltip" title="Guardar" name="GUARDAR" value="GUARDAR"   <?php echo $DISABLED2; ?> onclick="return validacion()">
                                                    <i class="ti-pencil-alt"></i> Guardar
                                                </button>
                                                <button type="submit" class="btn btn-danger " data-toggle="tooltip" title="Cerrar" name="CERRAR" value="CERRAR"   <?php echo $DISABLED2; ?> onclick="return validacion()">
                                                    <i class="ti-save-alt"></i> Cerrar
                                                </button>
                                            <?php } ?>
                                        </div>
                                        <div class="btn-group  col-xxl-6 col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 col-xs-12  float-right">
                                            <?php if ($OP != ""): ?>
                                                <button type="button" class="btn btn-primary  " data-toggle="tooltip" title="Informe" id="defecto" name="tarjas" Onclick="abrirPestana('../../assest/documento/informeInpsag.php?parametro=<?php echo $IDOP; ?>&&usuario=<?php echo $IDUSUARIOS; ?>');">
                                                    <i class="fa fa-file-pdf-o"></i> Informe
                                                </button>
                                                <button type="button" class="btn btn-primary  " data-toggle="tooltip" title=" S.I.F"   id="defecto" name="tarjas" Onclick="abrirPestana('../../assest/documento/informeInpsagSif.php?parametro=<?php echo $IDOP; ?>&&usuario=<?php echo $IDUSUARIOS; ?>');">
                                                    <i class="fa fa-file-pdf-o"></i>  S.I.F
                                                </button>
                                                <button type="button" class="btn  btn-info  " data-toggle="tooltip" title="Packing list" id="defecto" name="tarjas" Onclick="abrirPestana('../../assest/documento/informeInpsagPackingList.php?parametro=<?php echo $IDOP; ?>&&usuario=<?php echo $IDUSUARIOS; ?>'); ">
                                                    <i class="fa fa-file-pdf-o"></i> Packing List
                                                </button>
                                                <button type="button" class="btn  btn-info  " data-toggle="tooltip" title="Packing list USDA" id="defecto" name="tarjas" Onclick="abrirPestana('../../assest/documento/informeInpsagPackingListUsda.php?parametro=<?php echo $IDOP; ?>&&usuario=<?php echo $IDUSUARIOS; ?>'); ">
                                                    <i class="fa fa-file-pdf-o"></i> Packing List USDA
                                                </button>
                                                <button type="button" class="btn  btn-success" data-toggle="tooltip" id="defecto" name="tarjas" title="Archivo Plano" <?php echo $DISABLEDC; ?> <?php echo $DISABLEDT; ?> Onclick="abrirPestana('../../assest/csv/CsvInpsag.php?parametro=<?php echo $IDOP; ?>&&usuario=<?php echo $IDUSUARIOS; ?>'); ">
                                                    <i class="fa fa-file-excel-o"></i> Archivo Plano
                                                </button>                                                
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                        </div>

                        <?php if (isset($_GET['op'])): ?>
                            <!-- Formulario separado para selección de existencias -->
                            <form method="post" id="form1" action="registroSelecionExistenciaPTInpSag.php">
                                <input type="hidden" name="IDP" value="<?php echo $IDOP; ?>" />
                                <input type="hidden" name="OPP" value="<?php echo $OP; ?>" />
                                <input type="hidden" name="URLP" value="registroInpsag" />
                                <input type="hidden" name="URLD" value="registroSelecionExistenciaPTInpSag" />
                            </form>
                            
                            <div class="card">                                                            
                                <div class="card-header bg-info">
                                    <h4 class="card-title">Detalle de Inspeccion SAG</h4>
                                </div>

                                <div class="card-header">
                                    <div class="form-row align-items-center">
                                        <form method="post" id="form1">
                                            <input type="hidden" class="form-control" placeholder="ID DESPACHO" id="IDP" name="IDP" value="<?php echo $IDOP; ?>" />
                                            <input type="hidden" class="form-control" placeholder="OP DESPACHO" id="OPP" name="OPP" value="<?php echo $OP; ?>" />
                                            <input type="hidden" class="form-control" placeholder="URL DESPACHO" id="URLP" name="URLP" value="registroInpsag" />
                                            <input type="hidden" class="form-control" placeholder="URL SELECCIONAR" id="URLD" name="URLD" value="registroSelecionExistenciaPTInpSag" />
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-success btn-block mb-2" data-toggle="tooltip" title="Seleccion Existencia" id="SELECIONOCDURL" name="SELECIONOCDURL"
                                                    <?php echo $DISABLED2; ?>  <?php   if ($ESTADO == 0) {   echo "disabled style='background-color: #eeeeee;'"; } ?>  >
                                                    Seleccion Existencias
                                                </button>
                                            </div>
                                        </form>
                                        <form method="post" class="col-auto" id="form_agregar_folio">
                                            <input type="hidden" name="IDP" value="<?php echo $IDOP; ?>" />
                                            <input type="hidden" name="OPP" value="<?php echo $OP; ?>" />
                                            <div class="input-group mb-2">
                                                <input type="text"
                                                       class="form-control"
                                                       placeholder="Ingresar folio"
                                                       id="FOLIOAGREGAR"
                                                       name="FOLIOAGREGAR"
                                                       value="<?php echo htmlspecialchars($FOLIOAGREGAR, ENT_QUOTES, 'UTF-8'); ?>"
                                                       <?php echo $DISABLED2; ?>
                                                       <?php if ($ESTADO == 0) { echo "disabled style='background-color: #eeeeee;'"; } ?>
                                                />
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary" name="AGREGARFOLIO" id="AGREGARFOLIO" <?php echo $DISABLED2; ?> <?php if ($ESTADO == 0) { echo "disabled";} ?>>
                                                        Agregar folio
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="col-auto">
                                            <label class="sr-only" for=""></label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Total Envase</div>
                                                </div>
                                                <input type="hidden" class="form-control" id="TOTALENVASE" name="TOTALENVASE" value="<?php echo $TOTALENVASE; ?>" />
                                                <input type="text" class="form-control" placeholder="Total Envase" id="TOTALENVASEV" name="TOTALENVASEV" value="<?php echo $TOTALENVASE2; ?>" disabled />
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <label class="sr-only" for=""></label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Total Neto</div>
                                                </div>
                                                <input type="hidden" class="form-control" id="TOTALNETO" name="TOTALNETO" value="<?php echo $TOTALNETO; ?>" />
                                                <input type="text" class="form-control" placeholder="Total Neto" id="TOTALENVASEV" name="TOTALENVASEV" value="<?php echo $TOTALNETO2; ?>" disabled />
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <label class="sr-only" for=""></label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Total Bruto</div>
                                                </div>
                                                <input type="hidden" class="form-control" id="TOTALBRUTO" name="TOTALBRUTO" value="<?php echo $TOTALBRUTO; ?>" />
                                                <input type="text" class="form-control" placeholder="Total Neto" id="TOTALENVASEV" name="TOTALENVASEV" value="<?php echo $TOTALBRUTO2; ?>" disabled />
                                            </div>
                                        </div>
                                    </div>
                                </div>                                                     
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="table-responsive">
                                                    <table id="detalle" class="table-hover " style="width: 100%;">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <a href="#" class="text-warning hover-warning">
                                                                        N° Folio
                                                                    </a>
                                                                </th>
                                                                <th class="text-center">Número de Lote</th>
                                                                <th>Condición SAG</th>
                                                                <th class="text-center">Operaciónes</th>
                                                                <th>Fecha Embalado </th>
                                                                <th>Código Estandar</th>
                                                                <th>Envase/Estandar</th>
                                                                <th>Variedad</th>
                                                                <th>Cantidad Envase</th>
                                                                <th>Kilos Neto</th>
                                                                <th>% Deshidratacion</th>
                                                                <th>Kilos Deshidratacion</th>
                                                                <th>CSG</th>
                                                                <th>Productor</th>
                                                                <th>Embolsado</th>
                                                                <th>Tipo Manejo</th>
                                                                <th>Calibre </th>
                                                                <th>Embalaje </th>
                                                           
                                                         
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if ($ARRAYTOMADO) { ?>
                                                                <?php foreach ($ARRAYTOMADO as $r) : ?>
                                                                    <?php
                                                                    $ARRAYVERPRODUCTORID = $PRODUCTOR_ADO->verProductor($r['ID_PRODUCTOR']);
                                                                    if ($ARRAYVERPRODUCTORID) {
                                                                        $CSGPRODUCTOR = $ARRAYVERPRODUCTORID[0]['CSG_PRODUCTOR'];
                                                                        $NOMBREPRODUCTOR = $ARRAYVERPRODUCTORID[0]['NOMBRE_PRODUCTOR'];
                                                                    } else {
                                                                        $CSGPRODUCTOR = "Sin Datos";
                                                                        $NOMBREPRODUCTOR = "Sin Datos";
                                                                    }
                                                                    $ARRAYVERVESPECIESID = $VESPECIES_ADO->verVespecies($r['ID_VESPECIES']);
                                                                    if ($ARRAYVERVESPECIESID) {
                                                                        $NOMBREVARIEDAD = $ARRAYVERVESPECIESID[0]['NOMBRE_VESPECIES'];
                                                                    } else {
                                                                        $NOMBREVARIEDAD = "Sin Datos";
                                                                    }
                                                                    $ARRAYEVERERECEPCIONID = $EEXPORTACION_ADO->verEstandar($r['ID_ESTANDAR']);
                                                                    if ($ARRAYEVERERECEPCIONID) {
                                                                        $CODIGOESTANDAR = $ARRAYEVERERECEPCIONID[0]['CODIGO_ESTANDAR'];
                                                                        $NOMBREESTANDAR = $ARRAYEVERERECEPCIONID[0]['NOMBRE_ESTANDAR'];
                                                                    } else {
                                                                        $NOMBREESTANDAR = "Sin Datos";
                                                                        $CODIGOESTANDAR = "Sin Datos";
                                                                    }
                                                                    if ($r['EMBOLSADO'] == "1") {
                                                                        $EMBOLSADO =  "SI";
                                                                    }
                                                                    if ($r['EMBOLSADO'] == "0") {
                                                                        $EMBOLSADO =  "NO";
                                                                    }
                                                                    $ARRAYTMANEJO = $TMANEJO_ADO->verTmanejo($r['ID_TMANEJO']);
                                                                    if ($ARRAYTMANEJO) {
                                                                        $NOMBRETMANEJO = $ARRAYTMANEJO[0]['NOMBRE_TMANEJO'];
                                                                    } else {
                                                                        $NOMBRETMANEJO = "Sin Datos";
                                                                    }
                                                                    $ARRAYTCALIBRE = $TCALIBRE_ADO->verCalibre($r['ID_TCALIBRE']);
                                                                    if ($ARRAYTCALIBRE) {
                                                                        $NOMBRETCALIBRE = $ARRAYTCALIBRE[0]['NOMBRE_TCALIBRE'];
                                                                    } else {
                                                                        $NOMBRETCALIBRE = "Sin Datos";
                                                                    }
                                                                    $ARRAYTEMBALAJE = $TEMBALAJE_ADO->verEmbalaje($r['ID_TEMBALAJE']);
                                                                    if ($ARRAYTEMBALAJE) {
                                                                        $NOMBRETEMBALAJE = $ARRAYTEMBALAJE[0]['NOMBRE_TEMBALAJE'];
                                                                    } else {
                                                                        $NOMBRETEMBALAJE = "Sin Datos";
                                                                    }
                                                                    ?>
                                                                    <tr class="center">
                                                                        <td>
                                                                            <a href="#" class="text-warning hover-warning">
                                                                                <?php
                                                                                echo $r['FOLIO_AUXILIAR_EXIEXPORTACION'];
                                                                                ?>
                                                                            </a>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" 
                                                                                   class="form-control" 
                                                                                   name="LOTE_<?php echo $r['ID_EXIEXPORTACION']; ?>" 
                                                                                   value="<?php echo htmlspecialchars($r['LOTE']); ?>" 
                                                                                   placeholder="Número de lote"
                                                                                   onblur="actualizarLote(<?php echo $r['ID_EXIEXPORTACION']; ?>, this.value)"
                                                                                   <?php echo $DISABLED2; ?>
                                                                                   <?php if ($ESTADO == 0) { echo "disabled";} ?> />
                                                                        </td>

                                                                       <td>
                                                                            <!-- Input hidden para asegurar que el valor siempre se envíe -->
                                                                            <input type="hidden" 
                                                                                   id="TESTADOSAG_HIDDEN_<?php echo $r['ID_EXIEXPORTACION']; ?>"
                                                                                   name="TESTADOSAG_ROW[<?php echo $r['ID_EXIEXPORTACION']; ?>]" 
                                                                                   value="<?php echo isset($r['TESTADOSAG']) ? $r['TESTADOSAG'] : ''; ?>" />
                                                                            
                                                                            <select class="form-control select2 estado-sag-select" 
                                                                                    id="TESTADOSAG_<?php echo $r['ID_EXIEXPORTACION']; ?>" 
                                                                                    data-target="TESTADOSAG_HIDDEN_<?php echo $r['ID_EXIEXPORTACION']; ?>"
                                                                                    style="width:100%;"
                                                                                    onchange="document.getElementById('TESTADOSAG_HIDDEN_<?php echo $r['ID_EXIEXPORTACION']; ?>').value = this.value;"
                                                                                    <?php if ($ESTADO == 0) { echo 'disabled style="pointer-events:none; opacity:0.6;"'; } ?>>
                                                                                <option value=""></option>
                                                                                <option value="1" <?php if (isset($r['TESTADOSAG']) && $r['TESTADOSAG'] == "1") echo "selected"; ?>>En Inspección</option>
                                                                                <option value="2" <?php if (isset($r['TESTADOSAG']) && $r['TESTADOSAG'] == "2") echo "selected"; ?>>Aprobado Origen</option>
                                                                                <option value="3" <?php if (isset($r['TESTADOSAG']) && $r['TESTADOSAG'] == "3") echo "selected"; ?>>Aprobado USDA</option>
                                                                                <option value="4" <?php if (isset($r['TESTADOSAG']) && $r['TESTADOSAG'] == "4") echo "selected"; ?>>Fumigado</option>
                                                                                <option value="5" <?php if (isset($r['TESTADOSAG']) && $r['TESTADOSAG'] == "5") echo "selected"; ?>>Rechazado</option>
                                                                            </select>
                                                                        </td>

                                                                        <td>
                                                                            <form method="post" id="form1">
                                                                                <input type="hidden" class="form-control" placeholder="ID QUITAR" id="IDQUITAR" name="IDQUITAR" value="<?php echo $r['ID_EXIEXPORTACION']; ?>" />
                                                                                <div class="btn-group btn-rounded btn-block" role="group" aria-label="Operaciones Detalle">
                                                                                    <button type="submit" class="btn btn-sm btn-danger  " id="QUITAR" name="QUITAR" data-toggle="tooltip" title="Quitar Existencia" <?php echo $DISABLED2; ?>  <?php if ($ESTADO == 0) { echo "disabled";} ?>>
                                                                                        <i class="ti-close"></i><br> Quitar
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </td>
                                                                        <td><?php echo $r['EMBALADO']; ?></td>
                                                                        <td><?php echo $CODIGOESTANDAR; ?></td>
                                                                        <td><?php echo $NOMBREESTANDAR; ?></td>
                                                                        <td><?php echo $NOMBREVARIEDAD; ?></td>
                                                                        <td><?php echo $r['ENVASE']; ?></td>
                                                                        <td><?php echo $r['NETO']; ?></td>
                                                                        <td><?php echo $r['PORCENTAJE']; ?></td>
                                                                        <td><?php echo $r['DESHIRATACION']; ?></td>
                                                                        <td><?php echo $CSGPRODUCTOR; ?></td>
                                                                        <td><?php echo $NOMBREPRODUCTOR; ?></td>
                                                                        <td><?php echo $EMBOLSADO; ?></td>
                                                                        <td><?php echo $NOMBRETMANEJO; ?></td>
                                                                        <td><?php echo $NOMBRETCALIBRE; ?></td>
                                                                        <td><?php echo $NOMBRETEMBALAJE; ?></td>
                                                                        
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
                            </div>
                        <?php endif ?>
                        </form>
                        </div>
                        <!--.row -->
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
                $ARRAYNUMERO = $INPSAG_ADO->obtenerNumero($_REQUEST['PLANTA'], $_REQUEST['TEMPORADA']);
                $NUMERO = $ARRAYNUMERO[0]['NUMERO'] + 1;
                //UTILIZACION METODOS SET DEL MODELO
                //SETEO DE ATRIBUTOS DE LA CLASE, OBTENIDO EN EL FORMULARIO
                $INPSAG->__SET('NUMERO_INPSAG', $NUMERO);
                $INPSAG->__SET('FECHA_INPSAG', $_REQUEST['FECHAINPSAG']);
                $INPSAG->__SET('CORRELATIVO_INPSAG', $_REQUEST['CORRELATIVOINPSAG']);
                $INPSAG->__SET('OBSERVACION_INPSAG', $_REQUEST['OBSERVACIONINPSAG']);
                $INPSAG->__SET('CIF_INPSAG', $_REQUEST['CIF']);
                $INPSAG->__SET('ID_TINPSAG', $_REQUEST['TINPSAG']);
                $INPSAG->__SET('ID_TMANEJO', $_REQUEST['TMANEJO']);
                $INPSAG->__SET('ID_INPECTOR', $_REQUEST['INPECTOR']);
                $INPSAG->__SET('ID_CONTRAPARTE', $_REQUEST['CONTRAPARTE']);
                $INPSAG->__SET('ID_PAIS1', $_REQUEST['PAIS1']);
                $INPSAG->__SET('ID_PAIS2', $_REQUEST['PAIS2']);
                $INPSAG->__SET('ID_PAIS3', $_REQUEST['PAIS3']);
                $INPSAG->__SET('ID_PAIS4', $_REQUEST['PAIS4']);
                $INPSAG->__SET('ID_PLANTA', $_REQUEST['PLANTA']);
                $INPSAG->__SET('ID_TEMPORADA', $_REQUEST['TEMPORADA']);
                $INPSAG->__SET('ID_USUARIOI', $IDUSUARIOS);
                $INPSAG->__SET('ID_USUARIOM', $IDUSUARIOS);
                //LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                $INPSAG_ADO->agregarInpsag($INPSAG);


                // //OBTENER EL ID DE LA INPSAG CREADA PARA LUEGO ENVIAR EL INGRESO DEL DETALLE
                $ARRYAOBTENERID = $INPSAG_ADO->obtenerId(
                    $_REQUEST['FECHAINPSAG'],
                    $_REQUEST['OBSERVACIONINPSAG'],
                    $_REQUEST['PLANTA'],
                    $_REQUEST['TEMPORADA'],
                );

                $AUSUARIO_ADO->agregarAusuario2($NUMERO,1,1,"".$_SESSION["NOMBRE_USUARIO"].", Registro de Inspección SAG","fruta_inpsag",$ARRYAOBTENERID[0]['ID_INPSAG'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],$_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );

                // //REDIRECCIONAR A PAGINA registroInpsag.php
                $id_dato = $ARRYAOBTENERID[0]['ID_INPSAG'];
                $accion_dato = "crear";
                // echo "<script type='text/javascript'> location.href ='registroInpsag.php?op';</script>";
                echo '<script>
                    Swal.fire({
                        icon:"success",
                        title:"Registro Creado",
                        text:"El registro de inspección se ha creado correctamente",
                        showConfirmButton: true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm:false
                    }).then((result)=>{
                        location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
                    })
                </script>';

            }
            //OPERACION EDICION DE FILA
            if (isset($_REQUEST['GUARDAR'])) {
                // Crear archivo de log temporal para debugging
                
                //UTILIZACION METODOS SET DEL MODELO
                //SETEO DE ATRIBUTOS DE LA CLASE, OBTENIDO EN EL FORMULARIO
                $INPSAG->__SET('FECHA_INPSAG', $_REQUEST['FECHAINPSAG']);
                $INPSAG->__SET('CORRELATIVO_INPSAG', $_REQUEST['CORRELATIVOINPSAG']);
                $INPSAG->__SET('CANTIDAD_ENVASE_INPSAG', $_REQUEST['TOTALENVASE']);
                $INPSAG->__SET('KILOS_NETO_INPSAG', $_REQUEST['TOTALNETO']);
                $INPSAG->__SET('KILOS_BRUTO_INPSAG', $_REQUEST['TOTALBRUTO']);
                $INPSAG->__SET('OBSERVACION_INPSAG', $_REQUEST['OBSERVACIONINPSAG']);
                $INPSAG->__SET('CIF_INPSAG', $_REQUEST['CIFE']);
                $INPSAG->__SET('TESTADOSAG', $_REQUEST['TESTADOSAG']);
                $INPSAG->__SET('ID_TINPSAG', $_REQUEST['TINPSAGE']);
                $INPSAG->__SET('ID_TMANEJO', $_REQUEST['TMANEJOE']);
                $INPSAG->__SET('ID_INPECTOR', $_REQUEST['INPECTORE']);
                $INPSAG->__SET('ID_CONTRAPARTE', $_REQUEST['CONTRAPARTEE']);
                $INPSAG->__SET('ID_PAIS1', $_REQUEST['PAIS1E']);
                $INPSAG->__SET('ID_PAIS2', $_REQUEST['PAIS2E']);
                $INPSAG->__SET('ID_PAIS3', $_REQUEST['PAIS3E']);
                $INPSAG->__SET('ID_PAIS4', $_REQUEST['PAIS4E']);
                $INPSAG->__SET('ID_USUARIOM', $IDUSUARIOS);
                $INPSAG->__SET('ID_INPSAG', $_REQUEST['IDP']);
                //LLAMADA AL METODO DE EDITAR DEL CONTROLADOR
                // PARCHE: asegurar valor para TESTADOSAG en cabecera si no viene
                if (!isset($_REQUEST['TESTADOSAG']) || $_REQUEST['TESTADOSAG'] === '') {
                    $INPSAG->__SET('TESTADOSAG', '0');
                }
                $INPSAG_ADO->actualizarInpsag($INPSAG);

                /*echo '<script>
                   alert("'.$_REQUEST['ESTADO_INPSAG'].'");
                </script>';*/
                
                // Si viene ESTADO_INPSAG, actualizar estado general de la inspección
                if (isset($_REQUEST['ESTADO_INPSAG']) && $_REQUEST['ESTADO_INPSAG'] !== '') {
                    $INPSAG->__SET('ESTADO', $_REQUEST['ESTADO_INPSAG']);
                    if (method_exists($INPSAG_ADO, 'actualizarEstadoInpsag')) {
                        $INPSAG_ADO->actualizarEstadoInpsag($INPSAG);
                    }
                }
                
                // Actualizar estados SAG por fila si vienen como array TESTADOSAG_ROW
                
                // Intentar obtener de POST directamente si REQUEST falla
                $arrayEstadosSag = isset($_REQUEST['TESTADOSAG_ROW']) ? $_REQUEST['TESTADOSAG_ROW'] : 
                                  (isset($_POST['TESTADOSAG_ROW']) ? $_POST['TESTADOSAG_ROW'] : null);
                
                if ($arrayEstadosSag !== null && is_array($arrayEstadosSag)) {

                    $contadorActualizados = 0;
                    
                    foreach ($arrayEstadosSag as $idExi => $valorEstadoFila) {
                        $idExi = intval($idExi);
                        $valorEstadoFila = trim($valorEstadoFila);
                        
                        if ($idExi > 0 && $valorEstadoFila !== '') {
                            
                            try {
                                $EXIEXPORTACION->__SET('ID_EXIEXPORTACION', $idExi);
                                $EXIEXPORTACION->__SET('TESTADOSAG', $valorEstadoFila);
                                $resultadoActualizacion = $EXIEXPORTACION_ADO->actualizarEstadoSag($EXIEXPORTACION);
                                
                                $AUSUARIO_ADO->agregarAusuario2("NULL", 1, 2, "" . $_SESSION["NOMBRE_USUARIO"] . ", Actualización Condición SAG por folio ID: $idExi.", "fruta_exiexportacion", $idExi, $_SESSION["ID_USUARIO"], $_SESSION['ID_EMPRESA'], $_SESSION['ID_PLANTA'], $_SESSION['ID_TEMPORADA']);
                                
                                $contadorActualizados++;

                            } catch (Exception $e) {

                            }
                        } else {

                        }
                    }

                } else {

                    if ($arrayEstadosSag !== null) {

                    }
                }

                $AUSUARIO_ADO->agregarAusuario2($NUMEROVER,1,2,"".$_SESSION["NOMBRE_USUARIO"].", Modificación de inspección SAG","fruta_inpsag",$_REQUEST['IDP'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],$_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );

                if ($accion_dato == "crear") {
                    $id_dato = $_REQUEST['IDP'];
                    $accion_dato = "crear";
                    echo '<script>
                        Swal.fire({
                            icon:"info",
                            title:"Registro Modificado",
                            text:"El registro de inspección se ha modificada correctamente",
                            showConfirmButton: true,
                            confirmButtonText:"Cerrar",
                            closeOnConfirm:false
                        }).then((result)=>{
                            location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
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
                            text:"El registro de inspección se ha modificada correctamente",
                            showConfirmButton: true,
                            confirmButtonText:"Cerrar",
                            closeOnConfirm:false
                        }).then((result)=>{
                            location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
                        })
                    </script>';
                }

            }
            //OPERACION PARA CERRAR LA INPSAG
            if (isset($_REQUEST['CERRAR'])) {
                //UTILIZACION METODOS SET DEL MODELO
                //SETEO DE ATRIBUTOS DE LA CLASE, OBTENIDO EN EL FORMULARIO
                $ARRAYDINPSAG2 = $EXIEXPORTACION_ADO->verExistenciaPorInpSag($_REQUEST['ID']);
                if (empty($ARRAYDINPSAG2)) {
                    $SINO = "1";   
                    $MENSAJE = $MENSAJE."Tiene que haber al menos un registro de existencia selecionado. ";                 
              
                } else {
                    $MENSAJE = $MENSAJE;
                    $SINO = "0";
                }
                // Verificar que ninguna existencia asociada tenga estado "En Inspección" (1)
                /*$ARRAYEXISENCIAINPSAG = $EXIEXPORTACION_ADO->verExistenciaPorInpSag($_REQUEST['ID']);
                foreach ($ARRAYEXISENCIAINPSAG as $r) {
                  if ($r['TESTADOSAG'] == "1") {
                    $SINO = "1";
                    $MENSAJE = $MENSAJE."La existencia folio ".$r['FOLIO_AUXILIAR_EXIEXPORTACION']." está en estado 'En Inspección'. ";
                    break;
                  }
                }*/
                if (!isset($SINO) || $SINO != "1") { $SINO = "0"; }
                if($SINO==1){
                    echo '<script>
                        Swal.fire({
                            icon:"warning",
                            title:"Accion restringida",
                            text:"'.$MENSAJE.'",
                            showConfirmButton: true,
                            confirmButtonText:"Cerrar",
                            closeOnConfirm:false
                        })
                    </script>';
                }
                if ($SINO == "0") {
                        $INPSAG->__SET('FECHA_INPSAG', $_REQUEST['FECHAINPSAG']);
                        $INPSAG->__SET('CORRELATIVO_INPSAG', $_REQUEST['CORRELATIVOINPSAG']);
                        $INPSAG->__SET('CANTIDAD_ENVASE_INPSAG', $_REQUEST['TOTALENVASE']);
                        $INPSAG->__SET('KILOS_NETO_INPSAG', $_REQUEST['TOTALNETO']);
                        $INPSAG->__SET('KILOS_BRUTO_INPSAG', $_REQUEST['TOTALBRUTO']);
                        $INPSAG->__SET('OBSERVACION_INPSAG', $_REQUEST['OBSERVACIONINPSAG']);
                        $INPSAG->__SET('CIF_INPSAG', $_REQUEST['CIFE']);
                        $INPSAG->__SET('TESTADOSAG', $_REQUEST['TESTADOSAG']);
                        $INPSAG->__SET('ID_TINPSAG', $_REQUEST['TINPSAGE']);
                        $INPSAG->__SET('ID_TMANEJO', $_REQUEST['TMANEJOE']);
                        $INPSAG->__SET('ID_INPECTOR', $_REQUEST['INPECTORE']);
                        $INPSAG->__SET('ID_CONTRAPARTE', $_REQUEST['CONTRAPARTEE']);
                        $INPSAG->__SET('ID_PAIS1', $_REQUEST['PAIS1E']);
                        $INPSAG->__SET('ID_PAIS2', $_REQUEST['PAIS2E']);
                        $INPSAG->__SET('ID_PAIS3', $_REQUEST['PAIS3E']);
                        $INPSAG->__SET('ID_PAIS4', $_REQUEST['PAIS4E']);
                        $INPSAG->__SET('ID_USUARIOM', $IDUSUARIOS);
                        $INPSAG->__SET('ID_INPSAG', $_REQUEST['IDP']);
                        //LLAMADA AL METODO DE EDITAR DEL CONTROLADOR
                        if (!isset($_REQUEST['TESTADOSAG']) || $_REQUEST['TESTADOSAG'] === '') {
                            $INPSAG->__SET('TESTADOSAG', '0');
                        }

                        $INPSAG_ADO->actualizarInpsag($INPSAG);


                        // Si viene ESTADO_INPSAG, actualizar estado general de la inspección
                        if (isset($_REQUEST['ESTADO_INPSAG']) && $_REQUEST['ESTADO_INPSAG'] !== '') {
                            $INPSAG->__SET('ESTADO', $_REQUEST['ESTADO_INPSAG']);
                            if (method_exists($INPSAG_ADO, 'actualizarEstadoInpsag')) {
                                $INPSAG_ADO->actualizarEstadoInpsag($INPSAG);
                            }
                        }
                

                         // Actualizar estados SAG por fila si vienen como array TESTADOSAG_ROW
                
                        // Intentar obtener de POST directamente si REQUEST falla
                        $arrayEstadosSag = isset($_REQUEST['TESTADOSAG_ROW']) ? $_REQUEST['TESTADOSAG_ROW'] : 
                                        (isset($_POST['TESTADOSAG_ROW']) ? $_POST['TESTADOSAG_ROW'] : null);
                        
                        if ($arrayEstadosSag !== null && is_array($arrayEstadosSag)) {

                            $contadorActualizados = 0;
                            
                            foreach ($arrayEstadosSag as $idExi => $valorEstadoFila) {
                                $idExi = intval($idExi);
                                $valorEstadoFila = trim($valorEstadoFila);
                                
                                if ($idExi > 0 && $valorEstadoFila !== '') {
                                    
                                    try {
                                        $EXIEXPORTACION->__SET('ID_EXIEXPORTACION', $idExi);
                                        $EXIEXPORTACION->__SET('TESTADOSAG', $valorEstadoFila);
                                        $resultadoActualizacion = $EXIEXPORTACION_ADO->actualizarEstadoSag($EXIEXPORTACION);
                                        
                                        $AUSUARIO_ADO->agregarAusuario2("NULL", 1, 2, "" . $_SESSION["NOMBRE_USUARIO"] . ", Actualización Condición SAG por folio ID: $idExi.", "fruta_exiexportacion", $idExi, $_SESSION["ID_USUARIO"], $_SESSION['ID_EMPRESA'], $_SESSION['ID_PLANTA'], $_SESSION['ID_TEMPORADA']);
                                        
                                        $contadorActualizados++;

                                    } catch (Exception $e) {

                                    }
                                } else {

                                }
                            }

                        } else {

                            if ($arrayEstadosSag !== null) {

                            }
                        }


                        $INPSAG->__SET('ID_INPSAG', $_REQUEST['IDP']);
                        //LLAMADA AL METODO DE EDITAR DEL CONTROLADOR
                        $INPSAG_ADO->cerrado($INPSAG);

                       

                        $AUSUARIO_ADO->agregarAusuario2($NUMEROVER,1,3,"".$_SESSION["NOMBRE_USUARIO"].", Cerrar Inspeccion SAG","fruta_inpsag",$_REQUEST['IDP'],$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'],$_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );

                        $ARRAYEXISENCIAINPSAG = $EXIEXPORTACION_ADO->verExistenciaPorInpSag($_REQUEST['IDP']);
                        foreach ($ARRAYEXISENCIAINPSAG as $r) :
                            // Mantener estado individual por fila (no sobrescribir con global)
                            // $EXIEXPORTACION->__SET('TESTADOSAG', $_REQUEST['TESTADOSAG']);
                            $EXIEXPORTACION->__SET('ID_EXIEXPORTACION', $r['ID_EXIEXPORTACION']);
                            //LLAMADA AL METODO DE EDITAR DEL CONTROLADOR
                            $EXIEXPORTACION_ADO->actualizarEstadoSag($EXIEXPORTACION);
                        endforeach;


                        //REDIRECCIONAR A PAGINA registroInpsag.php
                        //SEGUNE EL TIPO DE OPERACIONS QUE SE INDENTIFIQUE EN LA URL
                        if ($accion_dato == "crear") {
                            $id_dato = $_REQUEST['IDP'];
                            $accion_dato = "ver";
                            echo '<script>
                                Swal.fire({
                                    icon:"info",
                                    title:"Registro Cerrado",
                                    text:"Este Inspeccion se encuentra cerrada y no puede ser modificada.",
                                    showConfirmButton: true,
                                    confirmButtonText:"Cerrar",
                                    closeOnConfirm:false
                                }).then((result)=>{
                                    location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
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
                                    text:"Este Inspeccion se encuentra cerrada y no puede ser modificada.",
                                    showConfirmButton: true,
                                    confirmButtonText:"Cerrar",
                                    closeOnConfirm:false
                                }).then((result)=>{
                                    location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
                                })
                            </script>';
                        }                        
                }
                
            }

            if (isset($_REQUEST['QUITAR'])) {
                // El valor del ID viene en el value del botón QUITAR
                $IDQUITAR = $_REQUEST['IDQUITAR'];
                $EXIEXPORTACION->__SET('ID_EXIEXPORTACION', $IDQUITAR);
                // LLAMADA AL METODO DE REGISTRO DEL CONTROLADOR
                $EXIEXPORTACION_ADO->actualizarDeselecionarSagCambiarEstado($EXIEXPORTACION);

                $AUSUARIO_ADO->agregarAusuario2("NULL",1,2,"".$_SESSION["NOMBRE_USUARIO"].", Se Quito la Existencia de la Inspeccion SAG.","fruta_exiexportacion", "NULL" ,$_SESSION["ID_USUARIO"],$_SESSION['ID_EMPRESA'], $_SESSION['ID_PLANTA'],$_SESSION['ID_TEMPORADA'] );  
                
                echo '<script>
                    Swal.fire({
                        icon:"error",
                        title:"Accion realizada",
                        text:"Se ha quitado la existencia de la Inspeccion.",
                        showConfirmButton: true,
                        confirmButtonText:"Cerrar",
                        closeOnConfirm:false
                    }).then((result)=>{
                        location.href = "registroInpsag.php?op&id='.$id_dato.'&a='.$accion_dato.'";                            
                    })
                </script>';
            }

            // OPERACIÓN PARA ACTUALIZAR LOTE VIA AJAX
            if (isset($_REQUEST['ACTUALIZAR_LOTE'])) {
                $ID_EXIEXPORTACION = $_REQUEST['ID_EXIEXPORTACION'];
                $LOTE = $_REQUEST['LOTE'];
                
                // Setear los valores en el modelo
                $EXIEXPORTACION->__SET('ID_EXIEXPORTACION', $ID_EXIEXPORTACION);
                $EXIEXPORTACION->__SET('LOTE', $LOTE);
                
                // Llamar al método para actualizar el lote
                $resultado = $EXIEXPORTACION_ADO->actualizarLote($EXIEXPORTACION);
                
                // Registrar en auditoría
                $AUSUARIO_ADO->agregarAusuario2("NULL", 1, 2, "" . $_SESSION["NOMBRE_USUARIO"] . ", Actualización de Lote en Inspección SAG.", "fruta_exiexportacion", $ID_EXIEXPORTACION, $_SESSION["ID_USUARIO"], $_SESSION['ID_EMPRESA'], $_SESSION['ID_PLANTA'], $_SESSION['ID_TEMPORADA']);
                
                // Respuesta para AJAX
                echo json_encode(['success' => $resultado]);
                exit;
            }
        ?>
        
        <script>
        // Sincronizar selectores de estado SAG con inputs hidden antes de enviar
        var formElement = document.getElementById('form_reg_dato');
        if (formElement) {
            console.log('✓ Formulario encontrado:', formElement.id);
            
            formElement.addEventListener('submit', function(e) {
                console.log('=== INICIO ENVÍO DE FORMULARIO ===');
                
          
                
                // ESTRATEGIA: Eliminar inputs hidden antiguos y crear nuevos justo antes del submit
                // Esto asegura que se envíen correctamente
                
                // 1. Eliminar cualquier input TESTADOSAG_ROW existente
                var oldInputs = document.querySelectorAll('input[name^="TESTADOSAG_ROW"]');
                console.log('Eliminando inputs antiguos:', oldInputs.length);
                oldInputs.forEach(function(input) {
                    input.remove();
                });
                
                // 2. Buscar TODOS los selects de estado SAG
                var selectsEstadoSag = document.querySelectorAll('.estado-sag-select');
                console.log('Total selects estado SAG encontrados:', selectsEstadoSag.length);
                
                if (selectsEstadoSag.length === 0) {
                    console.warn('⚠️ NO SE ENCONTRARON SELECTS con clase estado-sag-select');
                }
                
                var datosEnviar = [];
                
                // 3. Por cada select, crear un input hidden NUEVO dentro del formulario
                selectsEstadoSag.forEach(function(select, index) {
                    // Obtener ID de existencia del ID del select
                    // El ID del select es: TESTADOSAG_25514 por ejemplo
                    var idExistencia = select.id.replace('TESTADOSAG_', '');
                    
                    // Obtener valor del select2
                    var valor = '';
                    if (typeof jQuery !== 'undefined') {
                        try {
                            valor = jQuery(select).val();
                        } catch(e) {
                            valor = select.value;
                        }
                    } else {
                        valor = select.value;
                    }
                    
                    console.log(`Select ${index + 1}:`, {
                        id: select.id,
                        idExistencia: idExistencia,
                        valor: valor
                    });
                    
                    // Solo crear input si hay valor
                    if (valor && valor !== '') {
                        // Crear nuevo input hidden
                        var newInput = document.createElement('input');
                        newInput.type = 'hidden';
                        newInput.name = 'TESTADOSAG_ROW[' + idExistencia + ']';
                        newInput.value = valor;
                        newInput.id = 'TESTADOSAG_HIDDEN_' + idExistencia;
                        
                        // Agregar al formulario
                        formElement.appendChild(newInput);
                        
                        console.log(`  → Input creado: name="${newInput.name}", value="${newInput.value}"`);
                        
                        datosEnviar.push({
                            name: newInput.name,
                            value: newInput.value
                        });
                    } else {
                        console.log(`  → Sin valor, no se crea input`);
                    }
                });
                
                console.log('Total inputs creados:', datosEnviar.length);
                console.log('Datos que se enviarán:', datosEnviar);
                console.log('=== FIN PREPARACIÓN FORMULARIO ===');
                
                // El formulario se enviará normalmente con los nuevos inputs
            });
        } else {
            console.error('❌ NO SE ENCONTRÓ EL FORMULARIO con ID: form_reg_dato');
        }
        
       
        </script>
</body>

</html>