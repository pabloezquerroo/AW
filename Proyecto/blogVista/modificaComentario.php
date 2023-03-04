<?php

    namespace es\ucm\fdi\aw\blog;
    require_once dirname(__DIR__,1).'/includes/config.php';

    $idComentario=$_GET['c'];
    $form = new FormularioModificaComentario();
    $htmlFormModifica = $form->gestiona();
    $comentario = Comentario::BuscaPorId($idComentario);

    $tituloPagina = 'Modifica Comentario';

    if($comentario){
        
        $idUsuario = $comentario->getIdUsuario();

        if (isset($_SESSION["login"])) {
            if(isset($_SESSION['esAdmin']) || $_SESSION['id'] == $idUsuario){
                    $contenidoPrincipal = <<<EOS
                <div class="inicioSesion">
                    <h1>Modificar Comentario</h1>
                    $htmlFormModifica
                    <div class="pie-form">
                        <a href='./eliminaComentario.php?c=$idComentario'>Eliminar Comentario</a>
                    </div>
                </div>
                EOS;
            }
            else{
                $contenidoPrincipal = '<h1>No puedes modificar un comentario del que no eres propietario</h1>';
            }
        }
        else{
            $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
        }
    }
    else{
        $contenidoPrincipal = '<h1>Contenido inaccesible, el comentario que desea modificar no existe </h1>';
    }
    

    require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>