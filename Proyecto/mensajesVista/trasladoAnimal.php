<?php
namespace es\ucm\fdi\aw\mensajes;
require_once dirname(__DIR__,1).'/includes/config.php';

$tituloPagina = 'Traslado Animal';

$contenidoSideBarIzq = "";

$contenidoSideBarDer = "";

$form = new FormularioTrasladoAnimal();
$contenido = $form->gestiona();

$contenidoPrincipal = <<<EOS
<div class="inicioSesion">
    <h1>Traslado Animal</h1>
    $contenido
</div>
EOS;

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';