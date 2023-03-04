<?php

namespace es\ucm\fdi\aw\eventos;

use DateTime;
use es\ucm\fdi\aw\animales\Participa;
use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\protectora\Protectora;

class FormularioModificaEvento extends Formulario
{
    const EXTENSIONES_PERMITIDAS = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp', 'avif');
    const FORMAT_INPUT_DATETIME_LOCAL = 'Y-m-d\TH:i';
    const FORMAT_MYSQL = 'Y-m-d H:i:s';
    const TIPOS = array(Evento::CAMINATA, Evento::MERCADILLO);

    private $idEvento;
    public function __construct($idEvento) {
        parent::__construct('formModifica', ['enctype' => 'multipart/form-data', 'urlRedireccion' => "evento.php?id=$idEvento"]);
        $this->idEvento=$idEvento;
    }

    protected function seleccionTipo($idEvento){
        $evento=Evento::buscaPorId($idEvento);
        $name="tipo";
        $html="<select name=\"$name\" id=\"$name\" class=\"selectbox\">";
        foreach($this::TIPOS as $tipoEvento ){
            if($tipoEvento==Evento::CAMINATA){
                $nombreTipo="Caminata";
            }else{
                $nombreTipo="Mercadillo";
            }
            $selected =$tipoEvento==$evento->getTipo()?"selected=\"selected\"":"";
            $html.= "<option value=\"$tipoEvento\" $selected>$nombreTipo</option>";
        }
        $html.="</select>";
        return $html;
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $evento=Evento::buscaPorId($this->idEvento);
        if (!$evento){
            $descripcion = $datos['descripcion'] ?? '';
            $titulo = $datos['titulo'] ?? '';
            $fechaIni = $datos['fechaIni']??'';
            $fechaFin = $datos['fechaFin']??'';
        }else{
            $descripcion = $datos['descripcion'] ?? $evento->getDescripcion();
            $titulo = $datos['titulo'] ?? $evento->getTitulo();
            
            $fechaIni = new DateTime();
            $fechaIniSql = $datos['fechaIni']??$evento->getFechaIni();
            $fechaIni= DateTime::createFromFormat(self::FORMAT_MYSQL,$fechaIniSql);
            $fechaIni=$fechaIni->format(self::FORMAT_INPUT_DATETIME_LOCAL);
            $fechaFin = new DateTime();
            $fechaFinSql = $datos['fechaFin']??$evento->getFechaFin();
            $fechaFin= DateTime::createFromFormat(self::FORMAT_MYSQL,$fechaFinSql);
            $fechaFin=$fechaFin->format(self::FORMAT_INPUT_DATETIME_LOCAL);
        }

        //$selectProtectora=self::seleccionProtectora($this->idEvento);
        $selectTipo=self::seleccionTipo($this->idEvento);

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['descripcion', 'titulo', 'fecha_inicio', 'fecha_fin', 'archivo'], $this->errores, 'span', array('class' => 'error'));
        $fechaActual=date(self::FORMAT_INPUT_DATETIME_LOCAL);
        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoModifica">
            <label for="titulo">Titulo</label>    
            <input id="titulo" type="text" name="titulo" value="$titulo" />
            {$erroresCampos['titulo']}
        </div>
        <div class="campoModifica">
            <label for="tipo">Tipo de Evento</label>
            $selectTipo
        </div>
        <div class="campoModifica">
            <textarea name="descripcion" rows="5" cols="50" placeholder="a">
            $descripcion
            </textarea>
            <label for="descripcion">Descripcion</label>
            {$erroresCampos['descripcion']}
        </div>
        <div class="campoModifica">
            <label for="fechaIni">Fecha Inicio</label>
            <input id="fecha" type="datetime-local" name="fechaIni" min="$fechaActual" value="$fechaIni">
        </div>
        <div class="campoModifica">
            <label for="fechaFin">Fecha Fin</label>
            <input id="fecha" type="datetime-local" name="fechaFin" min="$fechaIni" value="$fechaFin">
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

    /* Gestion de los valores del formulario */
    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $titulo = trim($datos['titulo'] ?? '');
        $titulo = filter_var($titulo, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $titulo || mb_strlen($titulo) < 5) {
            $this->errores['titulo'] = 'El titulo del post debe de tener una longitud, de al menos 8 caracteres.';
        }

        $tipo=trim($datos['tipo'] ?? '');

        $descripcion = trim($datos['descripcion'] ?? '');
        $descripcion = filter_var($descripcion, FILTER_SANITIZE_STRING); /*usar otro saneamiento, filtersanitizestring sin soporte */
        if ( ! $descripcion || mb_strlen($descripcion) < 10) {
            $this->errores['descripcion'] = 'La descripcion de la protectora debe de tener una longitud, de al menos, 10 caracteres.';
        }

       
        $fechaIni = trim($datos['fechaIni'] ?? '');
        $fechaIni = filter_input(INPUT_POST, 'fechaIni', FILTER_SANITIZE_SPECIAL_CHARS);
        $dateIni=new DateTime();
        $dateIni = DateTime::createFromFormat(self::FORMAT_INPUT_DATETIME_LOCAL, $fechaIni);

        $fechaFin = trim($datos['fechaFin'] ?? '');
        $fechaFin = filter_input(INPUT_POST, 'fechaFin', FILTER_SANITIZE_SPECIAL_CHARS);
        $dateFin= new DateTime();
        $dateFin = DateTime::createFromFormat(self::FORMAT_INPUT_DATETIME_LOCAL, $fechaFin);

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
        
            //Si no ha habido errores con ningun campo crea la protectora.
            if (count($this->errores) === 0) {
                $evento=Evento::buscaPorId($this->idEvento);
                if($ok){
                    $idEvento= $evento->getId();
                    $fichero = "{$idEvento}";
                    $fichero .= ".{$extension}";
                    $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_EVENTOS, $fichero]);
                    $evento->modifica($titulo, $dateIni, $dateFin, $descripcion, $tipo, $fichero);
                    $evento->guarda();
                    if(!move_uploaded_file($tmp_name, $rutaImg)){
                        $this->errores['archivo'] = 'Error al mover el archivo';
                    }
                }
                else{
                    $fichero=$evento->getImagen();
                    $evento->modifica($titulo, $dateIni, $dateFin, $descripcion, $tipo, $fichero);
                    $evento->guarda();
                } 
            } 
            else {
                $this->errores[] = "Error de registro de protectora";
            }
        }else{
            $evento=Evento::buscaPorId($this->idEvento);
            $fichero=$evento->getImagen();
            $evento->modifica($titulo, $dateIni, $dateFin, $descripcion, $tipo, $fichero);
            $evento->guarda();
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