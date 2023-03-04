<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Formulario;
/* */
class FormularioEliminaProtectora extends Formulario
{

    public function __construct() {
        parent::__construct('FormularioEliminaProtectora', ['urlRedireccion' => 'protectoras.php']);
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $id = $_GET['id'];

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['id'], $this->errores, 'span', array('class' => 'error'));
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="id" type="hidden" name="id" value=$id />
            </div>
            <div>
                <button type="submit" name="borrar">Dar de baja</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $id = $_GET['id'];

        if (count($this->errores) === 0) {
            $protectora = Protectora::buscaPorId($id);
            $protectora->borrate();
        }
    }
}

?>
