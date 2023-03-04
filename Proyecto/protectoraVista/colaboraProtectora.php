<?php

namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioColaboraProtectora();
$htmlFormColabora = $form->gestiona();
$tituloPagina = 'Colabora Protectora';

if (isset($_SESSION["login"])) {
    $id = $_GET['id'];
    $protectora= Protectora::buscaPorId($_GET['id']);
    $nombreProtectora= $protectora->getNombre();

    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">
        <h1>Colaborar con $nombreProtectora</h1> 
        <p>¿Estás seguro de querer colaborar en la protectora? </p>
        <div class="opcionConfirmacion">
            $htmlFormColabora
            <a href='./protectoras.php'>Cancelar</a>
        </div>
    </div>
    EOS;
}
else{
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';

?>