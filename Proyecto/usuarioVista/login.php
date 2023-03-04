<?php

namespace es\ucm\fdi\aw\usuarios;
require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioLogin();
$htmlFormLogin = $form->gestiona();

$tituloPagina = 'Login';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

if (!isset($_SESSION["login"])) {
    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">
        <h1>Acceso al sistema</h1>
        $htmlFormLogin
        <div class="pie-form">
            <a href="registro.php">¿No tienes Cuenta? Registrate</a>
        </div>
    </div>
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible con la sesión iniciada</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
