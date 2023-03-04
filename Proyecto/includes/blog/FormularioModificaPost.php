<?php
    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\Formulario;

    class FormularioModificaPost extends Formulario
    {
        const EXTENSIONES_PERMITIDAS = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp', 'avif');
        private $idPost;

        public function __construct(){
            $this->idPost = $_GET['p'];
            parent::__construct('formModifica', ['enctype' => 'multipart/form-data', 'urlRedireccion' => "post.php?p=$this->idPost"]);
        }

        protected function generaCamposFormulario(&$datos)
        {
            $post = Post::buscaPorId($this->idPost);
            
            if (!$post){    //Si no existe el post
                $descripcion = $datos['descripcion'] ?? '';
                $titular = $datos['titular'] ?? '';
            }else{  // Si existe el post
                $descripcion = $datos['descripcion'] ?? $post->getDescripcion();
                $titular = $datos['titular'] ?? $post->getTitular();
            }
        
            // Se generan los mensajes de error si existen.
            $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
            $erroresCampos = self::generaErroresCampos(['descripcion', 'titular', 'archivo'], $this->errores, 'span', array('class' => 'error'));

            $html = <<<EOF
            $htmlErroresGlobales
            <div class="campoModifica">
                <label for="titular">Titular</label>
                <input id="titular" type="text" name="titular" value="$titular"/> 
                {$erroresCampos['titular']}
            </div>
            <div class="campoModifica">
                <textarea name="descripcion" rows="5" cols="50">
                $descripcion
                </textarea>
                <label for="descripcion">Descripcion</label>
                {$erroresCampos['descripcion']}
            </div>
            <div class="campoModifica">
                <label for="archivo">Archivo </label>
                <input type="file" name="archivo" id="archivo" />
                {$erroresCampos['archivo']}
            </div>
            <div class="campoModifica">
                <button type="submit" name="registro">Modificar</button>
            </div>
            EOF;

            return $html;
        }

        protected function procesaFormulario(&$datos)
        {
            $this->errores = [];

            $titular = trim($datos['titular'] ?? '');
            $titular = filter_var($titular, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ( ! $titular || mb_strlen($titular) < 5) {
                $this->errores['titular'] = 'El titular del post debe de tener una longitud, de al menos 8 caracteres.';
            }

            $descripcion = trim($datos['descripcion'] ?? '');
            $descripcion = filter_var($descripcion, FILTER_SANITIZE_STRING); 
            if ( ! $descripcion || mb_strlen($descripcion) < 20) {
                $this->errores['descripcion'] = 'La descripcion de la protectora debe de tener una longitud, de al menos, 20 caracteres.';
            }

            $ok = $_FILES['archivo']['error'] == UPLOAD_ERR_OK && count($_FILES) == 1;
            if ($ok) {
                $nombreImg = $_FILES['archivo']['name'];
                /* 1.a) Valida el nombre del archivo */
                $ok = self::check_file_uploaded_name($nombreImg) && $this->check_file_uploaded_length($nombreImg);
            
                /* 1.b) Sanitiza el nombre del archivo (elimina los caracteres que molestan)
                $ok = self::sanitize_file_uploaded_name($nombre);
                */

                /* 1.c) Utilizar un id de la base de datos como nombre de archivo */
                // Vamos a optar por esta opción que es la que se implementa más adelante

                /* 2. comprueba si la extensión está permitida */
                $extension = pathinfo($nombreImg, PATHINFO_EXTENSION);
                $ok = $ok && in_array($extension, self::EXTENSIONES_PERMITIDAS);

                /* 3. comprueba el tipo mime del archivo corresponde a una imagen image/* */
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($_FILES['archivo']['tmp_name']);
                $ok = preg_match('/image\/*./', $mimeType);

                if (!$ok) {
                    $this->errores['archivo'] = 'El archivo tiene un nombre o tipo no soportado';
                }

                $tmp_name = $_FILES['archivo']['tmp_name'];
            
            }

            if (count($this->errores) === 0) {
                $post = Post::buscaPorId($this->idPost);
                $post->modificaPost($titular, $descripcion);
                $post->guarda();

                if($ok){
                    $idPost = $post->getId();
                    $fichero = "{$idPost}";
                    $fichero .= ".{$extension}";
                    $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_POST, $fichero]);

                    if(!move_uploaded_file($tmp_name, $rutaImg)){
                        $this->errores['archivo'] = 'Error al mover el archivo';
                    }

                    $post->setImagen($fichero);
                    $post->guarda();
                }
            }
        }

        private static function check_file_uploaded_name($filename)
        {
            return (bool) ((mb_ereg_match('/^[0-9A-Z-_\.]+$/i', $filename) === 1) ? true : false);
        }

        /**
         * Sanitize $_FILES[][name]. Remove anything which isn't a word, whitespace, number
         * or any of the following caracters -_~,;[]().
         *
         * If you don't need to handle multi-byte characters you can use preg_replace
         * rather than mb_ereg_replace.
         * 
         * @param (string) $filename - Uploaded file name.
         * @author Sean Vieira
         * @see http://stackoverflow.com/a/2021729
         */
        private static function sanitize_file_uploaded_name($filename)
        {
            /* Remove anything which isn't a word, whitespace, number
        * or any of the following caracters -_~,;[]().
        * If you don't need to handle multi-byte characters
        * you can use preg_replace rather than mb_ereg_replace
        * Thanks @Łukasz Rysiak!
        */
            $newName = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
            // Remove any runs of periods (thanks falstro!)
            $newName = mb_ereg_replace("([\.]{2,})", '', $newName);

            return $newName;
        }

        /**
         * Check $_FILES[][name] length.
         *
         * @param (string) $filename - Uploaded file name.
         * @author Yousef Ismaeil Cliprz.
         * @See http://php.net/manual/es/function.move-uploaded-file.php#111412
         */
        private function check_file_uploaded_length($filename)
        {
            return (bool) ((mb_strlen($filename, 'UTF-8') < 250) ? true : false);
        }
    }
?>