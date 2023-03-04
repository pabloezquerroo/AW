<?php

    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\Aplicacion;

    class Like{ 

        /* ATRIBUTOS */
        private $idPost;
        private $idUsuario;

        /* Constructora de la clase */
        private function __construct( $idPost, $idUsuario){
            $this->idPost = $idPost;
            $this->idUsuario = $idUsuario;
        }

        /* Crea un like */
        public static function crea($idPost, $idUsuario) {
            $postFavorito = new Like($idPost, $idUsuario);
            return $postFavorito->guarda();
        }

        /* Guardo o modifica un like */
        public function guarda(){
            return self::inserta($this);
        }

        /* Inserta en la BD el like pasado por parametro */
        private static function inserta($like) {
            $result = false;
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query=sprintf("INSERT INTO favoritos(idPost, idUsuario) VALUES (%d, %d)"
                , $like->idPost
                , $like->idUsuario);

            if ( $conn->query($query) ) {
                $result = $like;
            } else {
                error_log("Error BD ({$conn->errno}): {Error en la insercion en la BBDD de un like}");
            }
            return $result;
        }

        /* BUSQUEDAS */

        /* Busca el numero de likes de un post dado. */
        public static function buscalikesPost($IdPost)
        {
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("SELECT * FROM favoritos WHERE idPost=%d", $IdPost);
            $rs = $conn->query($query);
            $result = null;
            $i = 0;
            if ($rs->num_rows>0) {
                while($fila = $rs->fetch_assoc()) {
                    $result[$i] = new Like( $fila['idPost'], $fila['idUsuario']);
                    $i = $i + 1;
                }
                $rs->free();
            } else {
                error_log("Error BD ({$conn->errno}): {Error en la busqueda de likes del post: $IdPost}");
            }
            return $result;
        }

        /* Devuelve una lista con los likes del usuario dado */
        public static function buscaPostGustadosPorUsuario($idUsuario, $limit = null, $offset = null)
        {
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("SELECT * FROM favoritos WHERE idUsuario=%d LIMIT $offset, $limit", $idUsuario);
            $rs = $conn->query($query);
            $result = null;
            $i = 0;
            if ($rs->num_rows>0) {
                while($fila = $rs->fetch_assoc()) {
                    $result[$i] = new Like( $fila['idPost'], $fila['idUsuario']);
                    $i = $i + 1;
                }
                $rs->free();
            }
            return $result;
        }

        /* Devuelve el like si un usuario dado ha dado like a un post dado */
        public static function UserLikePost($idUsuario, $idPost)
        {
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("SELECT * FROM favoritos WHERE idUsuario=%d AND idPost=%d", $idUsuario, $idPost);
            $rs = $conn->query($query);
            $result = false;
            if ($rs) {
                $fila = $rs->fetch_assoc();
                if ($fila) {
                    $result = new Like( $fila['idPost'], $fila['idUsuario']);
                }
                $rs->free();
            }
             return $result;
        }

        /* CONTEOS */

        /* cuenta el numero de Publicaciones Gustadas Por un usuario */
        public static function cuentaPostGustadosPorUsuario($idUsuario){
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("SELECT * FROM favoritos WHERE idUsuario=%d", $idUsuario);
            $rs = $conn->query($query);
            $conteo = $rs->num_rows;
            return $conteo;
        }

        /* GUETTERS */

        public function getIdPost()
        {
            return $this->idPost;
        }
    
        public function getIdUsuario(){
            return $this->idUsuario;
        }
        
        /* borra el like pasada por parametro. */
        public static function borra($like)
        {
            return self::borraPorId($like->getIdPost(), $like->getIdUsuario());
        }

        /* Borra un like de la tabla favoritos */
        private static function borraPorId($idPost, $idUsuario)
        {
            if (!$idPost || !$idUsuario) {
                return false;
            } 
            $conn = Aplicacion::getInstance()->getConexionBd();
            $query = sprintf("DELETE FROM favoritos  WHERE idPost = %d AND idUsuario=%d", $idPost, $idUsuario);
            if ( ! $conn->query($query) ) {
                error_log("Error BD ({$conn->errno}): {Error al borrar el like de la BBDD}");
                return false;
            }
            return true;
        }
    }

?>