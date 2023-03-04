<?php
namespace es\ucm\fdi\aw\eventos;

/*Añadido nose si esta bien */
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\protectora\Colabora;
/**/

class Participa {

    public const CREADOR = 1;
    public const PARTICIPANTE = 2;

    private $idProtectora;
    private $idEvento;
    private $rol;

    /* Constructor de Participa */
    private function __construct($idProtectora, $idEvento=null, $rol = self::CREADOR)
    {
        $this->idProtectora = $idProtectora;
        $this->idEvento = $idEvento;
        $this->rol = $rol;
    }

    /* Actualiza la entrada en participa con los nuevos datos */
    private static function actualiza($participa)
    {
        $result = $participa;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE participa  SET rol='%d' WHERE id_protectora=%d AND id_evento=%d"
            , $participa->rol
            , $participa->idProtectora
            , $participa->idEvento
        );
        if ( !$conn->query($query) ) {
            $result = null;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }

    public static function cuentaProtectorasParticipantes($idEvento){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM participa WHERE id_evento=%d", $idEvento);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /*  FUNCIONES DE BUSQUEDA */

    public static function buscaCreadorEvento($idEvento){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT id_protectora FROM participa WHERE id_evento='%d' AND rol=%d",$idEvento, self::CREADOR);
        $rs = $conn->query($query);
        $result = null; 
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = Protectora::buscaPorId($fila['id_protectora']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* busca los participantes para la paginación */
    public static function buscaParticipantesEventoConLimite($idEvento, $offset, $limit){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM participa WHERE id_evento=%d ORDER BY id_evento LIMIT $offset, $limit", $idEvento);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = Protectora::buscaPorId($fila['id_protectora']);
                $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Devuelve true si es participante o creador*/
    public static function esParticipanteOCreadorEvento($idProtectora, $idEvento){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM participa WHERE id_evento=%d AND id_protectora=%d", $idEvento, $idProtectora);
        $rs = $conn->query($query);
        $result = false;
        if ($rs->num_rows > 0) {
            $result=true;
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Devuelve true si es participante o creador*/
    public static function esCreadorEvento($idProtectora, $idEvento){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM participa WHERE id_evento=%d AND id_protectora=%d AND rol=%d", $idEvento, $idProtectora, self::CREADOR);
        $rs = $conn->query($query);
        $result = false;
        if ($rs->num_rows > 0) {
            $result=true;
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }    

    /* Devuelve lista con las protectoras en las que el usuario colabora o es creador que no participan en el evento dado */
    public static function buscaMisProtectorasNoParticipantes($estadoProtectora, $idUsuario, $idEvento)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();        
        $query = sprintf("SELECT P.ID FROM protectora P JOIN colabora C ON P.ID=C.iDProtectora WHERE P.estado=%d AND C.iDUsuario=%d AND (C.rol=%d OR C.rol=%d) AND P.ID NOT IN (SELECT P.ID FROM protectora P JOIN participa A ON P.ID=A.id_protectora WHERE A.id_evento=%d)"
        , $estadoProtectora, $idUsuario, Colabora::COLABORA, COLABORA::CREADOR, $idEvento);    
        $rs = $conn->query($query);
        $result = null;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
               $result[$i] = Protectora::buscaPorId($fila['ID']);
               $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

     /* Devuelve lista con las protectoras en las que el usuario colabora o es creador que participan en el evento dado */
    public static function buscaMisProtectorasParticipantes($estadoProtectora, $idUsuario, $idEvento)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();        
        $query = sprintf("SELECT P.ID FROM protectora P JOIN colabora C ON P.ID=C.iDProtectora WHERE P.estado=%d AND C.iDUsuario=%d AND (C.rol=%d OR C.rol=%d) AND P.ID IN (SELECT P.ID FROM protectora P JOIN participa A ON P.ID=A.id_protectora WHERE A.id_evento=%d AND A.rol=%d)"
        , $estadoProtectora, $idUsuario, Colabora::COLABORA, Colabora::CREADOR, $idEvento, Participa::PARTICIPANTE);    
        $rs = $conn->query($query);
        $result = null;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
               $result[$i] = Protectora::buscaPorId($fila['ID']);
               $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }


public static function esMiProtectoraCreadorEvento($estadoProtectora, $idUsuario, $idEvento)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();        
        $query = sprintf("SELECT P.ID FROM protectora P JOIN colabora C ON P.ID=C.iDProtectora WHERE P.estado=%d AND C.iDUsuario=%d AND (C.rol=%d OR C.rol=%d) AND P.ID IN (SELECT P.ID FROM protectora P JOIN participa A ON P.ID=A.id_protectora WHERE A.id_evento=%d AND A.rol=%d)"
        , $estadoProtectora, $idUsuario, Colabora::COLABORA, Colabora::CREADOR, $idEvento, Participa::CREADOR);    
        $rs = $conn->query($query);
        $result = false;
        if ($rs->num_rows > 0) {
               $result = true;
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /*  FUNCIONES DE BORRADO */

    public static function eliminaParticipacionEvento($idEvento, $idProtectora)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("DELETE FROM participa WHERE id_evento=%d AND id_protectora=%d"
            , $idEvento
            , $idProtectora
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }        
        return $result;
    }

    public static function eliminaParticipantes($idEvento)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("DELETE FROM participa WHERE id_evento=%d AND rol=%d"
            , $idEvento
            , self::PARTICIPANTE
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function existeEvento($idEvento) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM participa WHERE id_evento=%d", $idEvento);
        $rs = $conn->query($query);
        $result = 0; 
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if($fila) {
                $result = 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
        }
    

    /* FUNCIONES DE INSERCION */

    /* inserta una nueva entrada participa en la BD */
    private static function inserta($participa)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO participa(id_protectora, id_evento, rol) VALUES ('%d', '%d', '%d')"
            , $participa->idProtectora
            , $participa->idEvento
            , $participa->rol
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

     /* Crea una entrada en colabora */
     public static function crea($idEvento, $idProtectora)
     {
            $participa = ' ';
         if(! (self::existeEvento($idEvento))){
                $participa = new Participa($idProtectora, $idEvento);
         }
         else{
            $participa = new Participa($idProtectora, $idEvento, self::PARTICIPANTE);
         }
 
         
         return $participa->guarda();
     }
 
     /* Guardo una entrada en colabora */
     public function guarda()
     {
         if($this->idEvento!==null){
             return self::inserta($this);
         }
         else{
             return self::actualiza($this);
         }
         
     }
 
     /* GUETTERS */

     public function getIdProtectora()
     {
        return $this->idProtectora;
     }
 
     public function getIdEvento()
     {
        return $this->idEvento;
     }
 
     public function getRol()
     {
        return $this->rol;
     }

     /* FUNCIONES DE CREAR Y GUARDAR */
}
?>