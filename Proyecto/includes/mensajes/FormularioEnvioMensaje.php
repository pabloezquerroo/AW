<?php
/* nose si esta bien */
namespace es\ucm\fdi\aw\mensajes;

use es\ucm\fdi\aw\Formulario;
/* */

class FormularioEnvioMensaje extends Formulario{

    const TIPOS_MENSAJE = ['MENSAJE', 'PETICION'];

    public function __construct(){
        $idPro = $_GET['id'];
        $idC = $_GET['chat'];
        parent::__construct('formEnviaMensaje', ['urlRedireccion' => 'mensajes.php?id='.$idPro.'&chat='.$idC]);
    }

    protected function generaCamposFormulario(&$datos){
        $mensaje = $datos['mensaje'] ?? '';

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['mensaje'], $this->errores, 'span', array('class' => 'error'));
        $id = $_GET['id'];
        $chat = $_GET['chat'];
        $html = <<<EOF
        $htmlErroresGlobales
        <div>
            <input type=text id="mensaje" name="mensaje" value="$mensaje" />
            {$erroresCampos['mensaje']}
            <button type="submit" name="enviar">Enviar</button>
            <input class="traslado" type="button" onclick=" location.href='trasladoAnimal.php?id=$id&chat=$chat' " value="Trasladar Animal" name="traslado" />
        </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos){
        $this->errores = [];
        $mensaje = trim($datos['mensaje']??'');
        $mensaje = filter_var($mensaje,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(isset($datos['traslado'])){

        }
        else{
            if($mensaje){
                Mensaje::enviarMensaje($_GET['id'],$_GET['chat'],$mensaje, Mensaje::MENSAJE);
            }
        }
    }

}