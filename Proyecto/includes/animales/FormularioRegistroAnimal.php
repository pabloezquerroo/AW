<?php
/* nose si esta bien asi */
namespace es\ucm\fdi\aw\animales;

use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\Imagen;
/* */
class FormularioRegistroAnimal extends Formulario
{
    private $idProtectora;
    const EXTENSIONES_PERMITIDAS = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp', 'avif');

    public function __construct($IDprotectora) {
        parent::__construct('formRegistro', ['enctype' => 'multipart/form-data','urlRedireccion' => 'animales.php']);
        $this->idProtectora=$IDprotectora;
    }

    protected function generaCamposFormulario(&$datos){
        $nombre = $datos['nombre'] ?? '';
        $edad = $datos['edad'] ?? '';
        $raza = $datos['raza'] ?? '';
        $peso = $datos['peso'] ?? '';

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'edad', 'raza', 'peso','archivo'], $this->errores, 'span', array('class' => 'error'));
        
        //lista protectoras
        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoSelectbox">
            <label for="tipoAnimal">Tipo de animal</label>
            <select name="tipoAnimal" id="tipoAnimal" class="selectbox">
                <option value="Perro">Perro</option>
                <option value="Gato">Gato</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <div class="campoRegistro">
            <input id="nombre" type="text" name="nombre" id="nombre" value="$nombre" placeholder="a"/>
            <label for="nombre">Nombre</label>
            {$erroresCampos['nombre']}
            <span id = "errorNombre"></span>
        </div>
        <div class="campoRegistro">
            <input id="edad" type="number" name="edad" value="$edad" min="0" placeholder="a"/>
            <label for="edad">Edad</label>
            {$erroresCampos['edad']}
            <span id = "errorEdad"></span>

        </div>
        <div class="campoSelectbox">
            
            <select name="generos" id="generos" class="selectbox"/>
                <option value="Macho">Macho</option>
                <option value="Hembra">Hembra</option>
                <option value="SN">Sin Genero</option>
            </select>
            <label for="generos">Genero</label>
        </div>
        <div class="campoRegistro">
            <input id="raza" type="text" name="raza" id="raza" value="$raza" placeholder="a"/>
            <label for="raza">Raza</label>
            {$erroresCampos['raza']}
            <span id = "errorRaza"></span>
        </div>
        <div class="campoRegistro">
            <input id="peso" type="number" name="peso" value="$peso" placeholder="a" min = "0"/>
            <label for="peso">Peso</label>
            {$erroresCampos['peso']}
            <span id = "errorPeso"></span>
        </div>
        <div class="campoRegistro">
            <input type="file" name="archivo" id="archivo" />
            <label for="archivo">Archivo </label>
            {$erroresCampos['archivo']}
            <span id = "errorFile"></span>
        </div>
        <div class="campoRegistro">
            <button type="submit" name="registro">Registrar</button>
        </div>
        EOF;
        return $html;
    }
    


    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $tipo = trim($datos['tipoAnimal'] ?? '');

        $nombre = trim($datos['nombre'] ?? '');
        $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nombre || mb_strlen($nombre) > 30) {
            $this->errores['nombre'] = 'El nombre tiene que tener una longitud menor a 30 caracteres.';
        }

        $edad = trim($datos['edad'] ?? '');
        $edad = filter_var($edad, FILTER_SANITIZE_NUMBER_INT);
        if ( ! $edad) {
            $this->errores['edad'] = 'La edad no es válida.';
        }

        $peso = trim($datos['peso'] ?? '');
        $peso = filter_var($peso, FILTER_SANITIZE_NUMBER_FLOAT);
        if ( ! $peso || $peso < 0 ) {
            $this->errores['peso'] = 'Error al introducir el peso.';
        }

        $genero = trim($datos['generos'] ?? '');

        $raza = trim($datos['raza'] ?? '');
        $raza = filter_var($raza, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $raza || mb_strlen($raza) > 30) {
            $this->errores['raza'] = 'La raza tiene que tener una longitud menor a 30 caracteres.';
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
            $Animal = Animal::crea($nombre, $tipo, $edad, $this->idProtectora, $genero, $raza, $peso);
            $idAnimal = $Animal->getId();    
            if($ok){        
                $fichero = "{$idAnimal}";
                $fichero .= ".{$extension}";

                $Animal->setImagen($fichero);
                $Animal->guarda();
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES, $fichero]);
                if(!move_uploaded_file($tmp_name, $rutaImg)){
                    $this->errores['archivo'] = 'Error al mover el archivo';
                }
            }

        }else{
            $this->errores[] = "Error de registro de animal";
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
