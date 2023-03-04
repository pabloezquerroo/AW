<?php

function mostrarRegistroProtectora() {
	$rutaApp = RUTA_APP;
	$html='';
	if (isset($_SESSION["login"]) && ($_SESSION["login"]===true)) {
		return "<a id=\"botonRegistroProtectora\" href='{$rutaApp}/protectoraVista/registroProtectora.php'>Registrar Protectora</a>";
	}
	return $html;
}
if (!isset($contenidoSideBarDer)){
	$contenidoSideBarDer='';
}
$contenidoSideBarDer.= mostrarRegistroProtectora();
?>
<aside id="sidebarDer">
	<?= $contenidoSideBarDer?>
</aside>
