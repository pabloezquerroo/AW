<?php
    namespace es\ucm\fdi\aw\blog;

    use es\ucm\fdi\aw\Aplicacion;

    class Post{
        
        /* ATRIBUTOS */
        private $ID; 
        private $descripcion;
        private $titular;
        private $IdProtectora;
        private $idUsuario;
        private $imagen;
        private $fecha;

        /* Constructora de la clase */
        private function __construct( $ID, $descripcion, $titular, $IdProtectora, $idUsuario, $fecha, $imagen =null){
            $this->ID = $ID;
            $this->descripcion = $descripcion;
            $this->titular = $titular;
            $this->IdProtectora = $IdProtectora;
            $this->idUsuario = $idUsuario;
            $this->imagen = $imagen;
            $this->fecha = $fecha;
        }

        /* Crea un post */
        public static function crea($descripcion, $titular, $IdProtectora, $idUsuario, $fecha, $ID = null) {    
            $post = new Post($ID, $descripcion, $titular, $IdProtectora, $idUsuario, $fecha);
            return $post->guarda();
        }

         /* Guardo o modifica un post */
        public function guarda(){
            if ($this->ID !== null) {
                return self::actualiza($this);
            }
            return self::inserta($this);
        }

        /*Modifico el post */
        public function modificaPost($titular, $descripcion) {
            $this->titular = $titular;
            $this->descripcion = $descripcion;
        }

        /* Inserta en la BD el post pasado por parametro */
        private static function inserta($post){
            $result = false;
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query=sprintf("INSERT INTO post(descripcion, titular, IdProtectora, fechaCreacion, idUsuario) VALUES ('%s', '%s', %d, '%s', %d)"
                , $conn->real_escape_string($post->descripcion)
                , $conn->real_escape_string($post->titular)
                , $post->IdProtectora
                , $post->fecha
                , $post->idUsuario
            );
            if ( $conn->query($query) ) {
                $post->ID = $conn->insert_id;
                $result = $post;
            } else {
                error_log("Error BD ({$conn->errno}): {Error al insertar un post nuevo en la BBDD}");
            }
            return $result;
        }

        /* Actualiza el post con los nuevos datos */
        private static function actualiza($post) {
            $result = $post;
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query=sprintf("UPDATE post SET IdProtectora= %d, descripcion='%s', titular='%s', idUsuario=%d, imagen='%s' WHERE ID=%d"
                , $post->IdProtectora
                , $conn->real_escape_string($post->descripcion)
                , $conn->real_escape_string($post->titular)
                , $post->idUsuario
                , $conn->real_escape_string($post->imagen)
                , $post->ID
            );
            if ( !$conn->query($query) ) {
                $result = null;
                error_log("Error BD ({$conn->errno}): {Error al actualizar un post en la BBDD}");
            }  
            return $result;
        }

        /* FUNCIONES DE BUSQUEDA */
        
        /* Busca un post por su ID */
        public static function buscaPorId($IdPost)
        {
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("SELECT * FROM post WHERE ID=%d", $IdPost);
            $rs = $conn->query($query);
            $result = null;
            if ($rs) {
                $fila = $rs->fetch_assoc();
                if ($fila) {
                    $result = new Post( $fila['ID'],  $fila['descripcion'], $fila['titular'], $fila['IdProtectora'], $fila['idUsuario'], $fila['fechaCreacion'], $fila['imagen']);
                }
                $rs->free();
            } else {
                error_log("Error BD ({$conn->errno}): {Error al buscar el post: $IdPost en la BBDD}");
            }
            return $result;
        }

        /* Devuelve ua lista con los post que ha realizado la protectora dada */
        public static function buscaPostPorProtectora($IdProtectora)
        {
            $conn = Aplicacion::getInstance()->getConexionBd();        
            $query = sprintf("SELECT * FROM post  WHERE IdProtectora=%d", $IdProtectora);    
            $rs = $conn->query($query);
            $result = null;
            $i = 0;
            if ($rs->num_rows > 0) {
                while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Post( $fila['ID'],  $fila['descripcion'], $fila['titular'], $fila['IdProtectora'], $fila['idUsuario'], $fila['fechaCreacion'], $fila['imagen']);
                $i = $i + 1;
                }  
                $rs->free();
            } 
            else {
                error_log("Error BD ({$conn->errno}): {Error al buscar los post de la protectora: $IdProtectora en la BBDD}");
            }
            return $result;
        }

        /* Devuelve ua lista con los post que ha realizado el usuario dado */
        public static function buscaPostPorUsuario($IdUsuario)
        {
            $conn = Aplicacion::getInstance()->getConexionBd();        
            $query = sprintf("SELECT * FROM post  WHERE idUsuario=%d", $IdUsuario);    
            $rs = $conn->query($query);
            $result = null;
            $i = 0;
            if ($rs->num_rows > 0) {
                while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Post( $fila['ID'],  $fila['descripcion'], $fila['titular'], $fila['IdProtectora'], $fila['idUsuario'], $fila['fechaCreacion'], $fila['imagen']);
                $i = $i + 1;
                }  
                $rs->free();
            } 
            else {
                error_log("Error BD ({$conn->errno}): {Error al buscar los post del usuario: $IdUsuario en la BBDD}");
            }
            return $result;
        }

        /* Devuelve una lista con los post que existen en la bbdd */
        public static function buscaPosts()
        {
            $conn = Aplicacion::getInstance()->getConexionBd();       
            $query = sprintf("SELECT * FROM post ORDER BY ID");    
            $rs = $conn->query($query);
            $result = null;
            $i = 0;
            if ($rs->num_rows > 0) {
                while($fila = $rs->fetch_assoc()) {
                $result[$i] = new Post( $fila['ID'],  $fila['descripcion'], $fila['titular'], $fila['IdProtectora'], $fila['idUsuario'], $fila['fechaCreacion'], $fila['imagen']);
                $i = $i + 1;
                }  
                $rs->free();
            } 
            else {
                error_log("Error BD ({$conn->errno}): {Error al buscar todos los post en la BBDD}");
            }
            return $result;
        }

        /* CONTEOS */

        /* cuenta el numero de Posts */
        public static function cuentaPosts(){
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("SELECT * FROM post");
            $rs = $conn->query($query);
            $conteo = $rs->num_rows;
            return $conteo;
        }

        /* GUETTERS */

        public function getId()
        {
            return $this->ID;
        }

        public function getFecha()
        {
            return $this->fecha;
        }
    
        public function getIdProtectora()
        {
            return $this->IdProtectora;
        }

        public function getTitular(){
            return $this->titular;
        }
    
        public function getDescripcion()
        {
            return $this->descripcion;
        }
    
        public function getIdUsuario(){
            return $this->idUsuario;
        }  

        public function getImagen(){
            return $this->imagen;
        }

        /* SETTER */ 
        
        public function setImagen($imagen){
            $this->imagen = $imagen;
        }

        /* Borra la protectora */
        public function borrate()
        {
            if ($this->ID !== null) {
                return self::borra($this);
            }
            return false;
        }

        /* borra el post pasado por parametro. */
        private static function borra($post)
        {
            return self::borraPorId($post->getId());
        }

        /* Borra el post que corresponde con el id pasado por parametro */
        private static function borraPorId($idPost)
        {
            if (!$idPost) {
                return false;
            } 
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("DELETE FROM post  WHERE ID = %d", $idPost);
            if ( ! $conn->query($query) ) {
                error_log("Error BD ({$conn->errno}): {Error al buscar el post: $idPost en la BBDD}");
                return false;
            }
            return true;
        }
    }
?>