<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Formulario;


class FormularioEliminaAsistencia extends Formulario
{
    
    
    public function __construct() {
        $idEvento = $_GET['id'];
        parent::__construct('FormulaAsistencia', ['urlRedireccion' => "evento.php?id=$idEvento"]);
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $idUsuario = $_SESSION['id'];
        $idEvento = $_GET['id'];

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['idUsuario', 'idEvento'], $this->errores, 'span', array('class' => 'error'));
        
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="idUsuario" type="hidden" name="idUsuario" value=$idUsuario />
            </div>
            <div>
                <input id="idEvento" type="hidden" name="idEvento" value=$idEvento />
            </div>
            <div>
                <button type="submit" name="borrar">Dejar de asistir</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $idUsuario = $_SESSION['id'];
        $idEvento = $_GET['id'];
        
        if (count($this->errores) === 0) {
            $asiste = Asiste::esAsistenteEvento($idUsuario, $idEvento);

            if(!$asiste){
                $this->errores['idUsuario'] = 'no ha podido darse de baja ';
            }
            else{
                Asiste::eliminaAsistenteEvento($idEvento, $idUsuario);
            }
        }
    }
}

?>
