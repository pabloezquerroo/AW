<?php
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Aplicacion;
use es\ucm\fdi\aw\protectora\Colabora;
use es\ucm\fdi\aw\config;

class Protectora{

    /* CONSTANTES PARA INDICAR EL ESTADO DE LA PROTECTORA */
    public const PENDIENTE = 0;
    public const ACTIVA = 1;

    /* ATRIBUTOS DE UNA PROTECTORA */
    private $ID= NULL;  
    private $nombre;    
    private $estado; 
    private $telefono;
    private $email;
    private $direccion;
    private $descripcion;
    private $imagen;

    /* Constructora de la clase */
    private function __construct($nombre, $estado, $telefono, $email, $direccion, $descripcion, $imagen = null, $ID = null){
        $this->ID = $ID;
        $this->nombre = $nombre;
        $this->estado = $estado;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->direccion = $direccion;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
    }

    /*  FUNCIONES DE ACTUALIZACION */

    /* Actualiza el usuario con los nuevos datos */
    private static function actualiza($protectora)
    {
        $result = $protectora;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE protectora  SET nombre= '%s', estado='%s', telefono=%d, email='%s',
                        direccion='%s', descripcion='%s', imagen='%s' WHERE id=%d"
            , $conn->real_escape_string($protectora->nombre)
            , $conn->real_escape_string($protectora->estado)
            , $protectora->telefono
            , $conn->real_escape_string($protectora->email)
            , $conn->real_escape_string($protectora->direccion)
            , $conn->real_escape_string($protectora->descripcion)
            , $conn->real_escape_string($protectora->imagen)
            , $protectora->ID
        );
        if ( !$conn->query($query) ) {
            $result = null;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }

    /* CONTEO */

