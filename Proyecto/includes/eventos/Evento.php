<?php

namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Aplicacion;
use \DateTime;

class Evento{

    /* CONSTANTES PARA INDICAR EL TIPO DEL EVENTO */
    const FORMAT_MYSQL = 'Y-m-d H:i:s';
    public const CAMINATA = 0;
    public const MERCADILLO = 1;

    /* ATRIBUTOS */
    private $ID;
    private $titulo;
    private $fechaInicio;
    private $fechaFin;
    private $descripcion;
    private $tipo;
    private $imagen;  

    /* Constructora de la clase */
    private function __construct( $ID = null, $titulo, $fechaInicio, $fechaFin, $descripcion, $tipo, $imagen=null){
        $this->ID = $ID;
        $this->titulo = $titulo;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->descripcion = $descripcion;
        $this->tipo = $tipo;
        $this->imagen = $imagen;
    }

    /* Crea un evento */
    public static function crea($titulo, $fechaInicio, $fechaFin, $descripcion, $tipo, $ID = null, $imagen=null)
    {
         
         $evento = new Evento($ID, $titulo, $fechaInicio, $fechaFin, $descripcion, $tipo, $imagen);
         return $evento->guarda();
    }

    /* Guardo o modifica un evento */
    public function guarda()
    {
        if ($this->ID !== null) {
            return self::actualiza($this);
        }
        return self::inserta($this);
    }

    /* Inserta en la BD el evento pasado por parametro */
    private static function inserta($evento)
    {
        $result = false;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("INSERT INTO eventos(title, start, end, descripcion, tipo) VALUES ('%s', '%s', '%s', '%s', '%d')"
            , $conn->real_escape_string($evento->titulo)
            , $evento->fechaInicio->format(self::FORMAT_MYSQL)
            , $evento->fechaFin->format(self::FORMAT_MYSQL)
            , $conn->real_escape_string($evento->descripcion)
            , $evento->tipo
        );
        if ( $conn->query($query) ) {
            $evento->ID = $conn->insert_id;
            $result = $evento;
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public function modifica($titulo, $fechaInicio, $fechaFin, $descripcion, $tipo, $imagen)
    {
        $this->titulo = $titulo;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->descripcion = $descripcion;
        $this->tipo = $tipo;
        $this->imagen = $imagen;
    }

    /* Actualiza el evento con los nuevos datos */
    private static function actualiza($evento)
    {       
        $result = $evento;
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query=sprintf("UPDATE eventos SET title='%s', start='%s', end='%s', descripcion='%s', tipo='%d',
                       imagen='%s' WHERE id=%d"
            , $conn->real_escape_string($evento->titulo)
            , $evento->fechaInicio->format(self::FORMAT_MYSQL)
            , $evento->fechaFin->format(self::FORMAT_MYSQL)
            , $conn->real_escape_string($evento->descripcion)
            , $evento->tipo
            , $conn->real_escape_string($evento->imagen)
            , $evento->ID

        );
        if ( !$conn->query($query) ) {
            $result = null;
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }  
        return $result;
    }

    public function borrate()
    {
        if ($this->ID !== null) {
            return self::borra($this);
        }
        return false;
    }

    private static function borra($evento)
    {
        return self::borraPorId($evento->ID);
    }

    private static function borraPorId($idEvento)
    {
        if (!$idEvento) {
            return false;
        } 
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("DELETE FROM eventos WHERE id = %d", $idEvento);
        if ( ! $conn->query($query) ) {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
            return false;
        }
        return true;
    }
    /* FUNCIONES DE BUSQUEDA */

    /* Busca un evento por su ID */
    public static function buscaPorId($IdEvento)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE id=%d", $IdEvento);
        $rs = $conn->query($query);
        $result = null;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Evento( $fila['id'], $fila['title'], $fila['start'], $fila['end'], $fila['descripcion'], 
                            $fila['tipo'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function buscaPorTitulo($titulo)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE title='%s'", $titulo);
        $rs = $conn->query($query);
        $result = null;
        if ($rs) {
            $fila = $rs->fetch_assoc();
            if ($fila) {
                $result = new Evento( $fila['id'], $fila['title'], $fila['start'], $fila['end'], $fila['descripcion'], 
                            $fila['tipo'], $fila['imagen']);
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    /* Busca eventos por tipo */
    public static function buscaEventosPorTipo($tipo)
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos WHERE tipo=%d", $tipo);
        $rs = $conn->query($query);
        $result = null;
        $i = 0;
        if ($rs->num_rows>0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Evento( $fila['id'], $fila['title'], $fila['start'], $fila['end'], $fila['descripcion'], 
                $fila['tipo'], $fila['imagen']);
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }

    public static function buscaEventos()
    {
        $conn = Aplicacion::getInstance()->getConexionBd();
        $query = sprintf("SELECT * FROM eventos");
        $rs = $conn->query($query);
        $result = null;
        $i = 0;
        if ($rs->num_rows>0) {
            while($fila = $rs->fetch_assoc()) {
                $result[$i] = $fila;
                $i = $i + 1;
            }
            $rs->free();
        } else {
            error_log("Error BD ({$conn->errno}): {$conn->error}");
        }
        return $result;
    }
    
    /* GUETTERS */
    
    public function getTitulo()
    {
        return $this->titulo;
    }

    public function getFechaIni()
    {
        return $this->fechaInicio;
    }

    public function getFechaFin()
    {
        return $this->fechaFin;
    }

     public function getId()
     {
         return $this->ID;
     }
 
     public function getTipo()
     {
         return $this->tipo;
     }
 
     public function getDescripcion(){
         return $this->descripcion;
     }
     
     public function getImagen(){
         return $this->imagen;
     }

    /* SETTER  */
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;
    }


}

?>