<?php
/* nose si esta bien asi */
namespace es\ucm\fdi\aw\animales;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioEliminaAnimal extends Formulario
{
    private $idAnimal;

    public function __construct($idAnimal) {
        parent::__construct('FormularioEliminaAnimal', ['urlRedireccion' => 'animales.php']);
        $this->idAnimal = $idAnimal;
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $id = $datos['id'] ?? $this->idAnimal;

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['id'], $this->errores, 'span', array('class' => 'error'));
        //FALTA EL ID URL DE LA PROTECTORA
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

        $id = $datos['id'] ?? $this->idAnimal;

        if (count($this->errores) === 0) {
            $animal = Animal::buscaPorId($id);
            $animal->borrate();
        }
    }
}

?>
