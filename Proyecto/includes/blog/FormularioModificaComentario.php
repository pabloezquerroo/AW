<?php

    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\Formulario;

    class FormularioModificaComentario extends Formulario
    {
        private $idComentario;

        public function __construct(){
            $this->idComentario = $_GET['c'];
            $idPost = Comentario::BuscaPorId($this->idComentario)->getIdPost();
            parent::__construct('formModifica', ['enctype' => 'multipart/form-data', 'urlRedireccion' => "post.php?p=$idPost"]);        
        }

        protected function generaCamposFormulario(&$datos)
        {
            error_log("$this->idComentario");
            $comentario = Comentario::buscaPorId($this->idComentario);
            
            if (!$comentario){  //Si no existe el comentario
                $descripcion = $datos['descripcion'] ?? '';
            }else{  // Si existe el comentario
                $descripcion = $datos['descripcion'] ?? $comentario->getDescripcion();
            }
        
            // Se generan los mensajes de error si existen.
            $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
            $erroresCampos = self::generaErroresCampos(['descripcion'], $this->errores, 'span', array('class' => 'error'));

            $html = <<<EOF
            $htmlErroresGlobales
            <div class="campoModifica">
                <textarea name="descripcion" rows="5" cols="50">
                $descripcion
                </textarea>
                <label for="descripcion">Descripcion</label>
                {$erroresCampos['descripcion']}
            </div>
            <div class="campoModifica">
                <button type="submit" name="modificar">Modificar</button>
            </div>
            EOF;

            return $html;
        }

        protected function procesaFormulario(&$datos)
        {
            $this->errores = [];

            $descripcion = trim($datos['descripcion'] ?? '');
            $descripcion = filter_var($descripcion, FILTER_SANITIZE_STRING); /*usar otro saneamiento, filtersanitizestring sin soporte */
            if ( ! $descripcion || mb_strlen($descripcion) < 1) {
                $this->errores['descripcion'] = 'No puedes publicar un comentario vacio';
            }

            if (count($this->errores) === 0) {
                $comentario = Comentario::buscaPorId($this->idComentario);
                $comentario->modificaComentario($descripcion);
                $comentario->guarda();
            }
        }
    }
?>