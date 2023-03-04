<?php
namespace es\ucm\fdi\aw\usuarios;

require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioModificaPassword();
$htmlFormModificaPassword = $form->gestiona();

$tituloPagina = 'Modifica Password Usuario';

if (isset($_SESSION["login"])) {
    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">    
        <h1> Modifica password </h1>
                $htmlFormModificaPassword
    </div>
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';