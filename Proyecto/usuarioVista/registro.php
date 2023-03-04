<?php
namespace es\ucm\fdi\aw\usuarios;
require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioRegistro();
$htmlFormRegistro = $form->gestiona();

$tituloPagina = 'Registro';

if (!isset($_SESSION["login"])) {
    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">
        <h1>Registro de usuario</h1>
        $htmlFormRegistro
    </div>
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible con la sesión iniciada</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
