<?php

namespace es\ucm\fdi\aw\usuarios;

require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioElimina();
$htmlFormElimina = $form->gestiona();

$tituloPagina = 'Elimina Usuario';

if (isset($_SESSION["login"])) {
    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">
        <h1> Dar de baja </h1>
        <p>¿Estás seguro de quierer dar de baja tu cuenta? </p>
        <div class="opcionConfirmacion">
            $htmlFormElimina
            <a href='ajustesUsuario.php'>Cancelar</a>
        </div>
    </div>
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1> ';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';