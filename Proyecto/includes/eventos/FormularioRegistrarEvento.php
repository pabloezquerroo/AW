<?php

namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\eventos\Participa;
use es\ucm\fdi\aw\eventos\Asiste;
use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\protectora\Protectora;
use \DateTime;

class FormularioRegistrarEvento extends Formulario
{
    const FORMAT_INPUT_DATETIME_LOCAL = 'Y-m-d\TH:i';
    const EXTENSIONES_PERMITIDAS = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'webp', 'avif');

    public function __construct() {
        parent::__construct('formRegistro', ['enctype' => 'multipart/form-data', 'urlRedireccion' => 'eventos.php']);
    }

    protected function camposProtectora(){
        $misProtectoras = Protectora::buscaMisProtectorasSinLimit(Protectora::ACTIVA ,$_SESSION['id']);
        $name="protectoras";
        $html="<select name=\"$name\" class=\"selectbox\">";
        if($misProtectoras == null) return "";
        foreach($misProtectoras as $protectora ){
            $nombreProtectora=$protectora->getNombre();
            $idProtectora=$protectora->getId();
            $html.= "<option value=\"$idProtectora\" >$nombreProtectora</option>";
        }
        $html.="</select>";
        return $html;
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $descripcion = $datos['descripcion'] ?? '';
        $titulo = $datos['titulo'] ?? '';

        $fechaIni = $datos['fecha_inicio']?? DateTime::createFromFormat('DD/MM/YYYY', $_GET['date']);
        $fechaFin = $datos['fecha_fin']??'';        

        $camposProtectora=self::camposProtectora();
        $tipoMercadillo= Evento::MERCADILLO;
        $tipoCaminata= Evento::CAMINATA;
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['descripcion', 'titulo', 'fecha_inicio', 'fecha_fin', 'archivo'], $this->errores, 'span', array('class' => 'error'));
        $fechaActual=date(self::FORMAT_INPUT_DATETIME_LOCAL);
        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoRegistro">
            <input id="titulo" type="text" name="titulo" value="$titulo" placeholder="a" tabindex = "1"/>
            <label for="titulo">Título</label>
            {$erroresCampos['titulo']}
            <span id = "errorTitulo"></span>
        </div>
        <div class="campoSelectbox">
        <label for="protectoras">Protectoras</label>
        $camposProtectora
        </div>
        <div class="campoSelectbox">
            <label for="tipo">Tipo de Evento</label>
            <select name="tipo" id="tipo" class="selectbox">
                <option value=$tipoMercadillo>Mercadillo</option>
                <option value=$tipoCaminata>Caminata</option>
            </select>
        </div>
        <div class="campoRegistro">
            <textarea id = "descripcion" name="descripcion" rows="5" cols="50" placeholder="a" tabindex = "2">$descripcion</textarea>
            <label for="descripcion">Descripción</label>
            {$erroresCampos['descripcion']}
            <span id = "errorDesc"></span>

        </div>
        <div class="campoRegistro">
            <input id="fecha" type="datetime-local" name="fecha_inicio" min="$fechaActual" value="$fechaIni" tabindex = "3">
            <label for="fecha_inicio">Fecha Inicio</label>
        </div>
        <div class="campoRegistro">
            <input id="fecha" type="datetime-local" name="fecha_fin" min="$fechaIni" value="$fechaFin" tabindex = "4">
            <label for="fecha_fin">Fecha Fin</label>
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

        $titulo = trim($datos['titulo'] ?? '');
        $titulo = filter_var($titulo, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $titulo || mb_strlen($titulo) < 5) {
            $this->errores['titulo'] = 'El titulo del post debe de tener una longitud, de al menos 8 caracteres.';
        }

        $protectora=trim($datos['protectoras'] ?? '');

        $tipo=trim($datos['tipo'] ?? '');

        $descripcion = trim($datos['descripcion'] ?? '');
        $descripcion = filter_var($descripcion, FILTER_SANITIZE_STRING); /*usar otro saneamiento, filtersanitizestring sin soporte */
        if ( ! $descripcion || mb_strlen($descripcion) < 10) {
            $this->errores['descripcion'] = 'La descripcion de la protectora debe de tener una longitud, de al menos, 10 caracteres.';
        }

        $fechaIni = trim($datos['fecha_inicio'] ?? '');
        $fechaIni = filter_input(INPUT_POST, 'fecha_inicio', FILTER_SANITIZE_SPECIAL_CHARS);
        $dateIni=new DateTime();
        $dateIni = DateTime::createFromFormat(self::FORMAT_INPUT_DATETIME_LOCAL, $fechaIni);

        $fechaFin = trim($datos['fecha_fin'] ?? '');
        $fechaFin = filter_input(INPUT_POST, 'fecha_fin', FILTER_SANITIZE_SPECIAL_CHARS);
        $dateFin= new DateTime();
        $dateFin = DateTime::createFromFormat(self::FORMAT_INPUT_DATETIME_LOCAL, $fechaFin);
        if($dateFin<= $dateIni){
            $this->errores['fecha_fin'] = "La fecha de finalización es menor a la hora de inicio.";
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
            //Si no ha habido errores con ningun campo crea la protectora.
            if (count($this->errores) === 0) {
                $evento=Evento::buscaPorTitulo($titulo);
                if($evento){
                    $this->errores[] = "El evento ya existe";
                }
                else{
                    $evento=Evento::crea($titulo, $dateIni, $dateFin, $descripcion, $tipo);
                    if ($evento){
                        Participa::crea($evento->getId(), $protectora);
                        Asiste::crea($evento->getId(), $_SESSION['id']);
                    }
                    if ($ok){
                        $idEvento = $evento->getId();
    
                        $fichero = "{$idEvento}";
                        $fichero .= ".{$extension}";
                        $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_EVENTOS, $fichero]);
                        $evento->setImagen($fichero);
                        $evento->guarda();
    
                        if(!move_uploaded_file($tmp_name, $rutaImg)){
                            $this->errores['archivo'] = 'Error al mover el archivo';
                        }
                    }
                }
            } 
            else {
                $this->errores[] = "Error de registro de evento";
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