<?php
function mostrarSaludo() {
	$rutaApp = RUTA_APP;
	$html='';
	$rutaUs = RUTA_USUARIO_VISTA;
	//$form = new es\ucm\fdi\aw\ FormularioLogin();
	//$htmlFormLogin = $form->gestiona();

	if (isset($_SESSION["login"]) && ($_SESSION["login"]===true)) {
		$html = "
		<div>
			Bienvenido, {$_SESSION['nombre']} <a href='{$rutaUs}/logout.php'>(Salir)</a>
		</div>
		<a href='{$rutaUs}/perfil.php'>Perfil Usuario</a>
		";
	} else {
		$html = "<a href='{$rutaUs}/login.php'>Login</a>
				<a href='{$rutaUs}/registro.php'>Registro</a>";
	}
	return $html;
}

function mostrarNavegacion(){
	$rutaUs = RUTA_ANIMALES_VISTA;
	$rutaEv = RUTA_EVENTOS_VISTA;
	$rutaPr = RUTA_PROTECTORA_VISTA;
	$rutaBl = RUTA_BLOG_VISTA;
	$html = "
	<li><a class=\"botonSeccion\" href=\"{$rutaUs}/animales.php\">Animales</a></li>
	<li><a class=\"botonSeccion\" href=\"{$rutaEv}/eventos.php\">Eventos</a></li>
	<li><a class=\"botonSeccion\" href=\"{$rutaPr}/protectoras.php\">Protectoras</a></li>
	<li><a class=\"botonSeccion\" href=\"{$rutaBl}/blog.php\">Blog</a></li>

	";
	return $html;
}

?>
<header>
	<div class="parte-superior-header">
		<a href=/AW-SW/Proyecto/index.php><img id="logo" src="/AW-SW/Proyecto/img/Pet2safe.svg" alt="Logo"></a>
		<div class="saludo">
			<?= mostrarSaludo() ?>
		</div>
	</div>
	<nav class="navegacion-secciones">
		<ul class="navegacion">
			<?= mostrarNavegacion() ?>
		</ul>
	</nav>
</header>