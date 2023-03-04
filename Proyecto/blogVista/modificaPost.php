<?php

    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\protectora\Colabora;
    require_once dirname(__DIR__,1).'/includes/config.php';

    $idPost=$_GET['p'];
    $form = new FormularioModificaPost();
    $htmlFormModifica = $form->gestiona();
    $post = Post::buscaPorId($idPost);

    $tituloPagina = 'Modifica Post';

    if($post){
        
        $pertenece = false;
        $idUsuario = $post->getIdUsuario();

        if($idProtectora = $post->getIdProtectora()){
           $pertenece = Colabora::isColaboraOrCreadorProtectora($_SESSION['id'], $idProtectora); 
        }

        if (isset($_SESSION["login"])) {
            if($_SESSION["esAdmin"]===true || $_SESSION['id'] == $idUsuario || $pertenece){
                    $contenidoPrincipal = <<<EOS
                <div class="inicioSesion">
                    <h1>Modificar Post</h1>
                    $htmlFormModifica
                    <div class="pie-form">
                        <a href='./eliminaPost.php?p=$idPost'>Eliminar Publicacion</a>
                    </div>
                </div>
                EOS;
            }
            else{
                $contenidoPrincipal = '<h1>No puedes modificar una publicacion de la que no eres propietario</h1>';
            }
        }
        else{
            $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
        }
    }
    else{
        $contenidoPrincipal = '<h1>Contenido inaccesible, la publicacion que desea modificar no existe </h1>';
    }
    

    require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>