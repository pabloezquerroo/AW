<?php

namespace es\ucm\fdi\aw\blog;

use Error;
use es\ucm\fdi\aw\Formulario;

class FormularioLikePost extends Formulario
{
    private $idPost;

    public function __construct($idPost) {
        parent::__construct('FormularioLikePost', ['urlRedireccion' => "post.php?p=$idPost"]); 
        /* o post al que se da megusta */
        $this->idPost = $idPost;
    }

    /* Genera el formulario */
    protected function generaCamposFormulario(&$datos)
    {
        $idUsuario = $_SESSION['id'];
        $like = Like::UserLikePost($idUsuario, $this->idPost);
        if($like){ //Si ya se habia dado like anteriormente
            $submit = "Deshacer Like";
        }
        else{ //Si no se habia dado like anteriormente
            $submit = "Like";
        }  

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
        $erroresCampos = self::generaErroresCampos(['idPost', 'idUsuario'], $this->errores, 'span', array('class' => 'error'));
        $html = <<<EOF
        $htmlErroresGlobales
            <div>
                <input id="idPost" type="hidden" name="idPost" value=$this->idPost />
            </div>
            <div>
                <input id="idUsuario" type="hidden" name="idUsuario" value=$idUsuario />
            </div>
            <div>
                <button type="submit" name="like">$submit</button>
            </div>
        EOF;
        return $html;
    }

    protected function procesaFormulario(&$datos)
    {
        $this->errores = [];
        $idUsuario = $_SESSION['id'];

        if (count($this->errores) === 0) {
            $Like = Like::UserLikePost($idUsuario, $this->idPost);

            if($Like){ //Si ya se habia dado like anteriormente
                $postLike = Like::borra($Like);
            }
            else{ //Si no se habia dado like anteriormente
                $postLike = Like::crea($this->idPost, $idUsuario);
            }  
        }
    }
}

?>
