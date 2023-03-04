<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Formulario;

class FormularioModificaPassword extends Formulario
{
    public function __construct() {
        parent::__construct('formModificaPassword', ['urlRedireccion' => 'ajustesUsuario.php']);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $password = $datos['password'] ?? '';
        $nuevaPassword = $datos['nuevaPassword'] ?? '';
        $nuevaPassword2 = $datos['nuevaPassword2'] ?? '';

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['password', 'nuevaPassword', 'nuevaPassword2'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
        <div>
            <input id="password" type="password" name="password" value="$password" placeholder="a"/>
            <label for="password">Contraseña actual</label>
            {$erroresCampos['password']}
        </div>
        <div>
            <input id="nuevaPassword" type="password" name="nuevaPassword" value="$nuevaPassword" placeholder="a"/>
            <label for="nuevaPassword">Nueva contraseña</label>
            {$erroresCampos['nuevaPassword']}
        </div>
        <div>
            <input id="nuevaPassword2" type="password" name="nuevaPassword2" value="$nuevaPassword2" placeholder="a"/>
            <label for="nuevaPassword2">Confirma la nueva contraseña</label>
            {$erroresCampos['nuevaPassword2']}
        </div>
        <p id="errorPass"></p>
        <div>
            <button type="submit" name="registro">Actualiza password</button>
        </div>
        EOF;

        return $html;
    }
    

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $password = trim($datos['password'] ?? '');
        $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $password || empty($password) ) {
            $this->errores['password'] = 'El password no puede estar vacío.';
        }

        $nuevaPassword = trim($datos['nuevaPassword'] ?? '');
        $nuevaPassword = filter_var($nuevaPassword, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nuevaPassword || mb_strlen($nuevaPassword) < 5 ) {
            $this->errores['nuevaPassword'] = 'El password tiene que tener una longitud de al menos 5 caracteres.';
        }

        $nuevaPassword2 = trim($datos['nuevaPassword2'] ?? '');
        $nuevaPassword2 = filter_var($nuevaPassword2, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $nuevaPassword2 || $nuevaPassword != $nuevaPassword2 ) {
            $this->errores['nuevaPassword'] = 'Los passwords deben coincidir';
        }


        if (count($this->errores) === 0) {
            $usuario = Usuario::buscaPorId($_SESSION['id']);
            if (!$usuario->compruebaPassword($password)){
                $this->errores['password'] = 'El password actual introducido no es correcto.';
            }else {
                $usuario->cambiaPassword($nuevaPassword);
                $usuario->guarda();
            } 
        }
    }
}