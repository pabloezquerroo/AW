<?php

namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioEliminaProtectora();
$htmlFormEliminaProtectora = $form->gestiona();

$tituloPagina = 'Elimina Protectora';

$idProtectora = $_GET['id'];

if (isset($_SESSION["login"])) {
    if(Colabora::isColaboraOrCreadorProtectora($_SESSION['id'],$idProtectora) || $_SESSION["esAdmin"]===true){
        $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
            <h1> Dar de baja protectora </h1>
            <p> ¿Estás seguro de quierer dar de baja la protectora? </p>
            <div class="opcionConfirmacion"> 
                $htmlFormEliminaProtectora
                <a href='protectora.php?id=$idProtectora'>Cancelar</a>
            </div>
        </div>
        EOS;
    }
    else {
        $contenidoPrincipal = '<h1>Contenido exclusivo para los creadores de la protectora.</h1>';
    }
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
