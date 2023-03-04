<?php

    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\Formulario;

    class FormularioEliminaComentario extends Formulario
    {
        private $idComentario;

        public function __construct($idComentario) {
            $comentario = Comentario::BuscaPorId($idComentario);
            $idPost = $comentario->getIdPost();
            parent::__construct('FormularioEliminaComentario', ['urlRedireccion' => "post.php?p=$idPost"]);
            $this->idComentario = $idComentario;
        }

        /* Genera el formulario */
        protected function generaCamposFormulario(&$datos)
        {

            // Se generan los mensajes de error si existen.
            $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
            $erroresCampos = self::generaErroresCampos(['idComentario'], $this->errores, 'span', array('class' => 'error'));
            $html = <<<EOF
            $htmlErroresGlobales
                <div>
                    <input id="idComentario" type="hidden" name="idComentario" value=$this->idComentario />
                </div>
                <div>
                    <button type="submit" name="borrar">Borrar</button>
                </div>
            EOF;
            return $html;
        }

        protected function procesaFormulario(&$datos)
        {
            $this->errores = [];
            if (count($this->errores) === 0) {
                $comentario = Comentario::BuscaPorId($this->idComentario);
                $comentario->borrate();
            }
        }
    }

?>
