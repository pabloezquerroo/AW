<?php

namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Formulario;

class FormularioEliminaEvento extends Formulario
{

    public function __construct() {
        parent::__construct('FormularioEliminaEvento', ['urlRedireccion' => 'eventos.php']);
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $id = $datos['id'] ?? $_GET['id'];

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['id'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="id" type="hidden" name="id" value=$id />
            </div>
            <div>
                <button type="submit" name="borrar">Eliminar</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $id = $datos['id'] ?? $_GET['id'];

        if (count($this->errores) === 0) {
            $evento = Evento::buscaPorId($id);
            $evento->borrate();
        }
    }
}

?>
