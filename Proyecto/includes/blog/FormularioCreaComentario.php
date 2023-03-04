<?php

    namespace es\ucm\fdi\aw\blog;

    use Error;
    use es\ucm\fdi\aw\Formulario;

    class FormularioCreaComentario extends Formulario
    {

        private $idUsuario; //los dos id deben ir en el formulario como hidden o no hace falta
        private $idPost;

        public function __construct() {
            $this->idPost = $_GET['p'];
            parent::__construct('formModifica', ['urlRedireccion' => "post.php?p=$this->idPost"]);
            $this->idUsuario = $_SESSION['id'];   
        }

        /* Genera el formulario */
        protected function generaCamposFormulario(&$datos)
        {
            $descripcion = $datos['descripcion'] ?? '';

            // Se generan los mensajes de error si existen.
            $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);
            $erroresCampos = self::generaErroresCampos(['descripcion'], $this->errores, 'span', array('class' => 'error'));

            $html = <<<EOF
            $htmlErroresGlobales
            <div class="campoModifica">
                <label for="descripcion" id='labelComentario'>Comentario</label>
                <textarea name="descripcion" rows="5" cols="50" placeholder="a">
                $descripcion
                </textarea>
                {$erroresCampos['descripcion']}
            </div>
            <div class="campoRegistro">
                <button type="submit" name="registro">Publicar</button>
            </div>
            EOF;

            return $html;
        }

        /* Gestion de los valores del formulario */
        protected function procesaFormulario(&$datos)
        {
            $this->errores = [];

            $descripcion = trim($datos['descripcion'] ?? '');
            $descripcion = filter_var($descripcion, FILTER_SANITIZE_STRING); 
            if ( ! $descripcion || mb_strlen($descripcion) < 1) {
                $this->errores['descripcion'] = 'No puedes publicar un comentario vacio';
            }
            
            //Si no ha habido errores con ningun campo crea el comentario.
            if (count($this->errores) === 0) {
                
                $Arrayfecha = getdate();
                $anio = $Arrayfecha['year'];
                $dia = $Arrayfecha['mday'];
                $mes = $Arrayfecha['mon'];
                $fecha = "$dia/$mes/$anio";

                $comentario = Comentario::crea($descripcion,$this->idUsuario, $this->idPost, $fecha);
                
                if(!$comentario){
                    $this->errores[] = "No ha podido publicarse el comentario correctamente.";
                }
            } 
        }
    }
?>