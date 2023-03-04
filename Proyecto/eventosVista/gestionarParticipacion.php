<?php
namespace es\ucm\fdi\aw\eventos;

use es\ucm\fdi\aw\protectora\Protectora;

require_once dirname(__DIR__,1).'/includes/config.php';

function mostrarFormularioParticipar(){
    if(Participa::buscaMisProtectorasNoParticipantes(Protectora::ACTIVA, $_SESSION['id'], $_GET['id'])){
        $formRegistro = new FormularioRegistroParticipacion();
        $htmlFormRegistro = $formRegistro->gestiona();
    }else{
        $htmlFormRegistro="<p> Parece que todas las protectoras con las que colaboras ya participan en este evento... </p>";
    }
    return $htmlFormRegistro;
}

function mostrarFormularioEliminar(){
    if(Participa::buscaMisProtectorasParticipantes(Protectora::ACTIVA, $_SESSION['id'], $_GET['id'])){
        $formElimina = new FormularioEliminaParticipacion();
        $htmlFormElimina = $formElimina->gestiona();
    }else{
        $htmlFormElimina="<p> Parece que ninguna de las protectoras con las que colaboras participan en este evento... O quizás la protectora con la que participas sea la creadora del evento. </p>";
    }
    return $htmlFormElimina;
}

$evento=Evento::buscaPorId($_GET['id'])->getTitulo();
$idEvento=$_GET['id'];

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

$tituloPagina = 'Gestion Participacion';
$htmlFormRegistro=mostrarFormularioParticipar();
$htmlFormElimina=mostrarFormularioEliminar();
if (isset($_SESSION["login"])) {
    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">
        <h1>Gestionar Participacion a $evento</h1>
        <h2>Participar</h2>
        $htmlFormRegistro
        <h2>Dejar de Participar</h2>
        $htmlFormElimina        
        <div class="pie-form">
            <a href='evento.php?id=$idEvento' >Volver al evento $evento</a>
        </div>
    </div>
    
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';