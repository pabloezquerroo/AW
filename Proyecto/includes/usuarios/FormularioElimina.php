<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\Formulario;

class FormularioElimina extends Formulario
{
    public function __construct() {
        parent::__construct('formElimina', ['urlRedireccion' => '../index.php']);
    }
    
    protected function generaCamposFormulario(&$datos)
    {
        $id = $_SESSION['id'];
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['email', 'nombre', 'password', 'password2'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="id" type="hidden" name="id" value="$id" />
            </div>
            <div>
                <button type="submit" name="id">Dar de baja</button>
            </div>
        EOF;

        return $html;
    }
    

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];
        
        $id = $_SESSION['id'];
        

        if (count($this->errores) === 0) {
            $usuario = Usuario::buscaPorId($id);

            if (!$usuario) {
                $this->errores['id'] = 'El usuario no existe';
            }
            else {
                $usuario->borrate();

                unset($_SESSION['id']);
                unset($_SESSION['email']);
                unset($_SESSION['login']);
                unset($_SESSION['esAdmin']);
                unset($_SESSION['nombre']);
    
                session_destroy();
            }
            
        }
    }
}