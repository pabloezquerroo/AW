<?php
/*nose si esta bien */
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioColaboraProtectora extends Formulario
{
    private $idProtectora;
    private $idUsuario;
    //PREGUNTAR REDIRECCION DE PAGINA PARAMETRO????
    public function __construct() {
        $this->idProtectora = $_GET['id'];
        $this->idUsuario = $_SESSION['id'];
        parent::__construct('formColabora', ['urlRedireccion' => "protectora.php?id=$this->idProtectora"]);
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $idProtectora =  $_GET['id'];
        $idUsuario = $_SESSION['id'];

        // Se generan los mensajes de error si existen
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['id', 'idUsuario'], $this->errores, 'span', array('class' => 'error'));
        
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="idProtectora" type="hidden" name="idProtectora" value=$idProtectora />
            </div>
            <div>
                <input id="idUsuario" type="hidden" name="idUsuario" value=$idUsuario />
            </div>
            <div>
                <button type="submit" name="colaborar">Colaborar</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $idProtectora = $_GET['id'];
        $idUsuario = $_SESSION['id'];

        if (count($this->errores) === 0) {
            $colabora = Colabora::crea($idProtectora, $idUsuario, Colabora::PENDIENTE);
        }
    }
}

?>
