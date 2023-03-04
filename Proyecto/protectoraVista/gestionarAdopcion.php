<?php

namespace es\ucm\fdi\aw\protectora;
use es\ucm\fdi\aw\animales\Animal;
use es\ucm\fdi\aw\usuarios\Usuario;
use es\ucm\fdi\aw\animales\FormularioAceptaAdopcion;
use es\ucm\fdi\aw\animales\FormularioCancelarAdopcion;
require_once dirname(__DIR__,1).'/includes/config.php';

$form = new FormularioEliminaProtectora();
$htmlFormEliminaProtectora = $form->gestiona();

$tituloPagina = 'Elimina Protectora';

$idUsuario = $_GET['idU'];
$idAnimal = $_GET['idA'];
$idProtectora = $_GET['idP'];

$nombreUsuario = Usuario::buscaPorId($idUsuario)->getNombre();
$nombreAnimal = Animal::buscaPorID($idAnimal)->getNombre();

$formAcepta = new  FormularioAceptaAdopcion($idAnimal, $idUsuario);
$htmlFormAceptaAdopcion = $formAcepta->gestiona();

$formCancela = new  FormularioCancelarAdopcion($idAnimal, $idUsuario);
$htmlFormCancelaAdopcion = $formCancela->gestiona();

if (isset($_SESSION["login"])) {
    if(Colabora::isColaboraOrCreadorProtectora($_SESSION['id'],$idProtectora)){
        $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
            <h1> Gestion de adopcion </h1>
            <p> ¿Quieres que se realice la adopcion entre $nombreUsuario y $nombreAnimal? </p>
            <div class="opcionConfirmacion"> 
                $htmlFormAceptaAdopcion
                $htmlFormCancelaAdopcion
            </div>
        </div>
        EOS;
    }
    else {
        $contenidoPrincipal = '<h1>Contenido exclusivo para los colaboradores de la protectora.</h1>';
    }
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
