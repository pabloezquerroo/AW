<?php
namespace es\ucm\fdi\aw\eventos;
use es\ucm\fdi\aw\protectora\Protectora;

require_once dirname(__DIR__,1).'/includes/config.php';

$tituloPagina = 'Eventos';

$contenidoPrincipal = '';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';
$rutaUs = RUTA_USUARIO_VISTA;

if (!isset($_SESSION["login"])) {
	$contenidoPrincipal .= <<<EOS
	<div class="contenidoNoLogueado">
		<h1>¿Eres nuevo?</h1>
		<p>¡Hola! Para formar parte de esta comunidad amante de los animales y poder acceder a todas
		las funcionalidades de la página debes estar registrado.</p>
		<h2>¡¡¡Bienvenido!!!</h2>
		<ul>
			<li class="botonRegistro"><a href='{$rutaUs}/registro.php'>Registrarse</a></li><li><a href='{$rutaUs}/login.php'>Ya tengo cuenta</a>
		</ul>
	</div>
	EOS;
} else {
	$contenidoPrincipal .= <<<EOS
	<div class="seccion">
		<h1>Eventos</h1>
		<div class="row">
			<div class="col"></div>
			<div class="col-7"> <div id="CalendarioWeb"> </div> </div>
			<div class="col"></div>
		</div>
	</div>
	EOS;
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';