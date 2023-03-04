<?php
namespace es\ucm\fdi\aw\mensajes;
use es\ucm\fdi\aw\Formulario;
use es\ucm\fdi\aw\animales\Animal;
class FormularioTrasladoAnimal extends Formulario
{

    public function __construct() {
        $idPro = $_GET['id'];
        $idC = $_GET['chat'];
        parent::__construct('formTrasladoAnimal', ['urlRedireccion' => 'mensajes.php?id='.$idPro.'&chat='.$idC, 'method' => 'POST']);
    }

    protected function generaCamposAnimales(&$datos){
        $idUsu= $_SESSION['id'];
        $idPro = $_GET['id'];
        $animales = Animal::misAnimales($idUsu); 
        $html = '';
        if($animales){
            $html .= '<select name= "animal" class="selectbox">';
            foreach($animales as $animal){
                if($idPro == $animal->getProtectora()){
                $nombre = $animal->getNombre();
                $idAnimal= $animal->getId();
                
                $html .= "<option value = $idAnimal> $nombre</opcion>";
                }
            }
            $html .= "</select>";
        }
        return $html;
    }

    protected function generaCamposFormulario(&$datos){
        $animales = $datos['animales'] ?? self::generaCamposAnimales($datos);
        $mensaje = "";
        $idPro = $_GET['id'];
        $idC = $_GET['chat'];
        $urlCancel = 'mensajes.php?id='.$idPro.'&chat='.$idC;
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['animales','mensaje'], $this->errores, 'span', array('class' => 'error'));

        $html = <<<EOF
        $htmlErroresGlobales
            <div class="campoSelectbox">
                <label>Animales:</label>
                $animales
            </div>
            <div class="campoModifica">
                <label for="mensaje">¿Alguna información adicional?</label>
                <input type=text id="mensaje" name="mensaje" value="$mensaje" />
            </div>
            <div class="opcionConfirmacion">
                <button type="submit" name="aplicar">Aplicar</button>    
                <input class="traslado" type="button" name="cancelar" value = "Cancelar" onclick=" location.href='mensajes.php?id=$idPro&chat=$idC'">
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];
        $mensaje = trim($datos['mensaje']??'');
        $mensaje = filter_var($mensaje,FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(!isset($datos['animal'])){
            $this->errores['animales'] = "ningun animal seleccionado";
        }
        if(count($this->errores) === 0 && isset($datos['aplicar'])){
            if(!Mensaje::existePeticion($_GET['id'],$_GET['chat'], $datos['animal']))
                Mensaje::enviarMensaje($_GET['id'],$_GET['chat'],$mensaje, Mensaje::PETICION, $datos['animal']);
            else
            $this->errores[] = "ya existe una petición por este animal";
        }

    }
}