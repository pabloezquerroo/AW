<?php
namespace es\ucm\fdi\aw\animales;
use es\ucm\fdi\aw\protectora\Protectora;

require_once dirname(__DIR__,1).'/includes/config.php';


$idAnimal=$_GET['id'];
$animal = Animal::buscaPorID($idAnimal);
$idProtectora = $animal->getProtectora();
$form = new FormularioModificaAnimal($idAnimal);
$htmlFormModifica = $form->gestiona();

$tituloPagina = 'Modifica Animal';

if (isset($_SESSION["login"])) {
    if(Protectora::perteneceMisProtectoras(Protectora::ACTIVA, $_SESSION['id'], $idProtectora) || $_SESSION["esAdmin"]===true || Adopta::esAnimalAdoptadoUsuario($_SESSION['id'], $_GET['id'])){
        $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
            <h1>Modifica datos del Animal</h1>
            $htmlFormModifica
            <div class="pie-form">
                <a href='./eliminaAnimal.php?id=$idAnimal'>Dar de baja animal</a>
            </div>
        </div>
        EOS;
    }
    else {
        $contenidoPrincipal = '<h1>Contenido exclusivo para las protectoras que gestionan al animal</h1>';
    }
}else{
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>