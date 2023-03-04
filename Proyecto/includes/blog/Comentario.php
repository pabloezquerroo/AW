<?php

    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\Aplicacion;

    class Comentario{  

        /* ATRIBUTOS */
        private $id;
        private $idUsuario;
        private $idPost;
        private $descripcion;
        private $fecha;

        /* Constructora de la clase */
        private function __construct( $descripcion, $idUsuario, $idPost, $fecha, $id){
            $this->idUsuario = $idUsuario;
            $this->descripcion = $descripcion;
            $this->idPost = $idPost;
            $this->id = $id;
            $this->fecha = $fecha;
        }

        /* Crea un comentario */
        public static function crea($descripcion, $idUsuario, $idPost, $fecha, $id = null) {
            $comentario = new Comentario($descripcion, $idUsuario, $idPost, $fecha, $id);
            return $comentario->guarda();
        }

        /* Guardo un comentario */
        public function guarda(){
            if ($this->id == null) {
                return self::inserta($this);
            }
            return self::actualiza($this); 
        }

        /*Modifico el comentario */
        public function modificaComentario($descripcion) {
            $this->descripcion = $descripcion;
        }

        /* Inserta en la BD el comentario pasado por parametro */
        private static function inserta($comentario) {
            $result = false;
            $conn = Aplicacion::getInstance()->getConexionBd();

            $query=sprintf("INSERT INTO comentarios(idUsuario, descripcion, idPost, fechaCrea) VALUES (%d, '%s', %d, '%s')"
                , $comentario->idUsuario
                , $conn->real_escape_string($comentario->descripcion)
                , $comentario->idPost
                , $conn->real_escape_string($comentario->fecha));
            if ( $conn->query($query) ) {
                $comentario->id = $conn->insert_id;
                $result = $comentario;
            } else {
                error_log("Error BD ({$conn->errno}): {Error al insertar el comentario en la BBDD}");
            }
            return $result;
        }

        /* Actualiza el comentario con los nuevos datos */
        private static function actualiza($comentario) {
            $result = $comentario;
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query=sprintf("UPDATE comentarios SET descripcion='%s' WHERE id=%d"
                , $conn->real_escape_string($comentario->descripcion)
                , $comentario->id
            );
            if ( !$conn->query($query) ) {
                $result = null;
                error_log("Error BD ({$conn->errno}): {Error al actualizar un comentario en la BBDD}");
            }  
            return $result;
        }

        /* BUSQUEDAS */

        /* Busca los comentarios de un post dado. */
        public static function BuscaComentariosPost($IdPost)
        {
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("SELECT * FROM comentarios WHERE idPost=%d ORDER BY id", $IdPost);
            $rs = $conn->query($query);
            $result = null;
            $i = 0;
            if ($rs->num_rows>0) {
                while($fila = $rs->fetch_assoc()) {
                    $result[$i] = new Comentario( $fila['descripcion'], $fila['idUsuario'], $fila['idPost'], $fila['fechaCrea'], $fila['id']);
                    $i = $i + 1;
                }
                $rs->free();
            } else {
                error_log("Error BD ({$conn->errno}): {Error al buscar los comentarios de un post en la BBDD}");
            }
            return $result;
        }

        /* Busca un comentario por su Id */
        public static function BuscaPorId($idComentario)
        {
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("SELECT * FROM comentarios WHERE id=%d", $idComentario);
            $rs = $conn->query($query);
            $result = null;
            if ($rs) {
                $fila = $rs->fetch_assoc();
                if ($fila) {
                    $result = new Comentario( $fila['descripcion'], $fila['idUsuario'], $fila['idPost'], $fila['fechaCrea'], $fila['id']);
                }
                $rs->free();
            } else {
                error_log("Error BD ({$conn->errno}): {Error en la busqueda del comentario: $idComentario en la BBDD}");
            }
            return $result;
        }

        /* GUETTERS */

        public function getId()
        {
            return $this->id;
        }
    
        public function getIdUsuario(){
            return $this->idUsuario;
        }

        public function getIdPost(){
            return $this->idPost;
        }

        public function getDescripcion(){
            return $this->descripcion;
        }

        public function getFecha(){
            return $this->fecha;
        }
        
        /* borra el comentario pasado por parametro. */
        private static function borra($comentario)
        {
            return self::borraPorId($comentario->id);
        }

        /* Borra una entrada en la tabla comentarios */
        private static function borraPorId($idComentario)
        {
            if (!$idComentario) {
                return false;
            } 
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("DELETE FROM comentarios  WHERE id = %d", $idComentario);
            if ( ! $conn->query($query) ) {
                error_log("Error BD ({$conn->errno}): {Error en el borrado del comentario en la BBDD}");
                return false;
            }
            return true;
        }

        public function borrate()
        {
            if ($this->id !== null) {
                return self::borra($this);
            }
            return false;
        }
    }
?>