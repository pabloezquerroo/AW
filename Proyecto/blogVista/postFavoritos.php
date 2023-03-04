<?php
	namespace es\ucm\fdi\aw\blog;
	use es\ucm\fdi\aw\protectora\Colabora;
    use es\ucm\fdi\aw\protectora\Protectora;
    use es\ucm\fdi\aw\usuarios\Usuario;
	require_once dirname(__DIR__,1).'/includes/config.php';

	$tituloPagina = 'Mis Favoritos';
	$contenidoSideBarIzq = '';
	$contenidoSideBarDer = '';
	$contenidoPrincipal = '';
	$rutaApp = RUTA_APP;

	function mostrarBlog(){
		$postPorPagina = 2;
		$pagina = 1;

		if (isset($_GET["pagina"])) {
			$pagina = $_GET["pagina"];
		}

		# El límite es el número de productos por página
		$limit = $postPorPagina;
		# El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
		$offset = ($pagina - 1) * $postPorPagina;

		$idUsuario = $_SESSION['id'];
		$aux = Like::buscaPostGustadosPorUsuario($idUsuario, $limit, $offset);

		if($aux){
			$likes = array_reverse($aux);
		}

		$conteo = Like::cuentaPostGustadosPorUsuario($idUsuario);
		$paginas = ceil($conteo / $limit);
		$html = ' ';
		if($likes){
			$html .= "<div class=\"listaPosts\">";
			foreach($likes as $like){
				$idPost = $like->getIdPost();
				$post = Post::buscaPorId($idPost);
				$titular = $post->getTitular();
				$descripcion = $post->getDescripcion();$pertenece = false;
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
							<h2><a href='post.php?p=$idPost'>$titular</a></h2>
							<p>$descripcion</p>
							<span>$creador<span>
						</div>";
			}

			$html .= "</div>
				<ul class=\"paginacion\">";
			if ($pagina > 1) { 
				$aux = $pagina - 1; 
				$html .= "<li class=\"numPaginacion\">
					<a href='postFavoritos.php?pagina=$aux'>
						Anterior
					</a>
				</li>";
			}

			if($paginas > 1){
				for ($x = 1; $x <= $paginas; $x++) {
					$html .= "<li class=\"numPaginacion\">
					<a href='postFavoritos.php?pagina=$x'>$x</a>
					</li>";
				}
			}

			if ($pagina < $paginas) { 
				$aux = $pagina + 1; 
				$html .= "<li class=\"numPaginacion\">
				<a href='postFavoritos.php?pagina=$aux'>
						Siguiente
					</a>
				</li>";
			}
			$html .= "</ul>";
		}
		else{
			$html = '<p>¡VAYA! Parece que no tienes aun publicaciones Favoritas...</p>
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
			<h1>Publicaciones Favoritas</h1>
			$auxiliar1
		</div>
		EOS;
	}

	require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>
