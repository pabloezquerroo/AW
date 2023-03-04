<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioEliminaColaborador extends Formulario
{
    private $idUsuario;
    
    public function __construct($idUsuario) {
        $idProtectora= $_GET['id'];
        parent::__construct('FormularioEliminaColaborador', ['urlRedireccion' => "protectora.php?id=$idProtectora"]);
        $this->idUsuario = $idUsuario;
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $idProtectora = $_GET['id'];
        $idUsuario = $this->idUsuario;

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['idProtectora', 'idUsuario'], $this->errores, 'span', array('class' => 'error'));
        
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="idProtectora" type="hidden" name="idProtectora" value=$idProtectora />
            </div>
            <div>
                <input id="idUsuario" type="hidden" name="idUsuario" value=$idUsuario />
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

        $idProtectora = $_GET['id'];
        $idUsuario = $this->idUsuario;
        
        if (count($this->errores) === 0) {
            $colabora = Colabora::buscaColaborador($idUsuario, $idProtectora);

            if(!$colabora){
                $this->errores['id'] = 'no ha podido darse de baja a este colaborador.';
            }
            else{
                $colabora->borrate();
            }
        }
    }
}

?>
