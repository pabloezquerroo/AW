<?php
namespace es\ucm\fdi\aw\animales;

use es\ucm\fdi\aw\protectora\Protectora;
require_once dirname(__DIR__,1).'/includes/config.php';



$form = new FormularioRegistroAnimal($_GET['id']);
$htmlFormRegistro = $form->gestiona();
$tituloPagina = 'Registro';
$protectora= Protectora::buscaPorId($_GET['id']);
$nombreProtectora= $protectora->getNombre();
$contenidoPrincipal = <<<EOS
<div class="inicioSesion">
    <h1>Registro de animal en la protectora $nombreProtectora </h1>
    $htmlFormRegistro
</div>
EOS;

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
