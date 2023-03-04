<?php
namespace es\ucm\fdi\aw\animales;

use LDAP\Result;
use es\ucm\fdi\aw\Aplicacion;
/*Añadido nose si esta bien */
use es\ucm\fdi\aw\protectora\Colabora;
use es\ucm\fdi\aw\protectora\Protectora;
/**/

class Animal{

    private $ID;
    private $nombre;
    private $tipo;
    private $protectora;
    private $edad; 
    private $raza; 
    private $genero; //int:0-Masculino 1-Femenino
    private $peso;
    private $imagen; 

    
    /* Constructor de la clase */
    private function __construct($idAnimal = null, $nombreAnimal, $tipo, $protectora, $edad, $genero, $raza, $peso, $imagen = null) {
        //simplemente que envie a la base de datos
        $this->ID = $idAnimal;
        $this->nombre = $nombreAnimal;
        $this->tipo = $tipo;
        $this->protectora = $protectora;
        $this->edad = $edad;
        $this->raza = $raza;
        $this->genero = $genero;
        $this->peso = $peso;
        $this->imagen = $imagen;
    }

    //GUETTERS

    /* getter de Id */
    public function getId()
    {
        return $this->ID;
    }
    
    /* guetter de nombre */
    public function getNombre()
    {
        return $this->nombre;
    }

    /* guetter de tipo */
    public function getTipo()
    {
        return $this->tipo;
    }

    /* guetter protectora */
    public function getProtectora()
    {
        return $this->protectora;
    }

    /* guetter edad */
    public function getEdad()
    {
        return $this->edad;
    }

    /* guetter Raza */
    public function getRaza()
    {
        return $this->raza;
    }

    /* guetter Genero */
    public function getGenero()
    {
        return $this->genero;
    }

    /* guetter Peso */
    public function getPeso()
    {
        return $this->peso;
    }

    /* guetter Imagen */
    public function getImagen()
    {
        return $this->imagen;
    }

