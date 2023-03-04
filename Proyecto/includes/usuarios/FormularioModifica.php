<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Formulario;

class FormularioModifica extends Formulario
{
    const EXTENSIONES_PERMITIDAS = array('jpg','jpeg', 'png');
    const TIPOS_VIVIENDA = array('Piso', 'Casa', 'Otro');

    public function __construct() {
        parent::__construct('formModifica', ['enctype' => 'multipart/form-data', 'urlRedireccion' => 'perfil.php']);
    }

    protected function encontrarTipoVivienda($tipo){
        $name="tipo_vivienda";
        $html="<select name=\"{$name}\" class=\"selectbox\">";
        foreach($this::TIPOS_VIVIENDA as $tipo_vivienda ){
            $selected =$tipo_vivienda==$tipo?"selected=\"selected\"":"";
            $html.= "<option name=\"$tipo_vivienda\" value=\"$tipo_vivienda\" $selected>$tipo_vivienda</option>";
        }
        $html.="</select>";
        return $html;
    }

    protected function tieneTerraza($terraza){
        if (Usuario::TIENE_TERRAZA==$terraza){
            $tieneTerraza = "checked";
        }
        else{
            $tieneTerraza = "";
        }
        return $tieneTerraza;
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $usuario=Usuario::buscaUsuario($_SESSION['email']);
        if(!$usuario){
            $nombre = $datos['nombre'] ?? '';
            $email = $datos['email'] ?? '';
            $direccion = $datos['direccion'] ?? '';
            $num_convivientes = $datos['num_convivientes'] ?? '';
            $dedicacion = $datos['dedicacion'] ?? '';
            $terraza = $datos['terraza'] ?? '';
            $num_mascotas = $datos['num_mascotas'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $m2_vivienda = $datos['m2_vivienda'] ?? '';
            $archivo = $datos['imagen'] ?? '';
        }else{
            $nombre = $datos['nombre'] ?? $usuario->getNombre();
            $email = $datos['email'] ?? $usuario->getEmail();
            $direccion = $datos['direccion'] ?? $usuario->getDireccion();
            $num_convivientes = $datos['num_convivientes'] ?? $usuario->getNumConvivientes();
            $tipo_vivienda = $datos['tipo_vivienda'] ?? $usuario->getTipoVivienda();
            $dedicacion = $datos['dedicacion'] ?? $usuario->getDedicacion();
            $terraza = $datos['terraza'] ?? $usuario->getTerraza();
            $num_mascotas = $datos['num_mascotas'] ?? $usuario->getNumMascotas();
            $telefono = $datos['telefono'] ?? $usuario->getTelefono();
            $m2_vivienda = $datos['m2_vivienda'] ?? $usuario->getM2Vivienda();
            $archivo = $datos['imagen'] ?? $usuario->getImagen();
        }
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['email', 'nombre', 'direccion', 'num_convivientes', 'tipo_vivienda', 'dedicacion', 'terraza', 'num_mascotas', 'telefono', 'm2_vivienda', 'archivo'], $this->errores, 'span', array('class' => 'error'));
        $selectTipoVivienda = self::encontrarTipoVivienda($usuario->getTipoVivienda());
        $tieneTerraza = self::tieneTerraza($terraza);
        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoModifica">
            <label for="email">Nuevo correo del usuario</label>
            <input id="email" type="email" name="email" value="$email" />
            {$erroresCampos['email']}
        </div>
        <p id="errorEmail"></p>
        <div class="campoModifica">
            <label for="nombre">Nuevo nombre</label>
            <input id="nombre" type="text" name="nombre" value="$nombre" />
            {$erroresCampos['nombre']}
        </div>
        <p id="errorNombre"></p>
        <div class="campoModifica">
            <label for="direccion">Nueva dirección de tu domicilio</label>
            <input id="direccion" type="text" name="direccion" value="$direccion" />
            {$erroresCampos['direccion']}
        </div>
        <p id="errorDirec"></p>
        <div class="campoModifica">
            <label for="num_convivientes">¿Con cuántas personas vives ahora?</label>
            <input id="num_convivientes" type="number" name="num_convivientes" min="0" value="$num_convivientes" />
            {$erroresCampos['num_convivientes']}
        </div>
        <p id="errorConv"></p>
        <div class="campoSelectbox">
            <label for="tipo_vivienda">¿Ahora vives en un piso o casa?</label>
            $selectTipoVivienda
        </div>
        <div class="campoModifica">
            <label for="dedicacion">¿A qué te dedicas ahora?</label>
            <input id="dedicacion" type="text" name="dedicacion" value="$dedicacion" />
            {$erroresCampos['dedicacion']}
        </div>
        <p id="errorDedi"></p>
        <div class="checkbox">
            <label for="terraza">Marca la casilla si tu domicilio tiene terraza</label>
            <input type="checkbox" name="terraza" id="terraza" value="$terraza" $tieneTerraza>
        </div>
        <div class="campoModifica">
            <label for="num_mascotas">¿Cuántas mascotas tiene?</label>
            <input id="num_mascotas" type="number" name="num_mascotas" min="0" value="$num_mascotas" />
            {$erroresCampos['num_mascotas']}
        </div>
        <p id="errorMasc"></p>
        <div class="campoModifica">
            <label for="telefono">Teléfono nuevo</label>
            <input id="telefono" type="tel" name="telefono" value="$telefono" />
            {$erroresCampos['telefono']}
        </div>
        <p id="errorTel"></p>
        <div class="campoModifica">
            <label for="m2_vivienda">Metros cuadrados de su vivienda</label>
            <input id="m2_vivienda" type="number" min = "0" name="m2_vivienda" value="$m2_vivienda" />
            {$erroresCampos['m2_vivienda']}
        </div>
        <p id="errorMviv"></p>
        <div class="campoModifica">
            <input type="file" name="archivo" id="archivo" value="$archivo" />
            <label for="archivo">Archivo</label>
            {$erroresCampos['archivo']}
        </div>
        <p id="errorFile"></p>
        <div class="campoModifica">
            <button type="submit" name="registro">Modificar</button>
        </div>
        EOF;

        return $html;
    }
    

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $email = trim($datos['email'] ?? '');

        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if ( ! $email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errores['email'] = 'El correo del usuario no es correcto.';
        }

