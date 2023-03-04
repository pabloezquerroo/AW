<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\mensajes;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\animales\Animal;
/* */

class Mensaje{

    public const MENSAJE = 1;
    public const PETICION = 2;

    private $IdEmisor;
    private $IdReceptor;
    private $tipo;
    private $mensaje;
    private $fecha;
    private $idAnimal;
    private $idMensaje;


    /* Constructor de Mensaje*/
    private function __construct($IdEmisor, $IdReceptor,  $mensaje, $fecha,$idAnimal,$tipo, $idMensaje = null){
        $this->IdEmisor = $IdEmisor;
        $this->IdReceptor = $IdReceptor;
        $this->tipo = $tipo;
        $this->mensaje = $mensaje;
        $this->fecha = $fecha; // ver como se manejan los tipos date devueltos por la bd
        $this->idAnimal = $idAnimal;
        $this->idMensaje = $idMensaje;
    }

    public function getEmisor(){
        return $this->IdEmisor;
    }
    public function getReceptor(){
        return $this->IdReceptor;
    }
    public function getMensaje(){
        return $this->mensaje;
    }

    public function getTipo(){
        return $this->tipo;
    }

    public function getAnimal(){
        return $this->idAnimal;
    }
    public function getIdMensaje(){
        return $this->idMensaje;
    }

    /* FUNCIONES DE BUSQUEDA */

    /* Busca los mensajes de dos personas */ 
    public static function buscaMensajes($Id1, $Id2){ // $Id1 seria el usuario que inicio sesión
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM mensaje WHERE (IdEmisor = $Id1 or IdEmisor = $Id2) 
        and (IdReceptor = $Id1 or IdReceptor = $Id2) order by fecha");
        $rs = $conn->query($query);
        $result = null;
        $i = 0;
        if($rs->num_rows > 0){
            while($fila = $rs->fetch_assoc()){
                $result[$i] = new Mensaje($fila['IdEmisor'],$fila['IdReceptor'],
                $fila['mensaje'], $fila['fecha'], $fila['IdAnimal'], $fila['tipo'], $fila['ID']);
                $i++;
            }
        }
        else{
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function enviarMensaje($IdEmisor, $IdReceptor, $mensaje, $tipo, $idAnimal= null){
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("INSERT INTO mensaje(IdEmisor, IdReceptor, mensaje, tipo, idAnimal) VALUES ('%s', '%s', '%s', '%d', '%d')"
        , $IdEmisor, $IdReceptor, $mensaje, $tipo,$idAnimal);
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function buscaChats($id){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT DISTINCT p.ID FROM protectora p JOIN mensaje m 
                        on (p.ID = m.IdEmisor or p.ID = m.IdReceptor) AND (%d = m.IdEmisor or %d = m.IdReceptor)
                        WHERE p.ID != %d order by fecha desc", $id,$id,$id); 

        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if($rs){
            while($fila = $rs->fetch_assoc()){
                $result[$i] = Protectora::buscaPorId($fila['ID']);
                $i++;
            }
            $rs->free();
        }
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function buscaChatsLibres($id){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT DISTINCT protectora.ID
        FROM protectora
        WHERE protectora.ID != ALL (
            SELECT p.ID FROM protectora p JOIN mensaje m 
                        on (p.ID = m.IdEmisor or p.ID = m.IdReceptor) AND (%d = m.IdEmisor or %d = m.IdReceptor)) AND protectora.ID != %d AND protectora.estado",$id, $id,$id, Protectora::ACTIVA); 
        // faltaria ver como hacer para los mensajes recibidos sin responder
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if($rs){
            while($fila = $rs->fetch_assoc()){
                $result[$i] = Protectora::buscaPorId($fila['ID']);
                $i++;
            }
            $rs->free();
        }
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    private static function editaMensaje($id, $mensaje, $tipo){
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE mensaje SET mensaje = '%s', tipo= %d WHERE ID=%d"
            , $mensaje,$tipo,$id
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }

    public static function existePeticion($idp1,$idp2,$idA){
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT *
                            FROM mensaje
                            WHERE IdEmisor = %d AND IdReceptor = %d AND IdAnimal = %d",$idp1,$idp2,$idA); 
        $rs = $conn->query($query);
        if($rs){
            $fila = $rs->fetch_assoc();
            if($fila != null){
                $result = true;
            }
        }
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function aceptaTraslado($id){
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT *
                            FROM mensaje
                            WHERE ID = %d",$id); 
        $rs = $conn->query($query);
        if($rs){
            $fila = $rs->fetch_assoc();
            $result = Animal::trasladoAnimal($fila['IdAnimal'],$fila['IdReceptor']);
            self::editaMensaje($id, "Traslado de animal completado correctamente", self::MENSAJE);
        }
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function rechazaTraslado($id){
        self::editaMensaje($id, "No se ha podido completar el traslado animal", self::MENSAJE);
    }
}