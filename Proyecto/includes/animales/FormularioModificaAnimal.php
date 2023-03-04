<?php
namespace es\ucm\fdi\aw\animales;

use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\Imagen;

class FormularioModificaAnimal extends Formulario
{
    const EXTENSIONES_PERMITIDAS = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp', 'avif');
    const TIPOS = array('Perro', 'Gato', 'Otro');
    const GENEROS = array('Macho', 'Hembra', 'SN');

    private $idAnimal;
    public function __construct($idAnimal) {
        parent::__construct('formModifica', ['enctype' => 'multipart/form-data', 'urlRedireccion' => "animal.php?id=$idAnimal"]);
        $this->idAnimal=$idAnimal;
    }
    
    protected function encontrarTipo($tipo){
        $name="tipoAnimal";
        $html="<select name=\"{$name}\" class=\"selectbox\">";
        foreach($this::TIPOS as $tipoAnimal ){
            $selected =$tipoAnimal==$tipo?"selected=\"selected\"":"";
            $html.= "<option name=\"$tipoAnimal\" value=\"$tipoAnimal\" $selected>$tipoAnimal</option>";
        }
        $html.="</select>";
        return $html;
    }

    protected function encontrarGenero($genero){
        $name="generos";
        $html="<select name=\"{$name}\" class=\"selectbox\">";
        foreach($this::GENEROS as $generoAnimal ){
            $selected =$generoAnimal==$genero?"selected=\"selected\"":"";
            $html.= "<option name=\"$generoAnimal\" value=\"$generoAnimal\" $selected>$generoAnimal</option>";
        }
        $html.="</select>";
        return $html;
    }

    protected function generaCamposFormulario(&$datos){
        $animal=Animal::buscaPorID($this->idAnimal);
        if(!$animal){
            $nombre = $datos['nombre'] ?? '';
            $edad = $datos['edad'] ?? '';
            $raza = $datos['raza'] ?? '';
            $peso = $datos['peso'] ?? '';
        }else{
            $nombre = $datos['nombre'] ?? $animal->getNombre();
            $tipo = $datos['tipo'] ?? $animal->getTipo();
            $edad = $datos['edad'] ?? $animal->getEdad();
            $raza = $datos['raza'] ?? $animal->getRaza();
            $genero = $datos['genero'] ?? $animal->getGenero();
            $peso = $datos['peso'] ?? $animal->getPeso();

        }
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['nombre', 'edad', 'raza', 'peso','archivo'], $this->errores, 'span', array('class' => 'error'));
        
        $selectTipo=$this->encontrarTipo($tipo);
        $selectGenero=$this->encontrarGenero($genero);
        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoSelectbox">
            <label>Tipo de animal</label>
            $selectTipo
        </div>
        <div class="campoModifica">
            <label for="">Nombre</label>
            <input id="nombre" type="text" name="nombre" value="$nombre" />
            {$erroresCampos['nombre']}
        </div>
        <div class="campoModifica">
        <label for="edad">Edad</label>
        <input id="edad" type="number" name="edad" value="$edad" />
        {$erroresCampos['edad']}
        </div>
        <div class="campoSelectbox">
            <label for="genero">Genero:</label>
            $selectGenero
        </div>
        <div class="campoModifica">
            <label for="">Raza</label>
            <input id="raza" type="text" name="raza" value="$raza" />
            {$erroresCampos['raza']}
        </div>
        <div class="campoModifica">
            <label for="peso">Peso</label>
            <input id="peso" type="number" name="peso" value="$peso"/>
            {$erroresCampos['peso']}
        </div>
        <div class="campoModifica">
            <input type="file" name="archivo" id="archivo" />
            <label for="archivo">Archivo </label>
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

        $tipo = trim($datos['tipoAnimal'] ?? '');

        $nombre = trim($datos['nombre'] ?? '');
        $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nombre || mb_strlen($nombre) > 30) {
            $this->errores['nombre'] = 'El nombre tiene que tener una longitud menor a 30 caracteres.';
        }

        $edad = trim($datos['edad'] ?? '');
        $edad = filter_var($edad, FILTER_VALIDATE_INT);
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
            $ok = self::check_file_uploaded_name($nombreImg) && $this->check_file_uploaded_length($nombre);
            
            $extension = pathinfo($nombreImg, PATHINFO_EXTENSION);
            $ok = $ok && in_array($extension, self::EXTENSIONES_PERMITIDAS);
            
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($_FILES['archivo']['tmp_name']);
            $ok = preg_match('/image\/*./', $mimeType);
            if (!$ok) {
                $this->errores['archivo'] = 'El archivo tiene un nombre o tipo no soportado';
            }
            $tmp_name = $_FILES['archivo']['tmp_name'];
        }

        if (count($this->errores) === 0) {
            $animal = Animal::buscaPorId($this->idAnimal);
            
            
            if($ok){
                $fichero = "{$this->idAnimal}";
                $fichero .= ".{$extension}";

                $animal->modificaAnimal($nombre, $tipo, $edad, $genero, $raza, $peso, $fichero);
                $animal->guarda();

                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES, $fichero]);
                if(!move_uploaded_file($tmp_name, $rutaImg)){
                    $this->errores['archivo'] = 'Error al mover el archivo';
                }
            }
            else{
                $imagen = $animal->getImagen();
                $animal->modificaAnimal($nombre, $tipo, $edad, $genero, $raza, $peso, $imagen);
                $animal->guarda();
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
