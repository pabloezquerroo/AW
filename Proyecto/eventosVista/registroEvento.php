<?php
namespace es\ucm\fdi\aw\eventos;
use es\ucm\fdi\aw\protectora\Protectora;

require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioRegistrarEvento();
$htmlFormRegistrarEvento = $form->gestiona();

$tituloPagina = 'Registra Evento';
if (isset($_SESSION["login"])) {
    if (Protectora::buscaMisProtectorasSinLimit(Protectora::ACTIVA, $_SESSION['id'])){
        $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
            <h1>Nuevo Evento</h1>
            $htmlFormRegistrarEvento
        </div>
        EOS;
    }
    else {
        $contenidoPrincipal = '<h1>Contenido exclusivo para los colaboradores de protectoras</h1>';
    }
}
else{
    $contenidoPrincipal = <<<EOS
	<h1>¿Eres nuevo?</h1>
	<p>¡Hola! Para formar parte de esta comunidad amante de los animales y poder acceder a todas
	las funcionalidades de la página debes estar registrado.</p>
	<h2>¡¡¡Bienvenido!!!</h2>
	<ul>
		<li><a href='{$rutaApp}/registro.php'>Registrarse</a></li><li><a href='{$rutaApp}/login.php'>Ya tengo cuenta</a>
	</ul>
	EOS;
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';

?>