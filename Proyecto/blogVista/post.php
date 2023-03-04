<?php
    namespace es\ucm\fdi\aw\blog;
    use es\ucm\fdi\aw\protectora\Colabora;
    use es\ucm\fdi\aw\protectora\Protectora;
    use es\ucm\fdi\aw\usuarios\Usuario;
    require_once dirname(__DIR__,1).'/includes/config.php'; 

    $tituloPagina = 'Post';

    $contenidoSideBarIzq = '';
    $contenidoSideBarDer = '';

    $rutaApp = RUTA_APP;

    
    function mostrarImgCreador($pertenece, $imagen){
        $htmlImagenes = ' ';
            if($pertenece){
                //Si el creador es una protectora
                if($imagen){
                    $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS_MOSTRAR, $imagen]);
                    $htmlImagenes = "<img src=\"$rutaImg\" id=\"imgCreador\"></a>";
                }
                else{
                    $rutaImagenes = "pordefecto.jpeg";
                    $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS_MOSTRAR, $rutaImagenes]);
                    $htmlImagenes = "<img src=\"$rutaImg\" id=\"imgCreador\"></a>";
                }
            }
            else{
                //Si el creador es un usuario 
                if($imagen){
                    $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_USUARIOS_MOSTRAR, $imagen]);
                    $htmlImagenes = "<img src=\"$rutaImg\" id=\"imgCreador\"></a>";
                }
                else{
                    $rutaImagenes = "pordefecto.jpeg";
                    $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_USUARIOS_MOSTRAR, $rutaImagenes]);
                    $htmlImagenes = "<img src=\"$rutaImg\" id=\"imgCreador\"></a>";
                }
            }
        
        return $htmlImagenes;
    }

    function mostrarPost(){

        $idPost = $_GET['p'];
        $post = Post::buscaPorId($idPost); 
        $html = ' ';
        
        if($post){
            $titular = $post->getTitular();
            $descripcion = $post->getDescripcion();
            $fechaCrea = $post->getFecha();

            /*img*/
            $imagenpost = $post->getImagen();
            $ImgPost = '';
            if($imagenpost){
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_POST_MOSTRAR, $imagenpost]);
                $ImgPost = "<img src=\"$rutaImg\" id=\"imgUsuario\"></a>";
            }

            /*Aqui compruebo quien ha creado el post si un usuario o una protectora */
            $pertenece = false;
            $idUsuario = $post->getIdUsuario();
            $idProtectora = $post->getIdProtectora();

            /*Si lo ha creado una protectora compruebo si el usuario que visita el post colabora con ella*/
            if($idProtectora){
                $pertenece = Colabora::isColaboraOrCreadorProtectora($idUsuario, $idProtectora); 
            }

            /*Aqui asigno el nombre del creador a la variable creador */
            $user = Usuario::buscaPorId($idUsuario);
            $creador = $user->getNombre();
            $imagen = $user->getImagen();
            if($pertenece){
                $protectora = Protectora::buscaPorId($idProtectora);
                $creador = $protectora->getNombre();
                $imagen = $protectora->getImagen();
            }
            
            $enlaceModifica = '';
            $enlaceElimina = '';
            $idPost = $_GET['p'];

            if($_SESSION["esAdmin"]===true || $_SESSION['id'] == $idUsuario || Colabora::isColaboraOrCreadorProtectora($_SESSION['id'], $idProtectora)){
                $enlaceModifica = "<div><a href= './modificaPost.php?p=$idPost' id=\"botonModifica\">Modificar</a></div>";
                $enlaceElimina = "<div><a href= './eliminaPost.php?p=$idPost' id=\"botonModifica\">Eliminar</a></div>";
            }

            $enlaceLike = '';
            $form = new FormularioLikePost($idPost);
            if(isset($_SESSION['login'])){
                $enlaceLike = $form->gestiona();
            }

            $cntLikes = Like::buscalikesPost($idPost);
            if($cntLikes){$cntLikes = sizeof($cntLikes);}else{$cntLikes=0;}

            $htmlImagenes = mostrarImgCreador($pertenece, $imagen);

            $html .= "<div id='contenedorPost'>
                        $ImgPost
                        <div id=\"infoPost\">
                            <h1>$titular</h1>
                            <span>
                                $htmlImagenes $creador
                            </span>
                            <div>Creado el $fechaCrea</div>
                            <p>$descripcion</p>
                            <div id='conteoLikes'>$cntLikes Likes</div>
                            $enlaceLike 
                        </div>
                    </div>
                    <div id=\"botones\">
                        $enlaceModifica
                        $enlaceElimina
                    </div>";
        }
        else{
            $html = '<p>Parece que hubo un error al mostrar el post...</p>'; 
        }
        return $html;
    }

    function mostrarComentario($comentario){
        $html = '';

        $descripcion = $comentario->getDescripcion();
        $idUsuario = $comentario->getIdUsuario();
        $fecha = $comentario->getFecha();
        $id = $comentario->getId();

        $usuario = Usuario::buscaPorId($idUsuario);
        $nombre = $usuario->getNombre();

        $enlaceModifica = '';
        $enlaceElimina = ' ';

        if($_SESSION["esAdmin"]===true || $_SESSION['id'] == $idUsuario){
            $enlaceElimina = " <a href='eliminaComentario.php?c=$id' >Eliminar</a> ";
            $enlaceModifica = " <a href='modificaComentario.php?c=$id' >Modificar</a>";
        }

        $html = "<div id='contenedorComentario'>
                    <h3>$nombre</h3>
                    <p>$descripcion</p>
                    <div>Creado en $fecha</div>
                    <div id='pieComentario'>
                        $enlaceElimina
                        $enlaceModifica
                    </div>
                </div>";

        return $html;
    }
    
    function mostrarComentarios(){
        $idPost = $_GET['p'];
        $html = '';
        $comentarios = false;
        $aux = Comentario::BuscaComentariosPost($idPost);
        if($aux){
            $comentarios = array_reverse($aux);
        }
        $form = new FormularioCreaComentario();
        $enlaceCreaComentario = $form->gestiona();

        if($comentarios){
            $html .= "<div id='contenedorComentarios'>
                        $enlaceCreaComentario
                        <h2> Comentarios </h2>
                        <div id='comentarios'>";
                
            foreach($comentarios as $comentario){
                $html .= mostrarComentario($comentario);
            }
            $html .= "</div>";
        }
        else{
            $html = " <div id='contenedorComentarios'>
            $enlaceCreaComentario
            <p>¡VAYA! Parece que no hay comentarios aun en esta publicacion...</p>
            </div>";
        }

        return $html;
    }

    if (!isset($_SESSION["login"])) {
        $contenidoPrincipal = "<h1>Contenido no accesible sin la sesión iniciada</h1>";
    } 
    else {
        $auxiliar1 = mostrarPost();
        $auxiliar2 = mostrarComentarios();
        $contenidoPrincipal = <<<EOS
        <div class="seccion">
            $auxiliar1
            $auxiliar2
        </div>
        EOS;
    }

    require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>