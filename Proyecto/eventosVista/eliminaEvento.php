<?php

namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\protectora\Protectora;

require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioEliminaEvento();
$htmlFormEliminaEvento = $form->gestiona();

$tituloPagina = 'Elimina Evento';

$idEvento = $_GET['id'];

if (isset($_SESSION["login"])) {
    if(Participa::esMiProtectoraCreadorEvento(Protectora::ACTIVA, $_SESSION['id'], $idEvento) || $_SESSION["esAdmin"]===true){
        $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
        <h1> Dar de baja Evento </h1>
        <p> ¿Estás seguro de quierer dar de baja el evento? </p>
        <div class="opcionConfirmacion"> 
        $htmlFormEliminaEvento
        <a href='evento.php?id=$idEvento'>Cancelar</a>
        </div>
        </div>
        EOS;
    }else{
        $contenidoPrincipal = '<h1>Contenido exclusivo del creador del evento</h1>';
    }
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
