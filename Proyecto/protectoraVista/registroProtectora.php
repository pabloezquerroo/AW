<?php
namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioRegistroProtectora();
$htmlFormRegistro = $form->gestiona();

$tituloPagina = 'RegistroProtectora';

if (isset($_SESSION["login"])) {
    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">
        <h1>Registro de protectoras</h1>
        $htmlFormRegistro
    </div>
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin la sesión iniciada</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>