        $nombre = trim($datos['nombre'] ?? '');
        $nombre = filter_var($nombre, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nombre || mb_strlen($nombre) < 3) {
            $this->errores['nombre'] = 'El nombre tiene que tener una longitud de al menos 3 caracteres.';
        }

        $direccion = trim($datos['direccion'] ?? '');
        $direccion = filter_var($direccion, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $direccion || mb_strlen($direccion) < 5) {
            $this->errores['direccion'] = 'Introduzca una dirección correcta.';
        }

        $num_convivientes = trim($datos['num_convivientes'] ?? '');
        $num_convivientes = filter_var($num_convivientes, FILTER_SANITIZE_NUMBER_INT);
        if ( ! $num_convivientes < 0) {
            $this->errores['num_convivientes'] = 'Error al introducir el número de convivientes.';
        }

        $tipo_vivienda = trim($datos['tipo_vivienda'] ?? '');

        $dedicacion = trim($datos['dedicacion'] ?? '');
        $dedicacion = filter_var($dedicacion, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $dedicacion || mb_strlen($dedicacion) <= 0) {
            $this->errores['dedicacion'] = 'En caso de no tener dedicación, poner "NADA"';
        }

        $terraza = trim(isset($_POST['terraza']) ?? Usuario::NO_TIENE_TERRAZA);

        $num_mascotas = trim($datos['num_mascotas'] ?? '');
        $num_mascotas = filter_var($num_mascotas, FILTER_SANITIZE_NUMBER_INT);
        if ( ! $num_mascotas < 0) {
            $this->errores['num_mascotas'] = 'Introduzca un número válido de mascotas (mayor o igual que 0)';
        }

        $telefono = trim($datos['telefono'] ?? '');
        $telefono = filter_var($telefono, FILTER_SANITIZE_NUMBER_INT);
        if ( ! $telefono || mb_strlen($telefono) < 5) {
            $this->errores['telefono'] = 'Introduzca un número de teléfono válido.';
        }

        $m2_vivienda = trim($datos['m2_vivienda'] ?? '');
        $m2_vivienda = filter_var($m2_vivienda, FILTER_SANITIZE_NUMBER_INT);
        if ( ! $m2_vivienda || mb_strlen($m2_vivienda) < 0) {
            $this->errores['m2_vivienda'] = 'El nombre tiene que tener una longitud de al menos 5 caracteres.';
        }

        $ok = $_FILES['archivo']['error'] == UPLOAD_ERR_OK && count($_FILES) == 1;
        if (! $ok ) {
            /*$this->errores['archivo'] = 'Error al subir el archivo';
            return;*/
            //$usuarioaux = Usuario::buscaUsuario($_SESSION['email']);
        }
        else
        {
            $nombreImg = $_FILES['archivo']['name'];

            //Validacion nombre archivo nose porque no funciona check file name
            //echo self::check_file_uploaded_name($nombreImg);
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
        


        if (count($this->errores) === 0) {
            $usuario = Usuario::buscaPorId($_SESSION['id']);
            $archivo=' ';
            $archivo = $usuario->getImagen();
            if($ok){
                $idUsuario = $usuario->getId();
                $fichero = "{$idUsuario}";
                $fichero .= ".{$extension}";
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_USUARIOS, $fichero]);
                $usuario->setImagen($fichero);
                $usuario->guarda();

                if(!move_uploaded_file($tmp_name, $rutaImg)){
                    $this->errores['archivo'] = 'Error al mover el archivo';
                }

            $imagen = null;
            $usuario->modificaUsuario($email, $nombre, $direccion, $num_convivientes, $tipo_vivienda, $dedicacion, $terraza, $num_mascotas, $telefono, $m2_vivienda, $imagen);
            $usuario->guarda();
            $_SESSION['nombre'] = $usuario->getNombre();
            $_SESSION['email'] = $usuario->getEmail();

            }
            else{
                $usuario->modificaUsuario($email, $nombre, $direccion, $num_convivientes, $tipo_vivienda, $dedicacion, $terraza, $num_mascotas, $telefono, $m2_vivienda, $archivo);
                $usuario->guarda();
                $_SESSION['nombre'] = $usuario->getNombre();
                $_SESSION['email'] = $usuario->getEmail(); 
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