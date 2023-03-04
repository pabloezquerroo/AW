<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\protectora\Protectora;

class FormularioRegistroParticipacion extends Formulario
{
    public function __construct() {
        $id=$_GET['id'];
        parent::__construct('formRegistroParticipar', ['urlRedireccion' => "gestionarParticipacion.php?id=$id"]);
    }
    
    protected function camposProtectora(){
        $misProtectorasNoParticipantes = Participa::buscaMisProtectorasNoParticipantes(Protectora::ACTIVA, $_SESSION['id'], $_GET['id']);
        $name="protectoras";
        $html="<select name=\"$name\" class=\"selectbox\">";
            foreach($misProtectorasNoParticipantes as $protectora){
                $nombreProtectora=$protectora->getNombre();
                $idProtectora=$protectora->getId();
                $html.= "<option value=\"$idProtectora\" >$nombreProtectora</option>";
            }
        $html.="</select>";
        return $html;
    }

    protected function generaCamposFormulario(&$datos)
    {
        $protectoras=self::camposProtectora();

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['protectoras'], $this->errores, 'span', array('class' => 'error'));

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                $protectoras
                {$erroresCampos['protectoras']}
            </div>
            <div>
                <button type="submit" name="participar">Participar</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];

        $protectora=trim($datos['protectoras'] ?? '');

        if (count($this->errores) === 0) {
           Participa::crea($_GET['id'], $protectora);
        }
    }
}
