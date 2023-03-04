<?php

namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\protectora\Protectora;

require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioModificaEvento($_GET['id']);
$htmlFormModifica = $form->gestiona();
$tituloPagina = 'Modifica Evento';

if (isset($_SESSION["login"])) {
    $id = $_GET['id'];
    if(Participa::esMiProtectoraCreadorEvento(Protectora::ACTIVA, $_SESSION['id'], $id) || $_SESSION["esAdmin"]===true){
    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">
        <h1>Modifica datos del Evento</h1>
        $htmlFormModifica
        <div class="pie-form">
            <a href='./eliminaEvento.php?id=$id'>Dar de baja evento</a>
        </div>
    </div>
    EOS;
    }else{
        $contenidoPrincipal = '<h1>Contenido exclusivo del creador del evento</h1>';
    }
}
else{
    $contenidoPrincipal = ' ';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';

?>