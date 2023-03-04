<?php
namespace es\ucm\fdi\aw\protectora;

use es\ucm\fdi\aw\animales\Animal;
use es\ucm\fdi\aw\animales\Adopta;

require_once dirname(__DIR__,1).'/includes/config.php';

$tituloPagina = 'Solicitudes Adopcion';

function mostrarsolicitudesAdopcion() {
    $idProtectora = $_GET['id'];
    $solicitudesAdopcionPorPagina = 3;
    $pagina = 1;
    
    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }
    # El límite es el número de productos por página
    $limit = $solicitudesAdopcionPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $solicitudesAdopcionPorPagina;

    $animales = Animal::buscaPorProtectoraSinLimit($idProtectora);
    if($animales){
        $html = ' ';
        foreach($animales as $animal){
            $idAnimal = $animal->getId();
            $nombreAnimal = $animal->getNombre();
            $conteo = Adopta::cuentaSolicitudesAdopcion($idAnimal);
            $paginas = ceil($conteo / $limit);
            $usuarios =  Adopta::buscaUsuarioPendiente($idAnimal);
            
            if($usuarios) {
                $html .= " <div class=\"listaSeccion\">";
                foreach($usuarios as $usuario) {
                    $nombre = $usuario->getNombre();
                    $email = $usuario->getEmail();
                    $direccion = $usuario->getDireccion();
                    $num_convivientes = $usuario->getNumConvivientes();
                    $tipo_vivienda = $usuario->getTipoVivienda();
                    $dedicacion = $usuario->getDedicacion();
                    $terraza = $usuario->getTerraza();
                    $num_mascotas = $usuario->getNumMascotas();
                    $telefono = $usuario->getTelefono();
                    $m2_vivienda = $usuario->getM2Vivienda();
                    $idUsuario = $usuario->getId();

        
                    if ($terraza == 1){
                        $terraza = "con";
                    }
                    else {
                        $terraza = "sin";
                    }
    
        
                    if (isset($_SESSION['id'])){
                        if ( Adopta::existeProcesoAdopcion($usuario->getId(), $idAnimal) &&  Colabora::buscaColaborador($_SESSION['id'], $idProtectora))
                        {
                            $html .= "<div class=\"contenedorLista\">
                            <a href='../animalesVista/animal.php?id=$idAnimal'><h2>Adopcion de $nombreAnimal </h2></a>
                            <h3> $nombre </h3>
                            <div id=\"contenidoContenedorLista\">
                                <div id=\"infoGeneral\">
                                    <span>$email </span>
                                    <div>$direccion </div>
                                    <div>$telefono </div>
                               </div>
                                <ul id=\"requisitos\">
                                    <li>Se dedica a ser $dedicacion.</li>
                                    <li>Vive en un/una $tipo_vivienda de $m2_vivienda m² $terraza terraza.</li>
                                    <li>Convive con $num_convivientes personas.</li>
                                    <li>Actualmente tiene $num_mascotas mascotas.</li>
                                </ul>
                            </div>
                            <div>
                                <a href='./gestionarAdopcion.php?idU=$idUsuario&idA=$idAnimal&idP=$idProtectora'> Gestionar adopción </a>
                            </div>  
                        </div>";
                        }
                    }
                }
                $html .= "</div>";
            }
            else {
                $html = '<p>Parece que no hay solicitudes de adopcion...</p>';
            } 
        }
        
    }
    else {
        $html = '<p>Parece que la protectora no tiene animales para adoptar...</p>';
    } 
	return $html;
}


$auxiliar = mostrarsolicitudesAdopcion();
$protectora= Protectora::buscaPorId($_GET['id']);
$nombreProtectora= $protectora->getNombre();
if (isset($_SESSION["login"]) && Colabora::isColaboraOrCreadorProtectora($_SESSION['id'], $_GET['id'])) {
	$contenidoPrincipal = <<<EOS
	<div class="seccion">
		<h1>Solicitudes de Adopcion de $nombreProtectora</h1>
		$auxiliar
	</div>
	EOS;
}
else {
		$contenidoPrincipal = '<h1>Contenido no accesible, debes ser creador y estar logeado.</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>
