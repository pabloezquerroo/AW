<?php

namespace es\ucm\fdi\aw\blog;
use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\usuarios\Usuario;

class FormularioCreaPost extends Formulario
{
    const EXTENSIONES_PERMITIDAS = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp', 'avif');
 
    public function __construct() {
        parent::__construct('formRegistro', ['enctype' => 'multipart/form-data','urlRedireccion' => 'blog.php']);
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $descripcion = $datos['descripcion'] ?? '';
        $titular = $datos['titular'] ?? '';

        $idUser = $_SESSION['id'];
        $user = Usuario::buscaPorId($idUser);

        if($user){
            $nombreUser = $user->getNombre();
            $protectoras_user = Protectora::buscaMisProtectoras(Protectora::ACTIVA, $idUser);

            $opciones = <<<EOF
                <option  value="$nombreUser" >$nombreUser</option>
                EOF;

            $i = 0;
            if($protectoras_user){
                while($i < sizeof($protectoras_user)){
                    $nombreProtectora = $protectoras_user[$i]->getNombre();
                    $opciones .= <<<EOF
                        <option  value="$nombreProtectora" >$nombreProtectora</option>
                        EOF;
                    $i = $i + 1;
                }
            }

        }

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['descripcion', 'titular', 'archivo'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoRegistro">
            <input id="titulo" type="text" name="titular" value="$titular" tabindex = "1" />
            <label for="titular">Título</label>
            {$erroresCampos['titular']}
            <span id= "errorTitulo"></span>
        </div>
        <div class="campoRegistro">
            <textarea id ="descripcion" name="descripcion" rows="5" cols="50" tabindex = "2" placeholder="">$descripcion</textarea>
            <label for="descripcion">Descripción</label>
            {$erroresCampos['descripcion']}
            <span id= "errorDesc"></span>

        </div>
        <div class="campoSelectbox">
            <select name="IdCreador" id="IdCreador" class="selectbox" tabindex = "3"/>
                $opciones
            </select>
            <label for="IdCreador">Creado Por</label>
        </div>
        <div class="campoRegistro">
            <input type="file" name="archivo" id="archivo" />
            <label for="archivo">Archivo </label>
            {$erroresCampos['archivo']}
        </div>
        <div class="campoRegistro">
            <button type="submit" name="registro">Registrar</button>
        </div>
        EOF;
        return $html;
    }

    /* Gestion de los valores del formulario */
    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $titular = trim($datos['titular'] ?? '');
        $titular = filter_var($titular, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $titular || mb_strlen($titular) < 5) {
            $this->errores['titular'] = 'El titular del post debe de tener una longitud, de al menos 5 caracteres.';
        }

        $descripcion = trim($datos['descripcion'] ?? '');
        $descripcion = filter_var($descripcion, FILTER_SANITIZE_STRING);
        if ( ! $descripcion || mb_strlen($descripcion) < 1 || mb_strlen($descripcion) > 500) {
            $this->errores['descripcion'] = 'La descripcion del post no puede estar vacio u ocupar más de 500 caracteres';
        }

        $nombreCreador = trim($datos['IdCreador'] ?? '');

        
        //Tenemos la protectora que lo ha creado si esque lo ha creado una protectora
        $protectora = Protectora::buscaPornombre($nombreCreador);
        
        if($protectora){
            $idUsuario = $_SESSION['id'];
            $IdProtectora = $protectora->getId();
        }
        else{
            $idUsuario = $_SESSION['id'];
            $IdProtectora = 0;
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
        
        //Si no ha habido errores con ningun campo crea el post.
        if (count($this->errores) === 0) {

            $Arrayfecha = getdate();
            $anio = $Arrayfecha['year'];
            $dia = $Arrayfecha['mday'];
            $mes = $Arrayfecha['mon'];
            $fecha = "$dia/$mes/$anio"; //fecha de creacion del post.
            
            $post = Post::crea($descripcion, $titular, $IdProtectora, $idUsuario, $fecha);

            //Si registramos el post con una imagen
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