<?php
/* nose si esta bien asi */
namespace es\ucm\fdi\aw\animales;

use es\ucm\fdi\aw\Formulario;
/* */
class FormularioSolicitaAdopcion extends Formulario
{
    private $idAnimal;
    private $idUsuario;
    
    public function __construct($idAnimal) {
        parent::__construct('formColabora', ['urlRedireccion' => null]);
        $this->idAnimal = $idAnimal;
        $this->idUsuario = $_SESSION['id'];
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
                <button type="submit" name="adopcion">Solicitar adopcion</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $id = $this->idAnimal;
        $idUsuario = $this->idUsuario;

        if (count($this->errores) === 0) {
            $adopta = Adopta::crea($id, $idUsuario);
        }
    }
}

?>
