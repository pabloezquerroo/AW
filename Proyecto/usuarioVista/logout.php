<?php

require_once dirname(__DIR__,1).'/includes/config.php';

//Doble seguridad: unset + destroy
unset($_SESSION['id']);
unset($_SESSION['email']);
unset($_SESSION['login']);
unset($_SESSION['esAdmin']);
unset($_SESSION['nombre']);


session_destroy();

$tituloPagina = 'Logout';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

$contenidoPrincipal = <<<EOS
<h1>Hasta pronto!</h1>
EOS;

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>