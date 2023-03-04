<?php
    namespace es\ucm\fdi\aw\blog;
    require_once dirname(__DIR__,1).'/includes/config.php'; 
    
    $idComentario = $_GET['c'];
    $form = new FormularioEliminaComentario($idComentario);
    $htmlFormElimina = $form->gestiona();
    $comentario = Comentario::BuscaPorId($idComentario);
    $idPost = $comentario->getIdPost();
    
    $tituloPagina = 'Elimina Post';

    if($comentario){
        $idUsuario  = $comentario->getIdUsuario();

        if (isset($_SESSION["login"])) {
            if(isset($_SESSION['esAdmin']) || $_SESSION['id'] == $idUsuario){  //Comprobar si el usuario el propietario del post
                $contenidoPrincipal = <<<EOS
                <div class="inicioSesion">
                    <h1> Eliminar Comentario </h1>
                    <p> ¿Estás seguro de querer eliminar el comentario? </p>
                    <div class="opcionConfirmacion">     
                        $htmlFormElimina
                        <a href='post.php?p=$idPost'>Cancelar</a>
                    </div>
                </div>
                EOS;
            }
            else{
                $contenidoPrincipal = '<h1>No puedes eliminar un comentario de la que no eres propietario</h1>';
            }
        }
        else {
            $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
        }
    }
    else{
        $contenidoPrincipal = '<h1>Contenido inaccesible, el comentario que desea eliminar no existe </h1>';
    }

    require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>