    /* cuenta el numero de Protectoras activas */
    public static function cuentaEstadoActivo(){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM protectora WHERE estado=%d", self::ACTIVA);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /* cuenta cuenta las protectoras inactivas */
    public static function cuentaEstadoInactivo(){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM protectora WHERE estado=%d", self::PENDIENTE);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /* cuenta las protectoras del usuario loggeado */
    public static function cuentaMisProtectoras(){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM protectora P JOIN colabora C ON P.ID=C.iDProtectora WHERE P.estado='%s' AND C.iDUsuario=%d AND (C.rol=%d OR C.rol=%d)", self::ACTIVA, $_SESSION['id'], Colabora::CREADOR, Colabora::COLABORA);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /*  FUNCIONES DE BUSQUEDA */

    /* Busca una protectora por su ID */
    public static function buscaPorId($idProtectora)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM protectora WHERE ID=%d", $idProtectora);
        $rs = $conn->query($query);
        $result = null;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Protectora($fila['nombre'],  $fila['estado'], $fila['telefono'], 
                            $fila['email'], $fila['direccion'], $fila['descripcion'], $fila['imagen'], 
                            $fila['ID']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Busca una protectora por su ID */
    public static function buscaPorEmail($email)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM protectora WHERE email='%s'", $email);
        $rs = $conn->query($query);
        $result = null;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Protectora($fila['nombre'],  $fila['estado'], $fila['telefono'], 
                            $fila['email'], $fila['direccion'], $fila['descripcion'], $fila['imagen'], 
                            $fila['ID']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Busca una protectora por su estado */
    public static function buscaPorEstado($estadoProtectora, $offset = NULL, $limit = NULL)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        
        
        if($offset == null && $limit == null){
            $query = sprintf("SELECT * FROM protectora WHERE estado='%s'", $estadoProtectora);
        }
        else{
            $query = sprintf("SELECT * FROM protectora WHERE estado='%s' ORDER BY ID LIMIT $offset, $limit", $estadoProtectora);    
        }
        $rs = $conn->query($query);
        $result = null;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
               $result[$i] = new Protectora($fila['nombre'],  $fila['estado'], $fila['telefono'], 
                            $fila['email'], $fila['direccion'], $fila['descripcion'], $fila['imagen'], 
                            $fila['ID']);
               $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Devuelve ua lista con las protectoras a las que se pertenece con pàginación */
    public static function buscaMisProtectoras($estadoProtectora, $idUsuario ,$offset = NULL, $limit = NULL)
    {
        $conn = Aplicacion::getInstance()->getConexionBd(); 
        if($offset === NULL || $limit === NULL){
            $query = sprintf("SELECT * FROM protectora P JOIN colabora C ON P.ID=C.iDProtectora WHERE P.estado='%s' AND C.iDUsuario=%d AND (C.rol=%d OR C.rol=%d) ORDER BY ID /*LIMIT $offset, $limit*/", $estadoProtectora, $idUsuario, Colabora::COLABORA, COLABORA::CREADOR);    
        }   
        else{
            $query = sprintf("SELECT * FROM protectora P JOIN colabora C ON P.ID=C.iDProtectora WHERE P.estado='%s' AND C.iDUsuario=%d AND (C.rol=%d OR C.rol=%d) ORDER BY ID LIMIT $offset, $limit", $estadoProtectora, $idUsuario, Colabora::COLABORA, COLABORA::CREADOR);    
        }   
        $rs = $conn->query($query);
        $result = null;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
               $result[$i] = new Protectora($fila['nombre'],  $fila['estado'], $fila['telefono'], 
                            $fila['email'], $fila['direccion'], $fila['descripcion'], $fila['imagen'], 
                            $fila['ID']);
               $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Devuelve ua lista con las protectoras a las que se pertenece */
    public static function buscaMisProtectorasSinLimit($estadoProtectora, $idUsuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();        
        $query = sprintf("SELECT * FROM protectora P JOIN colabora C ON P.ID=C.iDProtectora WHERE P.estado='%s' AND C.iDUsuario=%d AND (C.rol=%d OR C.rol=%d) ORDER BY ID", $estadoProtectora, $idUsuario, Colabora::COLABORA, COLABORA::CREADOR);    
        $rs = $conn->query($query);
        $result = null;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
               $result[$i] = new Protectora($fila['nombre'],  $fila['estado'], $fila['telefono'], 
                            $fila['email'], $fila['direccion'], $fila['descripcion'], $fila['imagen'], 
                            $fila['ID']);
               $i = $i + 1;
            }  
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Devuelve si una protectora está entre mis protectoas */
    public static function perteneceMisProtectoras($estadoProtectora, $idUsuario, $idProtectora)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();        
        $query = sprintf("SELECT * FROM protectora WHERE ID=%d IN (SELECT P.ID FROM protectora P JOIN colabora C ON P.ID=C.iDProtectora WHERE P.estado='%s' AND C.iDUsuario=%d AND (C.rol=%d OR C.rol=%d))", $idProtectora, $estadoProtectora, $idUsuario, Colabora::COLABORA, COLABORA::CREADOR);    
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

    /* Busca una protectora por su nombre */
    public static function buscaPornombre($nombreProtectora)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM protectora P WHERE P.nombre='%s'", $conn->real_escape_string($nombreProtectora));
        $rs = $conn->query($query);
        $result = null; 
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Protectora($fila['nombre'],  $fila['estado'], $fila['telefono'], 
                            $fila['email'], $fila['direccion'], $fila['descripcion'], $fila['imagen'], 
                            $fila['ID']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Comprueba si la protectora esta activa */
    public function isActiva(){
        return $this->estado == $this::ACTIVA;
    }

    /*  FUNCIONES DE BORRADO */

    /* borra la protectora pasada por parametro. */
    private static function borra($protectora)
    {
        return self::borraPorId($protectora->ID);
    }

    /* Borra la protectora que corresponde con el id pasado por parametro */
    private static function borraPorId($idProtectora)
    {
        if (!$idProtectora) {
            return false;
        } 
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("DELETE FROM protectora  WHERE ID = %d", $idProtectora);
        if ( ! $conn->query($query) ) {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
        return true;
    }
    
    /* Borra la protectora */
    public function borrate()
    {
        if ($this->ID !== null) {
            return self::borra($this);
        }
        return false;
    }

    /*  FUNCIONES DE INSERCION.*/

    /* Inserta en la BD la protectora pasada por parametro */
    private static function inserta($protectora)
    {
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO protectora(nombre, estado, telefono, email, direccion, descripcion) VALUES ('%s', '%s', '%d', '%s', '%s', '%s')"
            , $conn->real_escape_string($protectora->nombre)
            , $conn->real_escape_string($protectora->estado)
            , $protectora->telefono
            , $conn->real_escape_string($protectora->email)
            , $conn->real_escape_string($protectora->direccion)
            , $conn->real_escape_string($protectora->descripcion)
        );
        if ( $conn->query($query) ) {
            $protectora->ID = $conn->insert_id;
            $result = $protectora;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* GUETTERS */

    public function getId()
    {
        return $this->ID;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getTelefono(){
        return $this->telefono;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getDireccion(){
        return $this->direccion;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }

    public function getImagen(){
        return $this->imagen;
    }

    /* FUNCIONES DE CREAR , GUARDAR Y MODIFICAR */

    /* Crea una protectora */
    public static function crea($nombre, $telefono, $email, $direccion, $descripcion, $imagen = null, $id = null)
    {
        if($id !== null){
            $estado = self::ACTIVA;
        }
        else{
            $estado = self::PENDIENTE;
        }
        
        $protectora = new Protectora($nombre, $estado, $telefono, $email, $direccion, $descripcion, $imagen, $id);
        return $protectora->guarda();
    }

    /*Modifico los atributos de la protectora */
    public function modificaProtectora($nombre, $telefono, $email, $direccion, $descripcion, $imagen) {
        $this->nombre = $nombre;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->direccion = $direccion;
        $this->descripcion = $descripcion;
        $this->imagen = $imagen;
    }

    /* Guardo o modifica una protectora */
    public function guarda()
    {
        if ($this->ID !== null) {
            return self::actualiza($this);
        }
        return self::inserta($this);
    }

    /* SETTER  */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }
}
?>