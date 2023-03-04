<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Formulario;

class FormularioLogin extends Formulario
{
    public function __construct() {
        parent::__construct('formLogin', ['urlRedireccion' => RUTA_APP.'/index.php']);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        // Se reutiliza el nombre de usuario introducido previamente o se deja en blanco
        $email = $datos['email'] ?? '';

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['email', 'password'], $this->errores, 'span', array('class' => 'error'));

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="email" type="email" name="email" value="$email" placeholder="Correo usuario" tabindex = "1"/>
                {$erroresCampos['email']}
                <span id="errorEmail"></span>
            </div>
            <div>
                <input id="password" type="password" name="password" placeholder="Contraseña" tabindex = "2"/>
                {$erroresCampos['password']}
                <span id="errorPass"></span>
            </div>
            <div>
                <button type="submit" name="login">Entrar</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];
        $email = trim($datos['email'] ?? '');
        $email = filter_var($email, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $email || empty($email) ) {
            $this->errores['email'] = 'El correo del usuario no puede estar vacío';
        }
        
        $password = trim($datos['password'] ?? '');
        $password = filter_var($password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ( ! $password || empty($password) ) {
            $this->errores['password'] = 'El password no puede estar vacío.';
        }
        
        if (count($this->errores) === 0) {
            $usuario = Usuario::login($email, $password);
        
            if (!$usuario) {
                $this->errores[] = "El correo del usuario o el password no coinciden";
            } else {
                $_SESSION['login'] = true;
                $_SESSION['id'] = $usuario->getId();
                $_SESSION['nombre'] = $usuario->getNombre();
                $_SESSION['email'] = $usuario->getEmail();
                $_SESSION['esAdmin'] = $usuario->isAdmin();
            }
        }
    }
}
