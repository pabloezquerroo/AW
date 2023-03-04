<?php
    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\protectora\Colabora;
    require_once dirname(__DIR__,1).'/includes/config.php'; 
    
    $form = new FormularioEliminaPost();
    $htmlFormElimina = $form->gestiona();
    $idPost = $_GET['p'];
    $post = Post::buscaPorId($idPost);

    $tituloPagina = 'Elimina Post';

    if($post){
        $pertenece = false;
        $idUsuario = $post->getIdUsuario();

        if($idProtectora = $post->getIdProtectora()){
           $pertenece = Colabora::isColaboraOrCreadorProtectora($_SESSION['id'], $idProtectora); 
        }

        if (isset($_SESSION["login"])) {
            if($_SESSION["esAdmin"]===true || $_SESSION['id'] == $idUsuario || $pertenece){  //Comprobar si el usuario el propietario del post
                $contenidoPrincipal = <<<EOS
                <div class="inicioSesion">
                    <h1> Eliminar Publicacion </h1>
                    <p> ¿Estás seguro de querer eliminar la publicacion? </p>
                    <div class="opcionConfirmacion">     
                        $htmlFormElimina
                        <a href='post.php?p=$idPost'>Cancelar</a>
                    </div>
                </div>
                EOS;
            }
            else{
                $contenidoPrincipal = '<h1>No puedes eliminar una publicacion de la que no eres propietario</h1>';
            }
        }
        else {
            $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
        }
    }
    else{
        $contenidoPrincipal = '<h1>Contenido inaccesible, la publicacion que desea eliminar no existe </h1>';
    }

    require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>