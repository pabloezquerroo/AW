<?php
    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\Formulario;

    class FormularioEliminaPost extends Formulario
    {
        private $idPost;

        public function __construct() {
            parent::__construct('FormularioEliminaPost', ['urlRedireccion' => 'blog.php']);
            $this->idPost = $_GET['p'];
        }

        /* Genera el formulario */
        protected function generaCamposFormulario(&$datos)
        {
            // Se generan los mensajes de error si existen.
            $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
            $erroresCampos = self::generaErroresCampos(['idPost'], $this->errores, 'span', array('class' => 'error'));
            
            $html = <<<EOF
            $htmlErroresGlobales
                <div>
                    <input id="idPost" type="hidden" name="idPost" value=$this->idPost />
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
                $post = Post::buscaPorId($this->idPost);
                $post->borrate();
            }
        }
    }
?>
