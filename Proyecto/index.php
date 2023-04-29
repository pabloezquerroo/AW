<?php

require_once __DIR__.'/includes/config.php';

$tituloPagina = 'Portada';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';


$contenidoPrincipal = <<<EOS
<div id="index">
    <h1>Pet2Safe</h1>
    <p> Comunidad de protectoras y amigos de los animales. 
    Si quieres estar al tanto de los animales en adopción, 
    los ya adoptados y los eventos benéficos, este es tu sitio.</p>
</div>
EOS;

require __DIR__.'/includes/vistas/plantillas/plantilla.php';
