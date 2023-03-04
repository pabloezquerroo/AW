<?php
/* nose si esta bien asi */
namespace es\ucm\fdi\aw\animales;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioCancelarAdopcion extends Formulario
{
    private $idAnimal;
    private $idUsuario;
    
    public function __construct($idAnimal, $idUsuario) {
        parent::__construct('formCancela', ['urlRedireccion' => '../animalesVista/animales.php']);
        $this->idAnimal = $idAnimal;
        $this->idUsuario = $idUsuario;
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $id =  $this->idAnimal;
        $idUsuario = $this->idUsuario;

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['idAnimal', 'idUsuario'], $this->errores, 'span', array('class' => 'error'));
        
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="idAnimal" type="hidden" name="idAnimal" value=$id />
            </div>
            <div>
                <input id="idUsuario" type="hidden" name="idUsuario" value=$idUsuario />
            </div>
            <div>
                <button type="submit" name="adopcion">Cancelar adopción</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $id = $this->idAnimal;
        $idUsuario = $this->idUsuario;
        error_log($idUsuario);

        if (count($this->errores) === 0) {
            $adopta = Adopta::eliminaProcesoAdopcion($id, $idUsuario);
        }
    }
}

?>