<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioModificaProtectora extends Formulario
{

    const EXTENSIONES_PERMITIDAS = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp', 'avif');

    private $idProtectora;
    //REdireccionar a protectora con idprotectora modificada.
    public function __construct($idProtectora) {
        parent::__construct('formModifica', ['enctype' => 'multipart/form-data','urlRedireccion' => "protectora.php?id=$idProtectora"]);
        $this->idProtectora = $idProtectora;
    }

    protected function generaCamposFormulario(&$datos)
    {
        $protectora = Protectora::buscaPorId($this->idProtectora);
        //FALTA CAMPO DE TEXTO EN DESCRIPCION
        if (!$protectora){
            $nombre = $datos['nombre'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $email = $datos['email'] ?? '';
            $direccion = $datos['direccion'] ?? '';
            $descripcion = $datos['descripcion'] ?? '';
            $archivo = $datos['archivo'] ?? null;
        }else{
            $nombre = $datos['nombre'] ?? $protectora->getNombre();
            $telefono = $datos['telefono'] ?? $protectora->getTelefono();
            $email = $datos['email'] ?? $protectora->getEmail();
            $direccion = $datos['direccion'] ?? $protectora->getDireccion();
            $descripcion = $datos['descripcion'] ?? $protectora->getDescripcion();
            $archivo = $datos['archivo'] ?? null;
        }
    
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'telefono', 'email', 'direccion', 'descripcion', 'archivo'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoModifica">
            <label for="nombre">Nombre</label>
            <input id="nombre" type="text" name="nombre" value="$nombre" />
            {$erroresCampos['nombre']}
            <span id ="errorNombre"></span>
        </div>
        <div class="campoModifica">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="$email" />
            {$erroresCampos['email']}
            <span id ="errorEmail"></span>
        </div>
        <div class="campoModifica">
            <label for="telefono">Telefono</label>
            <input id="telefono" type="number" name="telefono" value="$telefono" min = "0"/>
            {$erroresCampos['telefono']}
            <span id ="errorTel"></span>
        </div>
        <div class="campoModifica">
            <label for="direccion">Direccion</label>
            <input id="direccion" type="text" name="direccion" value="$direccion" />
            {$erroresCampos['direccion']}
            <span id ="errorDirec"></span>
        </div>
        <div class="campoModifica">
            <textarea name="descripcion" rows="5" cols="50">
            $descripcion
            </textarea>
            <label for="descripcion">Descripcion</label>
            {$erroresCampos['descripcion']}
        </div>
        <div class="campoModifica">
            <input type="file" name="archivo" id="archivo" value="$archivo" />
            <label for="archivo">Archivo</label>
            {$erroresCampos['archivo']}
            <span id ="errorFile"></span>
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

        $nombre = trim($datos['nombre'] ?? '');
        $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nombre || mb_strlen($nombre) < 3) {
            $this->errores['nombre'] = 'El nombre tiene que tener una longitud de al menos 3 caracteres.';
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
        $ok = $_FILES['archivo']['error'] == UPLOAD_ERR_OK && count($_FILES) == 1;
        if ($ok) {
            $nombreImg = $_FILES['archivo']['name'];

            //Validacion nombre archivo nose porque no funciona check file name
            $ok = $this->check_file_uploaded_length($nombreImg);

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


        if (count($this->errores) === 0) {
            $protectora = Protectora::buscaPorId($this->idProtectora);
            
            if($ok){
                $idProtectora = $protectora->getId();
                $fichero = "{$idProtectora}";
                $fichero .= ".{$extension}";
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS, $fichero]);
                $protectora->modificaProtectora($nombre, $telefono, $email, $direccion, $descripcion, $fichero);
                $protectora->guarda();

                if(!move_uploaded_file($tmp_name, $rutaImg)){
                    $this->errores['archivo'] = 'Error al mover el archivo';
                }
            }
            else{
                $imagen = $protectora->getImagen();
                $protectora->modificaProtectora($nombre, $telefono, $email, $direccion, $descripcion, $imagen);
                $protectora->guarda();
            }
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

?>