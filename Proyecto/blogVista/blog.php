<?php
	namespace es\ucm\fdi\aw\blog;
	use es\ucm\fdi\aw\protectora\Colabora;
    use es\ucm\fdi\aw\protectora\Protectora;
    use es\ucm\fdi\aw\usuarios\Usuario;
	require_once dirname(__DIR__,1).'/includes/config.php';

	$tituloPagina = 'Blog';
	$contenidoSideBarIzq = '';
	$contenidoSideBarDer = '';
	$contenidoPrincipal = '';
	$rutaApp = RUTA_APP;

	function mostrarBlog(){
		$aux = Post::buscaPosts();
		$blog = false;
		if($aux){
			$blog = array_reverse($aux);
		}
		$html = ' ';
		if($blog){
			$html .= "<div id=\"listaPosts\">";
			foreach($blog as $post){
				$titular = $post->getTitular();
				$descripcion = $post->getDescripcion();
				$id = $post->getId();
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
				if($pertenece){
					$protectora = Protectora::buscaPorId($idProtectora);
					$creador = $protectora->getNombre();
				}
				
				
				$html .= "<div class=\"contenedorLista\">
							<h2><a href='post.php?p=$id'>$titular</a></h2>
							<p>$descripcion</p>
							<span>$creador<span>
						</div>";
			}

			$html .= "</div>";
		}
		else{
			$html = '<p>¡VAYA! Parece que no hay post que mostrar...</p>
					</a>';
		}
		return $html;
	}

	if (!isset($_SESSION["login"])) {
		$rutaUsuarioVista = RUTA_USUARIO_VISTA;
		$contenidoPrincipal .= <<<EOS
		<div class="contenidoNoLogueado">
			<h1>¿Eres nuevo?</h1>
			<p>¡Hola! Para formar parte de esta comunidad amante de los animales y poder acceder a todas
			las funcionalidades de la página debes estar registrado.</p>
			<h2>¡¡¡Bienvenido!!!</h2>
			<ul>
				<li class="botonRegistro"><a href='{$rutaUsuarioVista}/registro.php'>Registrarse</a></li><li><a href='{$rutaUsuarioVista}/login.php'>Ya tengo cuenta</a>
			</ul>
		</div>
		EOS;
	} else {
		$auxiliar1 = mostrarBlog();
		$contenidoPrincipal .= <<<EOS
		<div class="seccion">
			<h1>Blog</h1>
			<a href='registroPost.php' class="botonRegistro">Nuevo Post</a>
			$auxiliar1
		</div>
		EOS;
	}

	require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>
