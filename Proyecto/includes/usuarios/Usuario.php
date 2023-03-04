<?php
/* nose si esta bien */
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Aplicacion;
/* */

class Usuario
{

    public const ADMIN_ROLE = 1;
    public const USER_ROLE = 2;
    public const TIENE_TERRAZA = 1;
    public const NO_TIENE_TERRAZA = 0;

    private $id;
    private $email;
    private $password;
    private $nombre;
    private $rol;
    private $direccion;
    private $num_convivientes;
    private $tipo_vivienda;
    private $dedicacion;
    private $terraza;
    private $num_mascotas;
    private $telefono;
    private $m2_vivienda;
    private $imagen;

    private function __construct($email, $password, $nombre, $direccion, $num_convivientes, $tipo_vivienda, $dedicacion, $terraza, $num_mascotas, $telefono, $m2_vivienda, $id = null, $rol = self::USER_ROLE, $imagen = null)
    {
        $this->id = $id;
        $this->email= $email;
        $this->password = $password;
        $this->nombre = $nombre;
        $this->rol = $rol;
        $this->direccion = $direccion;
        $this->num_convivientes = $num_convivientes;
        $this->tipo_vivienda = $tipo_vivienda;
        $this->dedicacion = $dedicacion;
        $this->terraza = $terraza;
        $this->num_mascotas = $num_mascotas;
        $this->telefono = $telefono;
        $this->m2_vivienda = $m2_vivienda;
        $this->imagen = $imagen;
    }

    public static function login($email, $password)
    {
        $usuario = self::buscaUsuario($email);
        if ($usuario && $usuario->compruebaPassword($password)) {
            return $usuario;
        }
        return false;
    }
    
    public static function crea($email, $password, $nombre, $direccion, $num_convivientes, $tipo_vivienda, $dedicacion, $terraza, $num_mascotas, $telefono, $m2_vivienda, $id = null, $rol = self::USER_ROLE)
    {
        $user = new Usuario($email, self::hashPassword($password), $nombre, $direccion, $num_convivientes, $tipo_vivienda, $dedicacion, $terraza, $num_mascotas, $telefono, $m2_vivienda, $id, $rol);
        return $user->guarda();
    }

    public static function buscaUsuario($email)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM usuarios U WHERE U.email='%s'", $conn->real_escape_string($email));
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Usuario($fila['email'], $fila['password'], $fila['nombre'], $fila['direccion'], $fila['num_convivientes'], $fila['tipo_vivienda'], $fila['dedicacion'], $fila['terraza'], $fila['num_mascotas'], $fila['telefono'], $fila['m2_vivienda'], $fila['id'], $fila['rol'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function buscaPorId($idUsuario)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM usuarios WHERE id=%d", $idUsuario);
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Usuario($fila['email'], $fila['password'], $fila['nombre'], $fila['direccion'], $fila['num_convivientes'], $fila['tipo_vivienda'], $fila['dedicacion'], $fila['terraza'], $fila['num_mascotas'], $fila['telefono'], $fila['m2_vivienda'], $fila['id'], $fila['rol'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }
    
    private static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function compruebaPassword($password)
    {
        return password_verify($password, $this->password);
    }

    public function cambiaPassword($nuevoPassword)
    {
        $this->password = self::hashPassword($nuevoPassword);
    }
   
    private static function inserta($usuario)
    {
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO usuarios(email, nombre, password, rol,  direccion, num_convivientes, tipo_vivienda, dedicacion, terraza, num_mascotas, telefono, m2_vivienda) VALUES ('%s', '%s', '%s', '%d', '%s', '%d', '%s', '%s', '%d', '%d', '%d', '%d')"
            , $conn->real_escape_string($usuario->email)
            , $conn->real_escape_string($usuario->nombre)
            , $conn->real_escape_string($usuario->password)
            , $conn->real_escape_string($usuario->rol)
            , $conn->real_escape_string($usuario->direccion)
            , $conn->real_escape_string($usuario->num_convivientes)
            , $conn->real_escape_string($usuario->tipo_vivienda)
            , $conn->real_escape_string($usuario->dedicacion)
            , $conn->real_escape_string($usuario->terraza)
            , $conn->real_escape_string($usuario->num_mascotas)
            , $conn->real_escape_string($usuario->telefono)
            , $conn->real_escape_string($usuario->m2_vivienda)
        
        );
        if ( $conn->query($query) ) {
            $usuario->id = $conn->insert_id;
            $result = $usuario;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    
    private static function actualiza($usuario)
    {
        $result = $usuario;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE usuarios U SET email= '%s', nombre='%s', password='%s', direccion='%s', num_convivientes='%d', tipo_vivienda='%s', dedicacion='%s', terraza='%d', num_mascotas='%d', telefono='%d', m2_vivienda='%d', imagen='%s' WHERE U.id=%d"
            , $conn->real_escape_string($usuario->email)
            , $conn->real_escape_string($usuario->nombre)
            , $conn->real_escape_string($usuario->password)
            , $conn->real_escape_string($usuario->direccion)
            , $usuario->num_convivientes
            , $conn->real_escape_string($usuario->tipo_vivienda)
            , $conn->real_escape_string($usuario->dedicacion)
            , $usuario->terraza
            , $usuario->num_mascotas
            , $usuario->telefono
            , $usuario->m2_vivienda
            , $conn->real_escape_string($usuario->imagen)
            , $usuario->id
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }
    
    private static function borra($usuario)
    {
        return self::borraPorId($usuario->id);
    }
    
    private static function borraPorId($idUsuario)
    {
        if (!$idUsuario) {
            return false;
        } 
        /* Los roles se borran en cascada por la FK
         * $result = self::borraRoles($usuario) !== false;
         */
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("DELETE FROM usuarios WHERE id = %d", $idUsuario);
        if ( ! $conn->query($query) ) {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
        return true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getNumConvivientes()
    {
        return $this->num_convivientes;
    }

    public function getTipoVivienda()
    {
        return $this->tipo_vivienda;
    }

    public function getDedicacion()
    {
        return $this->dedicacion;
    }

    public function getTerraza()
    {
        return $this->terraza;
    }

    public function getNumMascotas()
    {
        return $this->num_mascotas;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getM2Vivienda()
    {
        return $this->m2_vivienda;
    }

    public function setImagen($imagen){
        $this->imagen = $imagen;
    }

    public function getImagen(){
        return $this->imagen;
    }

    public function isAdmin(){
        return $this->rol == self::ADMIN_ROLE;
    }

    public function modificaUsuario($email, $nombre, $direccion, $num_convivientes, $tipo_vivienda, $dedicacion, $terraza, $num_mascotas, $telefono, $m2_vivienda, $imagen = null) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->num_convivientes = $num_convivientes;
        $this->tipo_vivienda = $tipo_vivienda;
        $this->dedicacion = $dedicacion;
        $this->terraza = $terraza;
        $this->num_mascotas = $num_mascotas;
        $this->telefono = $telefono;
        $this->m2_vivienda = $m2_vivienda;
        if($imagen != null){
            $this->imagen = $imagen;
        }
       
    }
    
    public function guarda()
    {
        if ($this->id !== null) {
            return self::actualiza($this);
        }
        return self::inserta($this);
    }
    
    public function borrate()
    {
        if ($this->id !== null) {
            return self::borra($this);
        }
        return false;
    }
}
