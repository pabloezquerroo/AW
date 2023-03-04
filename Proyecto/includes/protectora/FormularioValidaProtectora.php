<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioValidaProtectora extends Formulario
{
    private $idProtectora;
    
    public function __construct($idProtectora) {
        parent::__construct('formValidaProtectora', ['urlRedireccion' => 'admin.php']);
        $this->idProtectora = $idProtectora;
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $id = $datos['id'] ?? $this->idProtectora;

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['id'], $this->errores, 'span', array('class' => 'error'));
        
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="id" type="hidden" name="id" value=$id />
            </div>
            <div>
                <button type="submit" name="registro">Registrar</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $id = $datos['id'] ?? $this->idProtectora;

        if (count($this->errores) === 0) {
            $protectora = Protectora::buscaPorId($id);
            $protectora = Protectora::crea($protectora->getNombre(), $protectora->getTelefono(), $protectora->getEmail(), $protectora->getDireccion(), $protectora->getDescripcion(), $protectora->getImagen(),  $id);
        }
    }
}

?>
