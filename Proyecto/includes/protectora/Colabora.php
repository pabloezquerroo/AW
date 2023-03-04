<?php
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\usuarios\Usuario;

class Colabora {
    /* CONSTANTES PARA EL ROL DEL COLABORADOR */
    public const PENDIENTE = 1;
    public const CREADOR = 2;
    public const COLABORA = 3;

    /* ATRIBUTOS DE LA CLASE */
    private $iDProtectora = NULL;
    private $iDUsuario = NULL;
    private $rol;

    //CONSTRUCTOR

    /* Constructor de Colabora */
    private function __construct($iDProtectora, $iDUsuario, $rol)
    {
        $this->iDProtectora = $iDProtectora;
        $this->iDUsuario = $iDUsuario;
        $this->rol = $rol;
    }

    //FUNCIONES DE CONTEO

    /* Cuenta el numero de miembros de una protectora dada */
    public static function cuentaMiembrosProtectora($idProtectora){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDprotectora=%d AND (rol=%d OR rol=%d)", $idProtectora, self::COLABORA, self::CREADOR);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /* Cuenta el numero de solicitudes que tiene una protectora */
    public static function cuentaSolicitudesColabora($idProtectora){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDprotectora=%d AND rol=%d", $idProtectora, self::PENDIENTE);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    //FUNCIONES DE INSERCION Y DE ACTUALIZAR LA TABLA EN LA BBDD

    /* Actualiza la entrada en la tabla colabora con los nuevos datos */
    private static function actualiza($colabora)
    {
        $result = $colabora;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE colabora  SET rol=%d WHERE IDprotectora=%d AND IDusuario=%d"
                        , $colabora->rol, $colabora->iDProtectora, $colabora->iDUsuario);

        if ( !$conn->query($query) ) {
            $result = null;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* inserta una nueva entrada colabora en la BD */
    private static function inserta($colabora)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO colabora(IDprotectora, IDusuario, rol) VALUES (%d, %d, %d)"
                        , $colabora->iDProtectora, $colabora->iDUsuario, $colabora->rol);
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    //FUNCIONES DE BUSQUEDA
    
    /* busca el creador o creadores de la protectora pasada por parametro */
    public static function buscaCreadorProtectora($iDProtectora)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDprotectora=%d AND rol=%d", 
                        $iDProtectora, self::CREADOR);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()){
                $result[$i] = Usuario::buscaPorId($fila['IDusuario']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Devuelve una lista con los Usuarios en estado pendiente de una protectora dada */
    public static function buscaColaboradoresPendientesPorProtectora($idProtectora, $offset, $limit)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDprotectora=%d AND rol=%d ORDER BY IDprotectora LIMIT $offset, $limit", $idProtectora, self::PENDIENTE);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = Usuario::buscaPorId($fila['IDusuario']);
                $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Devuelve si un usuario está en  estado pendiente en una protectora dada */
    public static function esColaboradorPendiente($idProtectora, $idUsuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDprotectora=%d AND IDusuario=%d AND rol=%d", $idProtectora, $idUsuario, self::PENDIENTE);
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

    /* Esta funcion indica si un usuario pasado por parametro (su ID) es Creador de la protectora dada. */
    public static function isCreador($iDUsuario, $idProtectora)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDusuario=%d AND IDprotectora=%d AND rol=%d",
                        $iDUsuario, $idProtectora, self::CREADOR);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            $result=true;
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Devuelve una lista con las protectoras a las que pertenece el usuario. */
    public static function buscaProtectorasPorUsuario($iDUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDusuario=%d", $iDUsuario);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = Protectora::buscaPorId($fila['IDprotectora']);
                $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Indica si es colaborador o Creador de alguna protectora */
    public static function isColaboraOrCreador($iDUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDusuario=%d AND (rol=%d OR rol=%d)",
        $iDUsuario, self::CREADOR, self::COLABORA);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            $result=true;
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Indica si es un colaborador o Creador de una protectora Activa */
    public static function isColaboraOrCreadorActiva($iDUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora C JOIN protectora P WHERE C.IDprotectora=P.ID AND C.IDusuario=%d AND (C.rol=%d OR C.rol=%d) AND P.estado=%d",
        $iDUsuario, self::CREADOR, self::COLABORA, Protectora::ACTIVA);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            $result=true;
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Indica si es creador o colaborador de la protectora dada */
    public static function isColaboraOrCreadorProtectora($iDUsuario, $idProtectora){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora C JOIN protectora P WHERE C.IDprotectora=%d AND C.IDusuario=%d AND (C.rol=%d OR C.rol=%d) AND P.estado=%d",
        $idProtectora, $iDUsuario, self::CREADOR, self::COLABORA, Protectora::ACTIVA);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            $result=true;
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Indica si el usuario Colabora en la protectora dada */
    public static function buscaColaborador($iDUsuario,$iDProtectora){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDusuario=%d AND (IDprotectora=%d)",
        $iDUsuario, $iDProtectora);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($fila = $rs->fetch_assoc()) {
            $result= new Colabora($fila['IDprotectora'],  $fila['IDusuario'], $fila['rol']); ;
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* busca los usuarios activos (CREADORES O COLABORADORES) pertenicientes a una protectora */
    public static function buscaMiembrosProtectora($iDProtectora, $offset, $limit){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora WHERE IDprotectora=%d AND (rol=%d OR rol=%d) ORDER BY IDprotectora LIMIT $offset, $limit", $iDProtectora, self::COLABORA, self::CREADOR);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
               $result[$i] = new Colabora($fila['IDprotectora'],  $fila['IDusuario'], $fila['rol']); 
               $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* devuelve protectoras a las que el ususario pertenece */
    public static function ProtectorasPertenecientes($iDUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora C WHERE C.IDusuario=%d", $iDUsuario);
        $rs = $conn->query($query);
        $result = false; 
        $i = 0;
        if ($rs) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = Protectora::buscaPorId($fila['IDprotectora']);
                $i = $i + 1;
             }  
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Busca si un usuario dado pertenece a una protectora dada */
    public static function perteneceProtectora($iDUsuario, $iDProtectora){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM colabora  WHERE IDprotectora=%d AND IDusuario = %d", 
        $iDProtectora,
        $iDUsuario);
        $rs = $conn->query($query);
        $result = false; 
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if($fila) {
                $result = new Colabora($fila['IDprotectora'],  $fila['IDusuario'], $fila['rol']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    //FUNCIONES DE BORRADO
    
    /* Borra entrada en colabora */
    public function borrate()
    {
        if ($this->iDProtectora !== null || $this->iDUsuario !== null) {
            return self::borra($this);
        }
        return false;
    }

     /* Borra la entrada en colabora pasada por parametro */
     private static function borra($colabora)
     {
         if (!$colabora) {
             return false;
         } 
         $conn = Aplicacion::getInstance()->getConexionBd();
         $query = sprintf("DELETE FROM colabora  WHERE IDusuario = %d AND IDprotectora = %d", $colabora->iDUsuario, $colabora->iDProtectora);
         if ( ! $conn->query($query) ) {
             error_log("Error BD ({$conn->errno}): {$conn->error}");
             return false;
         }
         return true;
     }

     /* GUETTERS */

     public function getIdProtectora()
     {
        return $this->iDProtectora;
     }
 
     public function getIdUsuario()
     {
        return $this->iDUsuario;
     }
 
     public function getRol()
     {
        return $this->rol;
     }

     //FUNCIONES DE CREAR Y GUARDAR 

    /* Crea una entrada en colabora */
    public static function crea($iDProtectora, $iDUsuario, $rol)
    {
        $colabora = ' ';
        if(!(self::perteneceProtectora($iDUsuario, $iDProtectora))){
            $colabora = new Colabora($iDProtectora, $iDUsuario, $rol);
        }
        else{
           $colabora = new Colabora($iDProtectora, $iDUsuario, $rol);
        }

        
        return $colabora->guarda();
    }

    /* Funcion que se utiliza al crear una protectora, para asignar Creador al usuario que ha creado la protectora*/ 
    public static function creaCreador($iDProtectora, $iDUsuario, $rol){
        $colabora = new Colabora($iDProtectora, $iDUsuario, $rol);
        return self::inserta($colabora);
    }

    /* Funcion usada para modificar los permisos de un usuario. */
    public function modifica($rol){
        $this->rol = $rol;
        return self::actualiza($this);
    }

    /* Guardo una entrada en colabora */
    public function guarda()
    {
        if($this->rol == self::PENDIENTE){
            return self::inserta($this);
        }
        else{
            return self::actualiza($this);
        }
        
    }
}
?>