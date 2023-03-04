<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioValidaColaborador extends Formulario
{
    private $idProtectora;
    private $idUsuario;
    
    public function __construct($idProtectora, $idUsuario) {
        $aux = $idProtectora;
        parent::__construct('formValidaProtectora', ['urlRedireccion' => "solicitudesColabora.php?id=$idProtectora"]);
        $this->idProtectora = $idProtectora;
        $this->idUsuario = $idUsuario;
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $idP = $datos['idProtectora'] ?? $this->idProtectora;
        $idU = $datos['idUsuario'] ?? $this->idUsuario;

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['idProtectora', 'idUsuario'], $this->errores, 'span', array('class' => 'error'));
        
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="idProtectora" type="hidden" name="idProtectora" value=$idP />
            </div>
            <div>
                <input id="idUsuario" type="hidden" name="idUsuario" value=$idU />
            </div>    
            <div>
                <button type="submit" name="Validar">Validar</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $idP = $datos['idProtectora'] ?? $this->idProtectora;
        $idU = $datos['idUsuario'] ?? $this->idUsuario;

        if (count($this->errores) === 0) {
            $colabora = Colabora::crea($idP, $idU, Colabora::COLABORA);
        }
    }
}

?>
