<?php

//LLAMADA DE LOS ARCHIVOS NECESAROP PARA LA OPERACION DEL CONTROLADOR

//LLAMADA AL CONFIGURACION DE LA BASE DE DATOS
include_once '../../assest/config/BDCONFIG.php';

//INICIALIZAR VARIABLES
$HOST = "";
$DBNAME = "";
$USER = "";
$PASS = "";

//ESTRUCTURA DEL CONTROLADOR
class CONSULTA_ADO
{
    //ATRIBUTO
    private $conexion;

    //LLAMADO A LA BD Y CONFIGURAR PARAMETROS

    public function __CONSTRUCT()
    {
        try {
            $BDCONFIG = new BDCONFIG();
            $HOST = $BDCONFIG->__GET('HOST');
            $DBNAME = $BDCONFIG->__GET('DBNAME');
            $USER = $BDCONFIG->__GET('USER');
            $PASS = $BDCONFIG->__GET('PASS');


            $this->conexion = new PDO('mysql:host=' . $HOST . ';dbname=' . $DBNAME, $USER, $PASS);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    //CONSULTAS DASHBOARD FRUTA 

    

    public function TotalKgPtDespachadoExportacion($TEMPORADA, $PLANTA)
    {
        try {
        
            $datos = $this->conexion->prepare("SELECT E.NOMBRE_EMPRESA, FORMAT(IFNULL(KILOS_NETO_EXIEXPORTACION,0),2,'de_DE') AS NETO FROM FRUTA_DESPACHOEX DEX 
            JOIN FRUTA_EXIEXPORTACION DDEX ON DDEX.ID_DESPACHOEX = DEX.ID_DESPACHOEX 
            JOIN principal_empresa E ON E.ID_EMPRESA = DEX.ID_EMPRESA 
            WHERE DEX.ESTADO_REGISTRO = 1 
            AND DDEX.ESTADO_REGISTRO = 1 
            AND DEX.ESTADO = 0 
            AND DEX.ID_TEMPORADA = '".$TEMPORADA."' 
            AND DEX.ID_PLANTA = '".$PLANTA."'
            GROUP BY DEX.ID_EMPRESA");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;
        
                    //	print_r($resultado);
                    //	var_dump($resultado);
        
        
            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgMpRecepcionadosEmpresaPlanta($TEMPORADA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT E.NOMBRE_EMPRESA, SUM(DR.KILOS_NETO_DRECEPCION)AS TOTAL FROM fruta_recepcionmp R 
            JOIN fruta_drecepcionmp DR ON DR.ID_RECEPCION = R.ID_RECEPCION 
            JOIN principal_empresa E ON E.ID_EMPRESA = R.ID_EMPRESA 
            WHERE R.ESTADO_REGISTRO = 1 
            AND DR.ESTADO_REGISTRO = 1 
            AND R.ESTADO = 0 
            AND R.ID_TEMPORADA = '".$TEMPORADA."' 
            AND R.ID_PLANTA = '".$PLANTA."'
            GROUP BY R.ID_EMPRESA");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalKgMpRecepcionadosPlanta($TEMPORADA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT SUM(DR.KILOS_NETO_DRECEPCION)AS TOTAL FROM fruta_recepcionmp R 
            JOIN fruta_drecepcionmp DR ON DR.ID_RECEPCION = R.ID_RECEPCION 
            JOIN principal_empresa E ON E.ID_EMPRESA = R.ID_EMPRESA 
            WHERE R.ESTADO_REGISTRO = 1 
            AND DR.ESTADO_REGISTRO = 1 
            AND R.ESTADO = 0 
            AND R.ID_TEMPORADA = '".$TEMPORADA."' 
            AND R.ID_PLANTA = '".$PLANTA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalRecepcionMpAbiertas($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT COUNT(ID_RECEPCION)AS NUMERO FROM fruta_recepcionmp R WHERE R.ID_PLANTA = '".$PLANTA."' AND R.ID_EMPRESA = '".$EMPRESA."' AND R.ESTADO = 1 AND R.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalRecepcionIndAbiertas($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT COUNT(ID_RECEPCION)AS NUMERO FROM fruta_recepcionind R WHERE R.ID_PLANTA = '".$PLANTA."' AND R.ID_EMPRESA = '".$EMPRESA."' AND R.ESTADO = 1 AND R.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalDespachoMpAbiertas($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT COUNT(ID_DESPACHO)AS NUMERO FROM fruta_despachomp D WHERE D.ID_PLANTA = '".$PLANTA."' AND D.ID_EMPRESA = '".$EMPRESA."' AND D.ESTADO = 1 AND D.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalDespachoIndAbiertas($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT COUNT(ID_DESPACHO)AS NUMERO FROM fruta_despachoind D WHERE D.ID_PLANTA = '".$PLANTA."' AND D.ID_EMPRESA = '".$EMPRESA."' AND D.ESTADO = 1 AND D.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalProcesosAbiertos($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT COUNT(ID_PROCESO)AS NUMERO FROM fruta_proceso P WHERE P.ID_PLANTA = '".$PLANTA."' AND P.ID_EMPRESA = '".$EMPRESA."' AND P.ESTADO = 1 AND P.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalReembalajesAbiertos($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT COUNT(ID_REEMBALAJE)AS NUMERO FROM fruta_reembalaje REE WHERE REE.ID_PLANTA = '".$PLANTA."' AND REE.ID_EMPRESA = '".$EMPRESA."' AND REE.ESTADO = 1 AND REE.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalRepaletizajesAbiertos($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT COUNT(ID_REPALETIZAJE)AS NUMERO FROM fruta_repaletizajeex REPA WHERE REPA.ID_PLANTA = '".$PLANTA."' AND REPA.ID_EMPRESA = '".$EMPRESA."' AND REPA.ESTADO = 1 AND REPA.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalKgMpRecepcionadoAcumulado($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT SUM(DR.KILOS_NETO_DRECEPCION)AS TOTAL FROM fruta_recepcionmp R 
            JOIN fruta_drecepcionmp DR ON DR.ID_RECEPCION = R.ID_RECEPCION 
            WHERE R.ID_PLANTA = '".$PLANTA."' AND R.ID_EMPRESA = '".$EMPRESA."' AND R.ESTADO = 0 AND R.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgMpRecepcionadoDiaAnterior($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT SUM(DR.KILOS_NETO_DRECEPCION)AS TOTAL FROM fruta_recepcionmp R 
            JOIN fruta_drecepcionmp DR ON DR.ID_RECEPCION = R.ID_RECEPCION 
            WHERE R.ID_PLANTA = '".$PLANTA."' AND R.ID_EMPRESA = '".$EMPRESA."' AND R.ESTADO = 1 AND R.ID_TEMPORADA = '".$TEMPORADA."' AND DATE_FORMAT(R.FECHA_RECEPCION, '%Y-%m-%d') = DATE_FORMAT(date_sub(now(),interval 1 day), '%Y-%m-%d')");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalKgMpProcesado($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT SUM(P.KILOS_NETO_ENTRADA)AS TOTAL FROM fruta_proceso P 
            WHERE P.ID_PLANTA = '".$PLANTA."' AND P.ID_EMPRESA = '".$EMPRESA."' AND P.ESTADO = 0 AND P.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function TotalKgMpProcesadoDiaAnterior($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT SUM(P.KILOS_NETO_ENTRADA)AS TOTAL FROM fruta_proceso P 
            WHERE P.ID_PLANTA = '".$PLANTA."' AND P.ID_EMPRESA = '".$EMPRESA."' AND P.ESTADO = 0 AND P.ID_TEMPORADA = '".$TEMPORADA."' AND DATE_FORMAT(P.FECHA_PROCESO, '%Y-%m-%d') = DATE_FORMAT(date_sub(now(),interval 1 day), '%Y-%m-%d')");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgMpRecepcionadoHastaCincoAm($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT SUM(EXI.KILOS_NETO_EXIMATERIAPRIMA)AS TOTAL FROM fruta_eximateriaprima EXI
            WHERE EXI.ID_PLANTA = '".$PLANTA."' AND EXI.ID_EMPRESA = '".$EMPRESA."' AND EXI.ESTADO = 2 AND EXI.ESTADO_REGISTRO = 1 AND EXI.ID_TEMPORADA = '".$TEMPORADA."'
            AND EXI.FECHA_RECEPCION <= CONCAT(CURDATE(),' 05:00:00')");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgMpRecepcionadoDesdeCincoAm($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(DR.KILOS_NETO_DRECEPCION),0) AS TOTAL FROM fruta_recepcionmp R
                                                JOIN fruta_drecepcionmp DR ON DR.ID_RECEPCION = R.ID_RECEPCION
                                                WHERE R.ID_PLANTA = '".$PLANTA."'
                                                AND R.ID_EMPRESA = '".$EMPRESA."'
                                                AND R.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND R.ESTADO = 0
                                                AND R.ESTADO_REGISTRO = 1
                                                AND DR.ESTADO_REGISTRO = 1
                                                AND R.FECHA_RECEPCION >= CONCAT(CURDATE(),' 05:00:00')");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgMpRecepcionadoDiaActual($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(DR.KILOS_NETO_DRECEPCION),0) AS TOTAL FROM fruta_recepcionmp R
                                                JOIN fruta_drecepcionmp DR ON DR.ID_RECEPCION = R.ID_RECEPCION
                                                WHERE R.ID_PLANTA = '".$PLANTA."'
                                                AND R.ID_EMPRESA = '".$EMPRESA."'
                                                AND R.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND R.ESTADO = 0
                                                AND R.ESTADO_REGISTRO = 1
                                                AND DR.ESTADO_REGISTRO = 1
                                                AND DATE(R.FECHA_RECEPCION) = CURDATE()");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalExistenciaMateriaPrimaActual($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT SUM(EXI.KILOS_NETO_EXIMATERIAPRIMA) AS TOTAL FROM fruta_eximateriaprima EXI
            WHERE EXI.ID_PLANTA = '".$PLANTA."' AND EXI.ID_EMPRESA = '".$EMPRESA."' AND EXI.ESTADO = 2 AND EXI.ESTADO_REGISTRO = 1 AND EXI.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TopExportacionPorPais($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(PA.NOMBRE_PAIS,'Sin país') AS NOMBRE,
                                                    IFNULL(SUM(EXI.KILOS_NETO_EXIEXPORTACION),0) AS TOTAL
                                                FROM fruta_exiexportacion EXI
                                                JOIN fruta_despachoex DEX ON DEX.ID_DESPACHOEX = EXI.ID_DESPACHOEX
                                                LEFT JOIN ubicacion_pais PA ON DEX.ID_PAIS = PA.ID_PAIS
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND DEX.ESTADO_REGISTRO = 1
                                                AND DEX.ESTADO = 0
                                                AND EXI.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND EXI.ID_EMPRESA = '".$EMPRESA."'
                                                AND EXI.ID_PLANTA = '".$PLANTA."'
                                                GROUP BY DEX.ID_PAIS
                                                ORDER BY TOTAL DESC
                                                LIMIT 5");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TopExportacionPorRecibidor($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(RF.NOMBRE_RFINAL,'Sin recibidor') AS NOMBRE,
                                                    IFNULL(SUM(EXI.KILOS_NETO_EXIEXPORTACION),0) AS TOTAL
                                                FROM fruta_exiexportacion EXI
                                                JOIN fruta_despachoex DEX ON DEX.ID_DESPACHOEX = EXI.ID_DESPACHOEX
                                                LEFT JOIN fruta_rfinal RF ON DEX.ID_RFINAL = RF.ID_RFINAL
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND DEX.ESTADO_REGISTRO = 1
                                                AND DEX.ESTADO = 0
                                                AND EXI.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND EXI.ID_EMPRESA = '".$EMPRESA."'
                                                AND EXI.ID_PLANTA = '".$PLANTA."'
                                                GROUP BY DEX.ID_RFINAL
                                                ORDER BY TOTAL DESC
                                                LIMIT 5");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function CajasAprobadasPorPais($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(PA.NOMBRE_PAIS,'Sin país') AS NOMBRE,
                                                    IFNULL(SUM(INP.CANTIDAD_ENVASE_INPSAG),0) AS TOTAL
                                                FROM fruta_inpsag INP
                                                LEFT JOIN ubicacion_pais PA ON INP.ID_PAIS1 = PA.ID_PAIS
                                                WHERE INP.ESTADO_REGISTRO = 1
                                                AND INP.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND INP.ID_PLANTA = '".$PLANTA."'
                                                GROUP BY INP.ID_PAIS1
                                                ORDER BY TOTAL DESC
                                                LIMIT 5");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function UltimosProcesosBajaExportacionCerrados($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT
                                                    P.NUMERO_PROCESO,
                                                    P.FECHA_PROCESO,
                                                    P.KILOS_NETO_ENTRADA,
                                                    P.KILOS_EXPORTACION_PROCESO,
                                                    P.PDEXPORTACION_PROCESO,
                                                    P.PDEXPORTACIONCD_PROCESO
                                                FROM fruta_proceso P
                                                WHERE P.ID_PLANTA = '".$PLANTA."'
                                                AND P.ID_EMPRESA = '".$EMPRESA."'
                                                AND P.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND P.ESTADO = 0
                                                AND P.ESTADO_REGISTRO = 1
                                                ORDER BY P.PDEXPORTACION_PROCESO ASC, P.FECHA_PROCESO DESC
                                                LIMIT 5");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgProcesoEntradaSalida($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT
                                                    IFNULL(SUM(P.KILOS_NETO_ENTRADA),0) AS ENTRADA,
                                                    IFNULL(SUM(P.KILOS_NETO_PROCESO),0) AS SALIDA
                                                FROM fruta_proceso P
                                                WHERE P.ID_PLANTA = '".$PLANTA."'
                                                AND P.ID_EMPRESA = '".$EMPRESA."'
                                                AND P.ESTADO = 0
                                                AND P.ID_TEMPORADA = '".$TEMPORADA."'");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgProcesoDesdeCincoAm($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(P.KILOS_NETO_ENTRADA),0) AS TOTAL FROM fruta_proceso P
                                                WHERE P.ID_PLANTA = '".$PLANTA."'
                                                AND P.ID_EMPRESA = '".$EMPRESA."'
                                                AND P.ESTADO = 0
                                                AND P.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND P.FECHA_PROCESO >= CONCAT(CURDATE(),' 05:00:00')");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgProcesoDiaActual($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(P.KILOS_NETO_ENTRADA),0) AS TOTAL FROM fruta_proceso P
                                                WHERE P.ID_PLANTA = '".$PLANTA."'
                                                AND P.ID_EMPRESA = '".$EMPRESA."'
                                                AND P.ESTADO = 0
                                                AND P.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND DATE(P.FECHA_PROCESO) = CURDATE()");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgDespachoMpDesdeCincoAm($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(D.KILOS_NETO_DESPACHO),0) AS TOTAL FROM fruta_despachomp D
                                                WHERE D.ID_PLANTA = '".$PLANTA."'
                                                AND D.ID_EMPRESA = '".$EMPRESA."'
                                                AND D.ESTADO = 0
                                                AND D.ESTADO_REGISTRO = 1
                                                AND D.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND D.FECHA_DESPACHO >= CONCAT(CURDATE(),' 05:00:00')");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TotalKgDespachoMpDiaActual($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(D.KILOS_NETO_DESPACHO),0) AS TOTAL FROM fruta_despachomp D
                                                WHERE D.ID_PLANTA = '".$PLANTA."'
                                                AND D.ID_EMPRESA = '".$EMPRESA."'
                                                AND D.ESTADO = 0
                                                AND D.ESTADO_REGISTRO = 1
                                                AND D.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND DATE(D.FECHA_DESPACHO) = CURDATE()");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TopExportacionPorProductor($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT PR.NOMBRE_PRODUCTOR AS NOMBRE,
                                                    IFNULL(SUM(EXI.KILOS_NETO_EXIEXPORTACION),0) AS TOTAL
                                                FROM fruta_exiexportacion EXI
                                                LEFT JOIN fruta_productor PR ON EXI.ID_PRODUCTOR = PR.ID_PRODUCTOR
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND EXI.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND EXI.ID_EMPRESA = '".$EMPRESA."'
                                                AND EXI.ID_PLANTA = '".$PLANTA."'
                                                GROUP BY EXI.ID_PRODUCTOR
                                                ORDER BY TOTAL DESC
                                                LIMIT 5");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function TopExportacionPorVariedad($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT V.NOMBRE_VESPECIES AS NOMBRE,
                                                    IFNULL(SUM(EXI.KILOS_NETO_EXIEXPORTACION),0) AS TOTAL
                                                FROM fruta_exiexportacion EXI
                                                LEFT JOIN fruta_vespecies V ON EXI.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND EXI.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND EXI.ID_EMPRESA = '".$EMPRESA."'
                                                AND EXI.ID_PLANTA = '".$PLANTA."'
                                                GROUP BY EXI.ID_VESPECIES
                                                ORDER BY TOTAL DESC
                                                LIMIT 5");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosPorVariedadProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {

            if (empty($PRODUCTORES)) {
                return [];
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT V.NOMBRE_VESPECIES AS NOMBRE,
                                                    IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0) AS TOTAL
                                                FROM fruta_recepcionmp recepcion
                                                LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
                                                LEFT JOIN fruta_vespecies V ON detalle.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE recepcion.ESTADO_REGISTRO = 1
                                                AND recepcion.ESTADO = 0
                                                AND detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND recepcion.ID_PRODUCTOR IN ('".$productoresIn."')
                                                GROUP BY detalle.ID_VESPECIES
                                                ORDER BY TOTAL DESC;");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosPorSemanaProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {

            if (empty($PRODUCTORES)) {
                return [];
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT WEEK(recepcion.FECHA_RECEPCION,3) AS SEMANA,
                                                    IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0) AS TOTAL
                                                FROM fruta_recepcionmp recepcion
                                                LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
                                                LEFT JOIN fruta_vespecies V ON detalle.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE recepcion.ESTADO_REGISTRO = 1
                                                AND recepcion.ESTADO = 0
                                                AND detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND recepcion.ID_PRODUCTOR IN ('".$productoresIn."')
                                                GROUP BY SEMANA
                                                ORDER BY SEMANA;");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosPorProductorAsociado($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {

            if (empty($PRODUCTORES)) {
                return [];
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT PR.NOMBRE_PRODUCTOR AS NOMBRE,
                                                        PR.CSG_PRODUCTOR AS CSP,
                                                        IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0) AS TOTAL,
                                                        COUNT(DISTINCT recepcion.ID_RECEPCION) AS RECEPCIONES
                                                FROM fruta_recepcionmp recepcion
                                                LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
                                                LEFT JOIN fruta_productor PR ON recepcion.ID_PRODUCTOR = PR.ID_PRODUCTOR
                                                LEFT JOIN fruta_vespecies V ON detalle.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE recepcion.ESTADO_REGISTRO = 1
                                                AND recepcion.ESTADO = 0
                                                AND detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND recepcion.ID_PRODUCTOR IN ('".$productoresIn."')
                                                GROUP BY recepcion.ID_PRODUCTOR
                                                ORDER BY TOTAL DESC;");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function existenciaMateriaPrimaPorVariedadProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {
            if (empty($PRODUCTORES)) {
                return [];
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT V.NOMBRE_VESPECIES AS NOMBRE,
                                                    IFNULL(SUM(EXI.KILOS_NETO_EXIMATERIAPRIMA),0) AS TOTAL
                                                FROM fruta_eximateriaprima EXI
                                                LEFT JOIN fruta_vespecies V ON EXI.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND EXI.ESTADO = 2
                                                AND EXI.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND EXI.ID_PRODUCTOR IN ('".$productoresIn."')
                                                GROUP BY EXI.ID_VESPECIES
                                                ORDER BY TOTAL DESC;");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function topExportacionProductor($TEMPORADA, $ESPECIE, $PRODUCTORES, $LIMITE = 5)
    {
        try {
            if (empty($PRODUCTORES)) {
                return [];
            }

            $productoresIn = implode("','", $PRODUCTORES);
            $limiteSeguro = (int) $LIMITE;

            $datos = $this->conexion->prepare("SELECT PR.NOMBRE_PRODUCTOR AS NOMBRE,
                                                        PR.CSG_PRODUCTOR AS CSP,
                                                        IFNULL(SUM(EXPEXPORT.KILOS_NETO_EXIEXPORTACION),0) AS TOTAL
                                                FROM fruta_exiexportacion EXPEXPORT
                                                LEFT JOIN fruta_productor PR ON EXPEXPORT.ID_PRODUCTOR = PR.ID_PRODUCTOR
                                                LEFT JOIN fruta_vespecies V ON EXPEXPORT.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXPEXPORT.ESTADO_REGISTRO = 1
                                                AND EXPEXPORT.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND EXPEXPORT.ID_PRODUCTOR IN ('".$productoresIn."')
                                                GROUP BY EXPEXPORT.ID_PRODUCTOR
                                                ORDER BY TOTAL DESC
                                                LIMIT $limiteSeguro;");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function resumenRecepcionesProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {

            if (empty($PRODUCTORES)) {
                return [];
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0) AS KILOS,
                                                        COUNT(DISTINCT recepcion.ID_RECEPCION) AS RECEPCIONES,
                                                        COUNT(DISTINCT recepcion.ID_PRODUCTOR) AS PRODUCTORES
                                                FROM fruta_recepcionmp recepcion
                                                LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
                                                LEFT JOIN fruta_vespecies V ON detalle.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE recepcion.ESTADO_REGISTRO = 1
                                                AND recepcion.ESTADO = 0
                                                AND detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND recepcion.ID_PRODUCTOR IN ('".$productoresIn."');");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosMateriaPrimaProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {

            if (empty($PRODUCTORES)) {
                return 0;
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0) AS TOTAL
                                                FROM fruta_recepcionmp recepcion
                                                LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
                                                LEFT JOIN fruta_vespecies V ON detalle.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE recepcion.ESTADO_REGISTRO = 1
                                                AND detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND recepcion.FECHA_RECEPCION < CURRENT_DATE
                                                AND recepcion.ID_PRODUCTOR IN ('".$productoresIn."');");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado ? $resultado[0]['TOTAL'] : 0;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosProcesadosProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {
            if (empty($PRODUCTORES)) {
                return 0;
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(EXI.KILOS_NETO_EXIMATERIAPRIMA),0) AS TOTAL
                                                FROM fruta_eximateriaprima EXI
                                                INNER JOIN fruta_proceso P ON EXI.ID_PROCESO = P.ID_PROCESO
                                                LEFT JOIN fruta_vespecies V ON EXI.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND P.ESTADO_REGISTRO = 1
                                                AND P.ESTADO = 0
                                                AND P.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND EXI.ID_PRODUCTOR IN ('".$productoresIn."');");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado ? $resultado[0]['TOTAL'] : 0;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosProcesadosHoyProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {
            if (empty($PRODUCTORES)) {
                return 0;
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(EXI.KILOS_NETO_EXIMATERIAPRIMA),0) AS TOTAL
                                                FROM fruta_eximateriaprima EXI
                                                INNER JOIN fruta_proceso P ON EXI.ID_PROCESO = P.ID_PROCESO
                                                LEFT JOIN fruta_vespecies V ON EXI.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND P.ESTADO_REGISTRO = 1
                                                AND P.ESTADO = 0
                                                AND P.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND DATE(P.FECHA_PROCESO) = CURDATE()
                                                AND EXI.ID_PRODUCTOR IN ('".$productoresIn."');");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado ? $resultado[0]['TOTAL'] : 0;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosExportadosProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {
            if (empty($PRODUCTORES)) {
                return 0;
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(EXPEXPORT.KILOS_NETO_EXIEXPORTACION),0) AS TOTAL
                                                FROM fruta_exiexportacion EXPEXPORT
                                                LEFT JOIN fruta_vespecies V ON EXPEXPORT.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXPEXPORT.ESTADO_REGISTRO = 1
                                                AND EXPEXPORT.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND EXPEXPORT.ID_PRODUCTOR IN ('".$productoresIn."');");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado ? $resultado[0]['TOTAL'] : 0;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosRecepcionadosHoyProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {
            if (empty($PRODUCTORES)) {
                return 0;
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0) AS TOTAL
                                                FROM fruta_recepcionmp recepcion
                                                LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
                                                LEFT JOIN fruta_vespecies V ON detalle.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE recepcion.ESTADO_REGISTRO = 1
                                                AND recepcion.ESTADO = 0
                                                AND detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND DATE(recepcion.FECHA_RECEPCION) = CURDATE()
                                                AND recepcion.ID_PRODUCTOR IN ('".$productoresIn."');");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado ? $resultado[0]['TOTAL'] : 0;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function kilosPorCspYVariedadProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {
            if (empty($PRODUCTORES)) {
                return [];
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT PR.NOMBRE_PRODUCTOR AS PRODUCTOR,
                                                        PR.CSG_PRODUCTOR AS CSP,
                                                        V.NOMBRE_VESPECIES AS VARIEDAD,
                                                        IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0) AS TOTAL
                                                FROM fruta_recepcionmp recepcion
                                                LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
                                                LEFT JOIN fruta_productor PR ON recepcion.ID_PRODUCTOR = PR.ID_PRODUCTOR
                                                LEFT JOIN fruta_vespecies V ON detalle.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE recepcion.ESTADO_REGISTRO = 1
                                                AND recepcion.ESTADO = 0
                                                AND detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND recepcion.ID_PRODUCTOR IN ('".$productoresIn."')
                                                GROUP BY recepcion.ID_PRODUCTOR, detalle.ID_VESPECIES
                                                ORDER BY PRODUCTOR ASC, VARIEDAD ASC;");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function porcentajeExportacionProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {
            if (empty($PRODUCTORES)) {
                return 0;
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT 
                                                    IFNULL(SUM(EXPEXPORT.KILOS_NETO_EXIEXPORTACION),0) AS KILOS_EXPORTADOS,
                                                    (SELECT IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)
                                                     FROM fruta_recepcionmp recepcion
                                                     LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
                                                     LEFT JOIN fruta_vespecies V2 ON detalle.ID_VESPECIES = V2.ID_VESPECIES
                                                     WHERE recepcion.ESTADO_REGISTRO = 1
                                                     AND recepcion.ESTADO = 0
                                                     AND detalle.ESTADO_REGISTRO = 1
                                                     AND recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                     AND V2.ID_ESPECIES = '".$ESPECIE."'
                                                     AND recepcion.ID_PRODUCTOR IN ('".$productoresIn."')) AS KILOS_TOTALES
                                                FROM fruta_exiexportacion EXPEXPORT
                                                LEFT JOIN fruta_vespecies V ON EXPEXPORT.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXPEXPORT.ESTADO_REGISTRO = 1
                                                AND EXPEXPORT.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND EXPEXPORT.ID_PRODUCTOR IN ('".$productoresIn."');");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            if (!$resultado) {
                return 0;
            }

            $kilosExportados = $resultado[0]['KILOS_EXPORTADOS'];
            $kilosTotales = $resultado[0]['KILOS_TOTALES'];

            if ($kilosTotales <= 0) {
                return 0;
            }

            return round(($kilosExportados / $kilosTotales) * 100, 2);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function existenciaMateriaPrimaProductor($TEMPORADA, $ESPECIE, $PRODUCTORES)
    {
        try {
            if (empty($PRODUCTORES)) {
                return 0;
            }

            $productoresIn = implode("','", $PRODUCTORES);

            $datos = $this->conexion->prepare("SELECT IFNULL(SUM(EXI.KILOS_NETO_EXIMATERIAPRIMA),0) AS TOTAL
                                                FROM fruta_eximateriaprima EXI
                                                LEFT JOIN fruta_vespecies V ON EXI.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND EXI.ESTADO = 2
                                                AND EXI.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND V.ID_ESPECIES = '".$ESPECIE."'
                                                AND EXI.ID_PRODUCTOR IN ('".$productoresIn."');");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado ? $resultado[0]['TOTAL'] : 0;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ExistenciaMateriaPrimaPorVariedad($TEMPORADA, $EMPRESA, $PLANTA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT V.NOMBRE_VESPECIES AS NOMBRE,
                                                    IFNULL(SUM(EXI.KILOS_NETO_EXIMATERIAPRIMA),0) AS TOTAL
                                                FROM fruta_eximateriaprima EXI
                                                LEFT JOIN fruta_vespecies V ON EXI.ID_VESPECIES = V.ID_VESPECIES
                                                WHERE EXI.ESTADO_REGISTRO = 1
                                                AND EXI.ESTADO = 2
                                                AND EXI.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND EXI.ID_EMPRESA = '".$EMPRESA."'
                                                AND EXI.ID_PLANTA = '".$PLANTA."'
                                                GROUP BY EXI.ID_VESPECIES");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function verPlanta($ID_PLANTA){
        try{
            
            $datos=$this->conexion->prepare("SELECT * FROM   principal_planta   WHERE   ID_PLANTA  = '".$ID_PLANTA."';");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;
            
            //	print_r($resultado);
            //	var_dump($resultado);
            
            
            return $resultado;
        }catch(Exception $e){
            die($e->getMessage());
        }
        
    }

    




/* FIN CONSULTAS DASHBOARD */

    //FUNCIONES ESPECIALIZADAS 
    //RECEPCION VS PROCESO
    public function acumuladoRecepcionMp($TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle, principal_empresa empresa
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND recepcion.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND  empresa.ESTADO_REGISTRO = 1
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1 
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function acumuladoRecepcionMpEst($TEMPORADA, $ESPECIE)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion
												LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
											    LEFT JOIN principal_empresa empresa ON recepcion.ID_EMPRESA = empresa.ID_EMPRESA
												LEFT JOIN fruta_vespecies VES ON detalle.ID_VESPECIES = VES.ID_VESPECIES
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND recepcion.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND  empresa.ESTADO_REGISTRO = 1
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1 
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."' AND VES.ID_ESPECIES = '" . $ESPECIE . "'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function acumuladoRecepcionMpNoBulk($TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle, principal_empresa empresa
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND recepcion.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND  empresa.ESTADO_REGISTRO = 1
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1 
                                                AND recepcion.TRECEPCION !=3
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function acumuladoRecepcionMpBulk($TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle, principal_empresa empresa
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND  recepcion.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND  empresa.ESTADO_REGISTRO = 1
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1 
                                                AND  recepcion.TRECEPCION = 3
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function acumuladoRecepcionMpBulkEst($TEMPORADA, $ESPECIE)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion 
                                                LEFT JOIN fruta_drecepcionmp detalle ON recepcion.ID_RECEPCION = detalle.ID_RECEPCION
												LEFT JOIN principal_empresa empresa ON recepcion.ID_EMPRESA = empresa.ID_EMPRESA
												LEFT JOIN fruta_vespecies VES ON detalle.ID_VESPECIES = VES.ID_VESPECIES
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND  recepcion.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND  empresa.ESTADO_REGISTRO = 1
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1 
                                                AND  recepcion.TRECEPCION = 3
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."' AND VES.ID_ESPECIES = '" . $ESPECIE . "'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function acumuladoRecepcionMpPorEmpresa($EMPRESA,$TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_EMPRESA = '".$EMPRESA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function acumuladoRecepcionMpNoBulkPorEmpresa($EMPRESA,$TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND  recepcion.TRECEPCION != 3
                                                AND recepcion.ID_EMPRESA = '".$EMPRESA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function acumuladoRecepcionMpBulkPorEmpresa($EMPRESA,$TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND  recepcion.TRECEPCION = 3
                                                AND recepcion.ID_EMPRESA = '".$EMPRESA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function acumuladoRecepcionMpPorPlanta($PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle, principal_empresa empresa
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND recepcion.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND  empresa.ESTADO_REGISTRO = 1
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND  recepcion.ID_PLANTA = '".$PLANTA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function acumuladoRecepcionMpNoBulkPorPlanta($PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle, principal_empresa empresa
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND recepcion.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND  empresa.ESTADO_REGISTRO = 1
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND  recepcion.TRECEPCION !=3
                                                AND  recepcion.ID_PLANTA = '".$PLANTA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function acumuladoRecepcionMpBulkPorPlanta($PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle, principal_empresa empresa
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND recepcion.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND  empresa.ESTADO_REGISTRO = 1
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND  recepcion.TRECEPCION =3
                                                AND recepcion.ID_PLANTA = '".$PLANTA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function acumuladoRecepcionMpPorEmpresaPlanta($EMPRESA, $PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND recepcion.ID_EMPRESA = '".$EMPRESA."'
                                                AND recepcion.ID_PLANTA = '".$PLANTA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function acumuladoRecepcionMpNoBulkPorEmpresaPlanta($EMPRESA, $PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND  recepcion.TRECEPCION !=3
                                                AND recepcion.ID_EMPRESA = '".$EMPRESA."'
                                                AND recepcion.ID_PLANTA = '".$PLANTA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function acumuladoRecepcionMpBulkPorEmpresaPlanta($EMPRESA, $PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(detalle.KILOS_NETO_DRECEPCION),0)  AS 'NETO' 
                                                FROM  fruta_recepcionmp recepcion ,  fruta_drecepcionmp detalle
                                                WHERE recepcion.ID_RECEPCION =  detalle.ID_RECEPCION
                                                AND  recepcion.ESTADO = 0
                                                AND  recepcion.ESTADO_REGISTRO = 1
                                                AND  detalle.ESTADO_REGISTRO = 1
                                                AND  recepcion.TRECEPCION =3
                                                AND recepcion.ID_EMPRESA = '".$EMPRESA."'
                                                AND recepcion.ID_PLANTA = '".$PLANTA."'
                                                AND  recepcion.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function acumuladoProcesadoMp($TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(existencia.KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima existencia, principal_empresa empresa
                                                WHERE existencia.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND empresa.ESTADO_REGISTRO = 1
                                                AND existencia.ESTADO_REGISTRO = 1 
                                                AND existencia.ID_PROCESO IS NOT NULL                                             
                                                AND existencia.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function acumuladoProcesadoMpEst($TEMPORADA, $ESPECIE)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(existencia.KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima existencia
                                                LEFT JOIN principal_empresa empresa ON existencia.ID_EMPRESA = empresa.ID_EMPRESA
                                                LEFT JOIN fruta_vespecies VES ON existencia.ID_VESPECIES = VES.ID_VESPECIES
                                                WHERE existencia.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND empresa.ESTADO_REGISTRO = 1
                                                AND existencia.ESTADO_REGISTRO = 1 
                                                AND existencia.ID_PROCESO IS NOT NULL                                             
                                                AND existencia.ID_TEMPORADA = '".$TEMPORADA."' AND VES.ID_ESPECIES = '" . $ESPECIE . "'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function acumuladoProcesadoMpPorEmpresa($EMPRESA,$TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima 
                                                WHERE ESTADO_REGISTRO = 1 
                                                AND ID_PROCESO IS NOT NULL
                                                AND ID_EMPRESA = '".$EMPRESA."'                                    
                                                AND  ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function acumuladoProcesadoMpPorPlanta($PLANTA,$TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(existencia.KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima existencia, principal_empresa empresa
                                                WHERE existencia.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND empresa.ESTADO_REGISTRO = 1
                                                AND existencia.ESTADO_REGISTRO = 1 
                                                AND existencia.ID_PROCESO IS NOT NULL                                             
                                                AND existencia.ID_TEMPORADA = '".$TEMPORADA."'
                                                AND existencia.ID_PLANTA = '".$PLANTA."'   
    
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function acumuladoProcesadoMpPorEmpresaPlanta($EMPRESA, $PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima 
                                                WHERE ESTADO_REGISTRO = 1 
                                                AND ID_PROCESO IS NOT NULL
                                                AND ID_EMPRESA = '".$EMPRESA."'
                                                AND ID_PLANTA = '".$PLANTA."'                                    
                                                AND  ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


 
    //EXISTENCIA DISPONIBLE
    public function existenciaDisponibleMp($TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(existencia.KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima existencia, principal_empresa empresa
                                                WHERE existencia.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND empresa.ESTADO_REGISTRO = 1
                                                AND existencia.ESTADO_REGISTRO = 1 
                                                AND existencia.ESTADO = 2                                           
                                                AND existencia.ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }


    public function existenciaDisponibleMpEst($TEMPORADA, $ESPECIE)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(existencia.KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima existencia
                                                LEFT JOIN principal_empresa empresa ON existencia.ID_EMPRESA = empresa.ID_EMPRESA
												LEFT JOIN fruta_vespecies VES ON existencia.ID_VESPECIES = VES.ID_VESPECIES
                                                WHERE existencia.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND empresa.ESTADO_REGISTRO = 1
                                                AND existencia.ESTADO_REGISTRO = 1 
                                                AND existencia.ESTADO = 2                                           
                                                AND existencia.ID_TEMPORADA = '".$TEMPORADA."' AND VES.ID_ESPECIES = '" . $ESPECIE . "'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function existenciaDisponibleMpPorEmpresa($EMPRESA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                      IFNULL(SUM(KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima 
                                                WHERE ESTADO_REGISTRO = 1 
                                                AND ESTADO = 2      
                                                AND ID_EMPRESA  = '".$EMPRESA."'                                          
                                                AND  ID_TEMPORADA = '".$TEMPORADA."'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function existenciaDisponibleMpPorPlanta($PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                     IFNULL(SUM(existencia.KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima existencia, principal_empresa empresa
                                                WHERE existencia.ID_EMPRESA=empresa.ID_EMPRESA
                                                AND empresa.ESTADO_REGISTRO = 1
                                                AND existencia.ESTADO_REGISTRO = 1 
                                                AND existencia.ESTADO = 2 
                                                AND existencia.ID_PLANTA = '".$PLANTA ."'                                         
                                                AND existencia.ID_TEMPORADA = '".$TEMPORADA."'           
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    public function existenciaDisponibleMpPorEmpresaPlanta($EMPRESA,$PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                      IFNULL(SUM(KILOS_NETO_EXIMATERIAPRIMA),0)  AS 'NETO' 
                                                FROM fruta_eximateriaprima 
                                                WHERE ESTADO_REGISTRO = 1 
                                                AND ESTADO = 2         
                                                AND ID_EMPRESA = '".$EMPRESA."'        
                                                AND ID_PLANTA  = '".$PLANTA ."'                                              
                                                AND  ID_TEMPORADA = '".$TEMPORADA."'

                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    


    public function contarRegistrosAbiertosMateriales($EMPRESA, $PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                    (
                                                        select IFNULL( COUNT(recepcione.ID_RECEPCION),0) 
                                                        FROM material_recepcione recepcione
                                                        WHERE recepcione.ESTADO = 1     
                                                        AND recepcione.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                    ) AS 'RECEPCIONE',
                                                    (
                                                        select IFNULL( COUNT(recepcionm.ID_RECEPCION),0) 
                                                        FROM material_recepcionm recepcionm
                                                        WHERE recepcionm.ESTADO = 1
                                                        AND recepcionm.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                    ) AS 'RECEPCIONM',
                                                    (
                                                        select IFNULL( COUNT(despachoe.ID_DESPACHO),0) 
                                                        FROM material_despachoe despachoe
                                                        WHERE despachoe.ESTADO = 1
                                                        AND despachoe.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                    ) AS 'DESPACHOE',
                                                    (
                                                        select IFNULL( COUNT(despachom.ID_DESPACHO),0) 
                                                        FROM material_despachom despachom
                                                        WHERE despachom.ESTADO = 1
                                                        AND despachom.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                    ) AS 'DESPACHOM'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    public function contarRegistrosAbiertosFruta($EMPRESA, $PLANTA, $TEMPORADA)
    {
        try {

            $datos = $this->conexion->prepare("SELECT 
                                                        ( SELECT IFNULL( COUNT(recepcionmp.ID_RECEPCION),0)
                                                        FROM fruta_recepcionmp recepcionmp
                                                        WHERE recepcionmp.ESTADO = 1
                                                        AND recepcionmp.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'RECEPCIONMP',
                                                        ( SELECT IFNULL( COUNT(recepcionind.ID_RECEPCION),0)
                                                        FROM fruta_recepcionind recepcionind
                                                        WHERE recepcionind.ESTADO = 1
                                                        AND recepcionind.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        )AS 'RECEPCIONIND',
                                                        ( SELECT IFNULL( COUNT(recepcionp.ID_RECEPCION),0)
                                                        FROM fruta_recepcionpt recepcionp
                                                        WHERE recepcionp.ESTADO = 1
                                                        AND recepcionp.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        )AS 'RECEPCIONPT', 
                                                        ( SELECT IFNULL( COUNT(recepcionmp.ID_RECEPCION),0)
                                                        FROM fruta_recepcionmp recepcionmp
                                                        WHERE recepcionmp.ESTADO = 1
                                                        AND recepcionmp.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        )+
                                                        ( SELECT IFNULL( COUNT(recepcionind.ID_RECEPCION),0)
                                                        FROM fruta_recepcionind recepcionind
                                                        WHERE recepcionind.ESTADO = 1
                                                        AND recepcionind.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        )+
                                                        ( SELECT IFNULL( COUNT(recepcionp.ID_RECEPCION),0)
                                                        FROM fruta_recepcionpt recepcionp
                                                        WHERE recepcionp.ESTADO = 1
                                                        AND recepcionp.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'RECEPCION',
                                                        ( SELECT IFNULL( COUNT(despachomp.ID_DESPACHO),0)
                                                        FROM fruta_despachomp despachomp
                                                        WHERE despachomp.ESTADO = 1
                                                        AND despachomp.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'DESPACHOMP',
                                                        ( SELECT IFNULL( COUNT(despachoind.ID_DESPACHO),0)
                                                        FROM fruta_despachoind despachoind
                                                        WHERE despachoind.ESTADO = 1
                                                        AND despachoind.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'DESPACHOIND',
                                                        ( SELECT IFNULL( COUNT(despachopt.ID_DESPACHO),0)
                                                        FROM fruta_despachopt despachopt
                                                        WHERE despachopt.ESTADO = 1
                                                        AND despachopt.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'DESPACHOPT',
                                                        ( SELECT IFNULL( COUNT(despachoex.ID_DESPACHOEX),0)
                                                        FROM fruta_despachoex despachoex
                                                        WHERE despachoex.ESTADO = 1
                                                        AND despachoex.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'DESPACHOEXPO'  ,
                                                        ( SELECT IFNULL( COUNT(despachomp.ID_DESPACHO),0)
                                                        FROM fruta_despachomp despachomp
                                                        WHERE despachomp.ESTADO = 1
                                                        AND despachomp.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) +
                                                        ( SELECT IFNULL( COUNT(despachoind.ID_DESPACHO),0)
                                                        FROM fruta_despachoind despachoind
                                                        WHERE despachoind.ESTADO = 1
                                                        AND despachoind.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) +
                                                        ( SELECT IFNULL( COUNT(despachopt.ID_DESPACHO),0)
                                                        FROM fruta_despachopt despachopt
                                                        WHERE despachopt.ESTADO = 1
                                                        AND despachopt.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) +
                                                        ( SELECT IFNULL( COUNT(despachoex.ID_DESPACHOEX),0)
                                                        FROM fruta_despachoex despachoex
                                                        WHERE despachoex.ESTADO = 1
                                                        AND despachoex.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'DESPACHO',
                                                        ( SELECT IFNULL( COUNT(proceso.ID_PROCESO),0)
                                                        FROM fruta_proceso proceso
                                                        WHERE proceso.ESTADO = 1
                                                        AND proceso.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'PROCESO' ,
                                                        ( SELECT IFNULL( COUNT(reembalaje.ID_REEMBALAJE),0)
                                                        FROM fruta_reembalaje reembalaje
                                                        WHERE reembalaje.ESTADO = 1
                                                        AND reembalaje.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'REEMBALAJE',
                                                        ( SELECT IFNULL( COUNT(repaletizajeex.ID_REPALETIZAJE),0)
                                                        FROM fruta_repaletizajeex repaletizajeex
                                                        WHERE repaletizajeex.ESTADO = 1
                                                        AND repaletizajeex.ESTADO_REGISTRO = 1
                                                        AND ID_EMPRESA = '".$EMPRESA."'
                                                        AND ID_PLANTA = '".$PLANTA."'
                                                        AND ID_TEMPORADA = '".$TEMPORADA."'
                                                        ) AS 'REPALETIZAJE'
                                                ;	");
            $datos->execute();
            $resultado = $datos->fetchAll();
            $datos=null;

            //	print_r($resultado);
            //	var_dump($resultado);


            return $resultado;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
