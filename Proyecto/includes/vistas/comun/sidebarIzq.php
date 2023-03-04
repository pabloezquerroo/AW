<?php
namespace es\ucm\fdi\aw;
use es\ucm\fdi\aw\protectora\Colabora;
use es\ucm\fdi\aw\blog\Like;
$rutaApp = RUTA_APP;
$rutaProtectorasVista = RUTA_PROTECTORA_VISTA;
$rutaUsuarioVista = RUTA_USUARIO_VISTA;
$rutaBlogVista = RUTA_BLOG_VISTA;

if (isset($_SESSION["esAdmin"]) && ($_SESSION["esAdmin"]===true)) {
	if (!isset($contenidoSideBarIzq)){
		$contenidoSideBarIzq='';
	}
	$contenidoSideBarIzq .=<<<EOF
	<li><a href='$rutaUsuarioVista/admin.php'>Administrar protectoras</a></li>
	EOF;
}
if (isset($_SESSION['login'])&& Colabora::isColaboraOrCreadorActiva($_SESSION['id'])){
	if (!isset($contenidoSideBarIzq)){
		$contenidoSideBarIzq='';
	}
	$contenidoSideBarIzq .= <<<EOF
	<li><a href='$rutaProtectorasVista/misProtectoras.php'>Mis Protectoras</a></li>
	EOF;
}
if (isset($_SESSION['login']) && Like::cuentaPostGustadosPorUsuario($_SESSION['id'])){
	if (!isset($contenidoSideBarIzq)){
		$contenidoSideBarIzq='';
	}
	$contenidoSideBarIzq .= <<<EOF
	<li><a href='$rutaBlogVista/postFavoritos.php'>Mis Favoritos</a></li>
	EOF;
}
if (!isset($contenidoSideBarIzq)){
	$contenidoSideBarIzq='';
}else{
	$contenidoSideBarIzq .= ' ';
}

?>
<nav id="sidebarIzq">
<ul id="ulSideBarIzq">
<?= $contenidoSideBarIzq?>
</ul>
</nav>

