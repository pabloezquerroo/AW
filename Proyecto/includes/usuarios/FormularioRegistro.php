<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Formulario;

class FormularioRegistro extends Formulario
{
    const EXTENSIONES_PERMITIDAS = array('jpg','jpeg', 'png');

    public function __construct() {
        parent::__construct('formRegistro', ['enctype' => 'multipart/form-data', 'urlRedireccion' => '../index.php']);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $nombre = $datos['nombre'] ?? '';
        $email = $datos['email'] ?? '';
        $direccion = $datos['direccion'] ?? '';
        $num_convivientes = $datos['num_convivientes'] ?? '';
        $dedicacion = $datos['dedicacion'] ?? '';
        $num_mascotas = $datos['num_mascotas'] ?? '';
        $telefono = $datos['telefono'] ?? '';
        $m2_vivienda = $datos['m2_vivienda'] ?? '';
        $archivo = $datos['archivo'] ?? null;    
        
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['email', 'nombre', 'password', 'password2', 'direccion', 'num_convivientes', 'dedicacion', 'num_mascotas', 'telefono', 'm2_vivienda', 'archivo'], $this->errores, 'span', array('class' => 'error'));
        $tieneTerraza = Usuario::TIENE_TERRAZA;

        $html = <<<EOF
        $htmlErroresGlobales
        <div class="campoRegistro">
            <input id="email" type="email" name="email" value="$email" placeholder="a" tabindex = "1"/>
            <label for="email">Correo del usuario</label>
            {$erroresCampos['email']}
            <span id="errorEmail"></span>
        </div>
        <div class="campoRegistro">
            <input id="nombre" type="text" name="nombre" value="$nombre" placeholder="a" tabindex = "2"/>
            <label for="nombre">Nombre</label>
            {$erroresCampos['nombre']}
            <span id="errorNombre"></span>
        </div>
        <div class="campoRegistro">
            <input id="password" type="password" name="password" placeholder="a" tabindex = "3"/>
            <label for="password">Contraseña</label>
            {$erroresCampos['password']}
            <span id="errorPass"></span>
        </div>
        <div class="campoRegistro">
            <input id="password2" type="password" name="password2" placeholder="a" tabindex = "4"/>
            <label for="password2">Confirma la contraseña</label>
            {$erroresCampos['password2']}
        </div>            
        <div class="campoRegistro">
            <input id="direccion" type="text" name="direccion" value="$direccion" placeholder="a" tabindex = "5"/>
            <label for="direccion">Dirección de tu domicilio</label>
            {$erroresCampos['direccion']}
            <span id="errorDirec"></span>
        </div>
        <div class="campoRegistro">
            <input id="num_convivientes" type="number" name="num_convivientes" min="0" value="$num_convivientes" placeholder="a" tabindex = "6"/>
            <label for="num_convivientes">¿Con cuántas personas vives?</label>
            {$erroresCampos['num_convivientes']}
            <span id="errorConv"></span>
        </div>
        <div class="campoSelectbox">
            <label for="tipo_vivienda">¿Vives en un piso o casa?:</label>
            <select name="tipo_vivienda" id="tipo_vivienda" class="selectbox" tabindex = "7">
                <option value="Piso">Piso</option>
                <option value="Casa">Casa</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <div class="campoRegistro">
            <input id="dedicacion" type="text" name="dedicacion" value="$dedicacion" placeholder="a" tabindex = "8"/>
            <label for="dedicacion">¿A qué te dedicas?</label>
            {$erroresCampos['dedicacion']}
            <span id="errorDedi"></span>
        </div>
        <div class="checkbox">
            <label for="terraza">Marca la casilla si tu domicilio tiene terraza:</label>
            <input type="checkbox" name="terraza" id="terraza" value="$tieneTerraza">
        </div>
        <div class="campoRegistro">
            <input id="num_mascotas" type="number" name="num_mascotas" min="0" value="$num_mascotas" placeholder="a" tabindex = "9"/>
            <label for="num_mascotas">¿Cuántas mascotas tiene?</label>
            {$erroresCampos['num_mascotas']}
            <span id="errorMasc"></span>
        </div>
        <div class="campoRegistro">
            <input id="telefono" type="number" name="telefono" min = "0" value="$telefono" placeholder="a" tabindex = "10"/>
            <label for="telefono">Teléfono</label>
            {$erroresCampos['telefono']}
            <span id="errorTel"></span>
        </div>
        <div class="campoRegistro">
            <input id="m2_vivienda" type="number" name="m2_vivienda" min = "0" value="$m2_vivienda" placeholder="a" tabindex = "11"/>
            <label for="m2_vivienda">Metros cuadrados de su vivienda</label>
            {$erroresCampos['m2_vivienda']}
            <span id="errorMviv"></span>
        </div>
        <div class="campoRegistro">
            <input type="file" name="archivo" id="archivo" />
            <label for="archivo">Archivo</label>
            {$erroresCampos['archivo']}
            <span id="errorFile"></span>
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

        $password = trim($datos['password'] ?? '');
        $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $password || mb_strlen($password) < 5 ) {
            $this->errores['password'] = 'El password tiene que tener una longitud de al menos 5 caracteres.';
        }

        $password2 = trim($datos['password2'] ?? '');
        $password2 = filter_var($password2, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $password2 || $password != $password2 ) {
            $this->errores['password2'] = 'Los passwords deben coincidir';
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
            $this->errores['dedicacion'] = 'En caso de no tener dedicación, ponga "NADA"';
        }

        $terraza = trim($datos['terraza'] ?? Usuario::NO_TIENE_TERRAZA);

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
            $this->errores['m2_vivienda'] = 'Los metros cuadrados de la vivienda deben ser válidos.';
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
            $usuario = Usuario::buscaUsuario($email);
            if ($usuario) {
                $this->errores[] = "El usuario ya existe";
            } else {
                $usuario = Usuario::crea($email, $password, $nombre, $direccion, $num_convivientes, $tipo_vivienda, $dedicacion, $terraza, $num_mascotas, $telefono, $m2_vivienda);
                $_SESSION['login'] = true;
                $_SESSION['id'] = $usuario->getId();
                $_SESSION['email'] = $usuario->getEmail();
                $_SESSION['nombre'] = $usuario->getNombre();
                
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
                }
            }

        }
        else {
            $this->errores[] = "Error de registro de usuario";
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