    /* setter Imagen */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }

    /* Crea un nuevo objeto animal */
    public static function crea($nombre, $tipo, $edad, $protectora, $genero, $raza, $peso)
    {
        $animal = new Animal($idAnimal = null,$nombre, $tipo, $protectora,$edad, $genero, $raza, $peso);
        return $animal->guarda();
    }

    /* modifica el objeto animal */
    public function modificaAnimal($nombre, $tipo, $edad, $genero, $raza, $peso, $imagen){//modificar imagen
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->edad = $edad;
        $this->genero = $genero;
        $this->raza = $raza;
        $this->peso = $peso;
        $this->imagen = $imagen;
    }

    /* guarda un animal */
    public function guarda()
    {
        if ($this->ID !== null) {
            return self::actualiza($this);
        }
        return self::inserta($this);
    }

    /* actualiza un animal */
    private static function actualiza($animal)
    {
        $result = $animal;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE animales A SET nombre='%s', tipo='%s', edad='%d', genero='%s', raza='%s', peso='%d', imagen='%s' WHERE A.ID=%d"
            , $conn->real_escape_string($animal->nombre)
            , $conn->real_escape_string($animal->tipo)
            , $animal->edad
            , $conn->real_escape_string($animal->genero)
            , $conn->real_escape_string($animal->raza)
            , $animal->peso
            , $conn->real_escape_string($animal->imagen)
            , $animal->ID
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }

    /* modifica la pertenencia de una protectora de un animal adoptado */
    public static function modificaAadoptado($IDAnimal)
    {
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE animales SET protectora=NULL WHERE ID=%d"
            , $IDAnimal
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }

    /* inserta un animal dado */
    private static function inserta($animal){
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO animales(nombre, tipo, protectora, edad, genero, raza, peso, imagen) VALUES ('%s', '%s', '%d', '%d', '%s', '%s', '%d', '%s')"
            , $conn->real_escape_string($animal->nombre)
            , $conn->real_escape_string($animal->tipo)
            , $animal->protectora
            , $animal->edad
            , $conn->real_escape_string($animal->genero)
            , $conn->real_escape_string($animal->raza)
            , $animal->peso
            , $conn->real_escape_string($animal->imagen)
        );
        if ( $conn->query($query) ) {
            $animal->ID=$conn->insert_id;
            $result = $animal;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* borra un animal dado */
    private static function borra($animal)
    {
        return self::borraPorId($animal->ID);
    }

    /* borra un animal por su id */
    private static function borraPorId($idAnimal)
    {
        if (!$idAnimal) {
            return false;
        } 
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("DELETE FROM animales WHERE ID = %d", $idAnimal);
        if ( ! $conn->query($query) ) {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
        return true;
    }

    /* mueve un animal de una protectora a otra */
    public static function trasladoAnimal($idAnimal, $idProtectora){
        $result = true;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE animales SET protectora=%d WHERE ID=%d"
            , $idProtectora,$idAnimal
        );
        if ( !$conn->query($query) ) {
            $result = false;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        
        return $result;
    }

    /*busca animales de una protectora dada */
    public static function buscaPorProtectora($idProtectora, $offset = null, $limit = null){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.protectora=%d ORDER BY A.ID LIMIT $offset, $limit", $idProtectora );
        $rs = $conn->query($query);
        $result = false;
        $i=0;
        if ($rs) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /*busca animales de una protectora dada */
    public static function buscaPorProtectoraSinLimit($idProtectora){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.protectora=%d ORDER BY A.ID", $idProtectora );
        $rs = $conn->query($query);
        $result = false;
        $i=0;
        if ($rs) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /*borra un animal */
    public function borrate()
    {
        if ($this->ID !== null) {
            return self::borra($this);
        }
        return false;
    }

    /* lista de animales adoptables */
    public static function animalesAdoptables($filtro, $offset, $limit){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE %s A.protectora is not null ORDER BY A.ID LIMIT $offset, $limit", $filtro);
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }
    
    /* conteo animales adoptables */
    public static function cuentaAnimalesAdoptables($filtro){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE %s A.protectora is not null", $filtro);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo;
    }

    /* cuenta el numero de animales de una protectora dada */
    public static function cuentaAnimalesPorProtectora($idProtectora){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.protectora=%d ", $idProtectora);
        $rs = $conn->query($query);
        $conteo = $rs->num_rows;
        return $conteo; 
    }

    /* lista de mis animales */
    public static function misAnimales($idUsuario){
        $conn = Aplicacion::getInstance()->getConexionBd(); 
        $query = sprintf("SELECT * FROM animales A JOIN colabora C ON A.protectora=C.IDprotectora WHERE C.IDusuario=%d AND (C.rol=%d OR C.rol=%d)",
        $idUsuario, Colabora::CREADOR, Colabora::COLABORA );
        $rs = $conn->query($query);
        $result = false;
        $i = 0;
        if ($rs->num_rows > 0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } 
        else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* busca un animal por su id */
    public static function buscaPorID($IDanimal) {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.ID=%d", $IDanimal);
        $rs = $conn->query($query);
        $result = false;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* busca un animal por su raza */
    public static function buscaPorRaza($raza){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.raza=%s",  $conn->real_escape_string($raza));
        $rs = $conn->query($query);
        $result = false;
        $i=0;
        if ($rs) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* busca animal por tipo */
    public static function buscaPorTipo($tipo){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.tipo=%s",  $conn->real_escape_string($tipo));
        $rs = $conn->query($query);
        $result = false;
        $i=0;
        if ($rs) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* busca animal por edad */
    public static function buscaPorEdad($edad){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.edad=%d",  $edad);
        $rs = $conn->query($query);
        $result = false;
        $i=0;
        if ($rs) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'],$fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Busca animal por peso */
    public static function buscaPorPeso($peso){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.peso=%s",  $peso);
        $rs = $conn->query($query);
        $result = false;
        $i=0;
        if ($rs) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Busca animal por genero */
    public static function buscaPorGenero($genero){
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM animales A WHERE A.genero=%d", $genero );
        $rs = $conn->query($query);
        $result = false;
        $i=0;
        if ($rs) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Animal($fila['ID'], $fila['nombre'], $fila['tipo'],
                $fila['protectora'], $fila['edad'], $fila['genero'],
                $fila['raza'],  $fila['peso'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Guetter de tipos */
    public static function getTipos(){
        $result = null;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT DISTINCT A.tipo FROM animales A ");
        $rs = $conn->query($query);
        if ($rs) {
            $i = 0;
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = $fila['tipo'];
                $i += 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Guetter de razas */
    public static function getRazas(){
        $result = null;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT DISTINCT A.raza FROM animales A ");
        $rs = $conn->query($query);
        if ($rs) {
            $i = 0;
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = $fila['raza'];
                $i += 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }
    
    /* Guetter de filtros */
    public static function getFiltros(){
        $result = null;
        $result['tipo'] = self::getTipos();
        $result['raza'] = self::getRaza();
        $result['protectoras'] = Protectora::buscaPorEstado(Protectora::ACTIVA);
        return $result;
    }
}

?>