<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioRegistroProtectora extends Formulario
{
    const EXTENSIONES_PERMITIDAS = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp', 'avif');

    public function __construct() {
        parent::__construct('formRegistro', ['enctype' => 'multipart/form-data', 'urlRedireccion' => 'protectoras.php']);
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $nombre = $datos['nombre'] ?? '';
        $telefono = $datos['telefono'] ?? '';
        $email = $datos['email'] ?? '';
        $direccion = $datos['direccion'] ?? '';
        $descripcion = $datos['descripcion'] ?? '';
        $archivo = $datos['archivo'] ?? null;

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'telefono', 'email', 'direccion', 'descripcion', 'archivo'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoRegistro">
            <input id="nombre" type="text" name="nombre" value="$nombre" placeholder="a"/>
            <label for="nombre">Nombre</label>
            {$erroresCampos['nombre']}
            <span id ="errorNombre"></span>
            </div>
        <div class="campoRegistro">
            <input id="email" type="email" name="email" value="$email" placeholder="a"/>
            <label for="email">Email</label>
            {$erroresCampos['email']}
            <span id ="errorEmail"></span>
        </div>
        <div class="campoRegistro">
            <input id="telefono" type="number" name="telefono" value="$telefono" placeholder="a"/>
            <label for="telefono">Telefono</label>
            {$erroresCampos['telefono']}
            <span id ="errorTel"></span>
        </div>
        <div class="campoRegistro">
            <input id="direccion" type="text" name="direccion" value="$direccion" placeholder="a"/>
            <label for="direccion">Direccion</label>
            {$erroresCampos['direccion']}
            <span id ="errorDirec"></span>
        </div>
        <div class="campoRegistro">
            <textarea id = "descripcion" name="descripcion" rows="5" cols="50" placeholder="a">$descripcion</textarea>
            <label for="descripcion">Descripcion</label>
            {$erroresCampos['descripcion']}
            <span id = "errorDesc"></span>
        </div>
        <div class="campoRegistro">
            <input type="file" name="archivo" id="archivo" />
            <label for="archivo">Archivo</label>
            {$erroresCampos['archivo']}
            <span id ="errorFile"></span>
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

        $nombre = trim($datos['nombre'] ?? '');
        $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nombre || mb_strlen($nombre) < 3) {
            $this->errores['nombre'] = 'El nombre de la protectora tiene que tener una longitud de al menos 3 caracteres.';
        }

        $telefono = trim($datos['telefono'] ?? '');
        $telefono = filter_var($telefono, FILTER_VALIDATE_INT, array("options" => array("min_range"=>100000000, "max_range"=>999999999)));
        if ( !$telefono) {
            $this->errores['telefono'] = 'El telefono de la protectora no es correcto.';
        }

        $email = trim($datos['email'] ?? '');
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ( !$email) {
            $this->errores['email'] = 'El correo de la protectora no es correcto.';
        }

        $direccion = trim($datos['direccion'] ?? '');
        $direccion = filter_var($direccion, FILTER_SANITIZE_STRING);
        if ( ! $direccion || mb_strlen($direccion) < 10) {
            $this->errores['direccion'] = 'La direccion de la protectora no es correcta.';
        }

        $descripcion = trim($datos['descripcion'] ?? '');
        $descripcion = filter_var($descripcion, FILTER_SANITIZE_STRING);
        if ( ! $descripcion || mb_strlen($descripcion) < 5) {
            $this->errores['descripcion'] = 'La descripcion de la protectora no es correcta.';
        }
        elseif(mb_strlen($descripcion) > 500){
            $this->errores['descripcion'] = 'La descripcion de la protectora es demasiado larga';
        }

        $ok = $_FILES['archivo']['error'] == UPLOAD_ERR_OK && count($_FILES) == 1;
        //Si se ha subido un archivo
        if ($ok){
            $nombreImg = $_FILES['archivo']['name'];

            //Validacion nombre archivo nose porque no funciona check file name
            $ok = /*self::check_file_uploaded_name($nombreImg) &&*/ $this->check_file_uploaded_length($nombreImg);

            //Comprueba si la extension de la imagen esta permitida
            $extension = pathinfo($nombreImg, PATHINFO_EXTENSION);
            $ok = $ok && in_array($extension, self::EXTENSIONES_PERMITIDAS);

            //Comprobacion tipo de MIME
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['archivo']['tmp_name']);
            $ok = preg_match('/image\/*./', $mimeType);

            if(!$ok){
                $this->errores['archivo'] = 'El archivo tiene un nombre o tipo no soportado';
            }

            $tmp_name = $_FILES['archivo']['tmp_name'];
        }  

        
        //Si no ha habido errores con ningun campo crea la protectora.
        if (count($this->errores) === 0) {
            $protectora = Protectora::buscaPornombre($nombre);
            $protectoraaux = Protectora::buscaPorEmail($email);
            if ($protectora) {
                $this->errores[] = "La protectora ya existe";
            } 
            elseif($protectoraaux){
                $this->errores[]="Ese email ya esta registrado";
            }
            else {
                $protectora = Protectora::crea($nombre, $telefono, $email, $direccion, $descripcion);
                $idUsuario = $_SESSION['id'];
                $colabora = Colabora::creaCreador($protectora->getId(), $idUsuario, Colabora::CREADOR);
                if ($ok){
                    $idProtectora = $protectora->getId();

                    $fichero = "{$idProtectora}";
                    $fichero .= ".{$extension}";
                    $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS, $fichero]);
                    $protectora->setImagen($fichero);
                    $protectora->guarda();

                    if(!move_uploaded_file($tmp_name, $rutaImg)){
                        $this->errores['archivo'] = 'Error al mover el archivo';
                    }
                }   
            }
        } 
        else {
            $this->errores[] = "Error de registro de protectora";
        }
    }

    /**
     * Check $_FILES[][name]
     *
     * @param (string) $filename - Uploaded file name.
     * @author Yousef Ismaeil Cliprz
     * @See http://php.net/manual/es/function.move-uploaded-file.php#111412
     */
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