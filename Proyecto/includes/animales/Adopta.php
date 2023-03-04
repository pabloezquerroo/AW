<?php
namespace es\ucm\fdi\aw\animales;

/*Añadido nose si esta bien */
use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\usuarios\Usuario;
/**/

class Adopta {

    public const PENDIENTE = 1;
    public const ADOPTADO = 2;

    private $iDAnimal;
    private $iDUsuario;
    private $estado;

    /* Constructor de Adopta */
    private function __construct($iDAnimal, $iDUsuario, $estado = self::PENDIENTE)
    {
        $this->iDAnimal = $iDAnimal;
        $this->iDUsuario = $iDUsuario;
        $this->estado = $estado;
    }

    /* Actualiza la entrada en adopta con los nuevos datos */
    private static function actualiza($adopta)
    {
        $result = $adopta;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE adopta  SET estado=%d WHERE id_animal=%d AND id_usuario=%d"
            , $adopta->estado
            , $adopta->iDAnimal
            , $adopta->iDUsuario
        );
        if ( !$conn->query($query) ) {
            $result = null;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }

    /* Actualiza la entrada en adopta con los nuevos datos */
    public static function modificaEstado($idAnimal, $idUsuario, $estado)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE adopta  SET estado=%d WHERE id_animal=%d AND id_usuario=%d"
            , $estado
            , $idAnimal
            , $idUsuario
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }

    public static function cuentaSolicitudesAdopcion($idAnimal){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM adopta WHERE id_animal=%d AND estado=%d", $idAnimal, self::PENDIENTE);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /*  FUNCIONES DE BUSQUEDA */

    /* busca los animales adoptados por un usuario dado*/
    public static function buscaAnimalesAdoptados($idUsuario, $offset, $limit){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM adopta WHERE id_usuario=%d AND estado=%d ORDER BY id_animal LIMIT $offset, $limit", $idUsuario, self::ADOPTADO);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = Animal::buscaPorID($fila['id_animal']);
                $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* indica si el animal dado es adoptado por un usuario dado*/
    public static function esAnimalAdoptadoUsuario($idUsuario, $idAnimal){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM adopta WHERE id_usuario=%d AND id_animal=%d AND estado=%d", $idUsuario, $idAnimal, self::ADOPTADO);
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

    /* cuenta los animales adoptados por un usuario dado*/
    public static function cuentaAnimalesAdoptados($idUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM adopta WHERE id_usuario=%d AND estado=%d", $idUsuario, self::ADOPTADO);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /* busca los usuarios con proceso de adopcion de un animal pendiente */
    public static function buscaUsuarioPendiente($iDAnimal){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM adopta WHERE id_animal=%d AND estado=%d", $iDAnimal, self::PENDIENTE);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = Usuario::buscaPorId($fila['id_usuario']);
                $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function buscaUsuarioPendienteConLimite($idAnimal, $offset, $limit){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM adopta WHERE id_animal=%d AND estado=%d ORDER BY id_animal LIMIT $offset, $limit", $idAnimal, self::PENDIENTE);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = Usuario::buscaPorId($fila['id_usuario']);
                $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Busca si un usuario ha iniciado un proceso de adopcion con un animal dado */
    public static function existeProcesoAdopcion($iDUsuario, $iDAnimal) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM adopta  WHERE id_animal=%d AND id_usuario = %d", $iDAnimal, $iDUsuario);
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
    /* Actualiza la entrada en adopta con los nuevos datos */
    public static function eliminaProcesoAdopcion($idAnimal, $idUsuario)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("DELETE FROM adopta WHERE id_animal=%d AND id_usuario=%d"
            , $idAnimal
            , $idUsuario
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }        
        return $result;
    }

        /* Actualiza la entrada en adopta con los nuevos datos */
    public static function eliminaAdopcionesPendientes($idAnimal)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("DELETE FROM adopta WHERE id_animal=%d AND estado=%d"
            , $idAnimal
            , self::PENDIENTE
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* FUNCIONES DE INSERCION */

    /* inserta una nueva entrada colabora en la BD */
    private static function inserta($adopta)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO adopta(id_animal, id_usuario, estado) VALUES (%d, %d, %d)"
            , $adopta->iDAnimal
            , $adopta->iDUsuario
            , $adopta->estado
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

     /* GUETTERS */

     public function getIdAnimal()
     {
        return $this->iDAnimal;
     }
 
     public function getIdUsuario()
     {
        return $this->iDUsuario;
     }
 
     public function getEstado()
     {
        return $this->estado;
     }

     /* FUNCIONES DE CREAR Y GUARDAR */

    /* Crea una entrada en colabora */
    public static function crea($iDAnimal, $iDUsuario)
    {
        $adopta = ' ';
        if(! (self::existeProcesoAdopcion($iDUsuario, $iDAnimal))){
            $adopta = new Adopta($iDAnimal, $iDUsuario);
        }
        else{
           $adopta = new Adopta($iDAnimal, $iDUsuario, self::ADOPTADO);
        }

        
        return $adopta->guarda();
    }

    /* Guardo una entrada en colabora */
    public function guarda()
    {
        if($this->estado == self::PENDIENTE){
            return self::inserta($this);
        }
        else{
            return self::actualiza($this);
        }
        
    }

}
?>