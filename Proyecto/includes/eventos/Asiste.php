<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\usuarios\Usuario;


class Asiste {

    private $idUsuario;
    private $idEvento;


    /* Constructor de Asiste */
    private function __construct($idUsuario, $idEvento)
    {
        $this->idUsuario = $idUsuario;
        $this->idEvento = $idEvento;
    }

    public static function cuentaUsuariosAsistentes($idEvento){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM asiste WHERE id_evento=%d ", $idEvento);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /*  FUNCIONES DE BUSQUEDA */

    /* Devuelve si un usuario es asistente a un evento*/
    public static function esAsistenteEvento($idUsuario, $idEvento){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM asiste WHERE id_evento=%d AND id_usuario=%d", $idEvento, $idUsuario);
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

    
    /* Devuelve si existe el evento en la tabla asiste */
    public static function existeEvento($idEvento) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM asiste WHERE id_evento=%d", $idEvento);
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
    

    /*  FUNCIONES DE BORRADO */
    /* Elimina el asistente del evento dado */
    public static function eliminaAsistenteEvento($idEvento, $idUsuario)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("DELETE FROM asiste WHERE id_evento=%d AND id_usuario=%d"
            , $idEvento
            , $idUsuario
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }        
        return $result;
    }

        /* Elimna todos los asistentes del evento dado*/
    public static function eliminaAsistentes($idEvento)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("DELETE FROM asiste WHERE id_evento=%d"
            , $idEvento
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }


    /* FUNCIONES DE INSERCION */

    /* inserta una nueva entrada colabora en la BD */
    private static function inserta($asiste)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO asiste(id_usuario, id_evento) VALUES ('%d', '%d')"
            , $asiste->idUsuario
            , $asiste->idEvento
        );
        if ( !$conn->query($query) ) {
            $result = false;    
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

     /* Crea una entrada en colabora */
     public static function crea($idEvento, $idUsuario)
     {
         $participa = new Asiste($idUsuario, $idEvento);
         return $participa->guarda();
     }
 
     /* Guardo una entrada en colabora */
     public function guarda()
     {
        return self::inserta($this);
     }
 

     /* GUETTERS */

     public function getidUsuario()
     {
        return $this->idUsuario;
     }
 
     public function getIdEvento()
     {
        return $this->idEvento;
     }
}
?>