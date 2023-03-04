<?php

namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioModificaProtectora($_GET['id']);
$htmlFormModifica = $form->gestiona();
$tituloPagina = 'Modifica Protectora';

if (isset($_SESSION["login"])) {
    $id = $_GET['id'];
    if(Colabora::isColaboraOrCreadorProtectora($_SESSION['id'],$id) || $_SESSION["esAdmin"]===true){
        $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
            <h1>Modifica datos de Protectora</h1>
            $htmlFormModifica
            <div class="pie-form">
                <a href='./eliminaProtectora.php?id=$id'>Dar de baja protectora</a>
            </div>
        </div>
        EOS;
    }
    else {
        $contenidoPrincipal = '<h1>Contenido exclusivo para los creadores de la protectora.</h1>';
    }
}
else{
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';

?>