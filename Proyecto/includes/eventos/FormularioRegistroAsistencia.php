<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Formulario;

class FormularioRegistroAsistencia extends Formulario
{
    public function __construct() {
        $id = $_GET['id'];
        parent::__construct('formRegistroAsistir', ['urlRedireccion' =>  "evento.php?id=$id"]);
    }

    protected function generaCamposFormulario(&$datos)
    {
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['asistir'], $this->errores, 'span', array('class' => 'error'));

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="asistir" type="hidden" name="asistir"/>
            </div>
            <div>
                <button type="submit" name="asis">Asistir</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];
        if (count($this->errores) === 0) {
            Asiste::crea($_GET['id'], $_SESSION['id']);
        }
    }
}
