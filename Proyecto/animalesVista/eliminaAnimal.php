<?php
namespace es\ucm\fdi\aw\animales;
use es\ucm\fdi\aw\protectora\Protectora;

require_once dirname(__DIR__,1).'/includes/config.php';


$form = new FormularioEliminaAnimal($_GET['id']);
$htmlFormElimina = $form->gestiona();

$tituloPagina = 'Elimina Usuario';

$idAnimal = $_GET['id'];
$animal = Animal::buscaPorID($idAnimal);
$idProtectora = $animal->getProtectora();

if (isset($_SESSION["login"])) {
    if(Protectora::perteneceMisProtectoras(Protectora::ACTIVA, $_SESSION['id'], $idProtectora) || $_SESSION["esAdmin"]===true || Adopta::esAnimalAdoptadoUsuario($_SESSION['id'], $_GET['id'])){
        $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
            <h1> Dar de baja </h1>
            <p> ¿Estás seguro de querer eliminar el animal? </p>
            <div class="opcionConfirmacion">     
                $htmlFormElimina
                <a href='animal.php?id=$idAnimal'>Cancelar</a>
            </div>
        </div>
        EOS;
    }
    else {
        $contenidoPrincipal = '<h1>Contenido exclusivo para las protectoras que gestionan al animal</h1>';
    }
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';