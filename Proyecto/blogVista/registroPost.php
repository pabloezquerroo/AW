<?php
    namespace es\ucm\fdi\aw\blog;
    require_once dirname(__DIR__,1).'/includes/config.php';

    $form = new FormularioCreaPost();
    $htmlFormCreaPost = $form->gestiona();

    $tituloPagina = 'Crear Publicacion';

    if (isset($_SESSION["login"])) {
        $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
            <h1>Nueva Publicacion</h1>
            $htmlFormCreaPost
        </div>
        EOS;
    }
    else {
        $contenidoPrincipal = '<h1>Contenido no accesible sin la sesión iniciada</h1>';
    }

    require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>