<?php
namespace es\ucm\fdi\aw\usuarios;

use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\protectora\FormularioValidaProtectora;
require_once dirname(__DIR__,1).'/includes/config.php';


$tituloPagina = 'AdministrarProtectoras';

function mostrarProtectoras(){
	$protectorasPorPagina = 2;
    $pagina = 1;

    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }
    # El límite es el número de productos por página
    $limit = $protectorasPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $protectorasPorPagina;
	$conteo = Protectora::cuentaEstadoInactivo();

	$paginas = ceil($conteo / $limit);
    $protectoras = Protectora::buscaPorEstado(Protectora::PENDIENTE, $offset, $limit); 
    $html = ' ';
	if($protectoras){
		$html .= "<div class=\"listaSeccion\">";
		foreach($protectoras as $protectora){
			$nombre = $protectora->getNombre();
			$id = $protectora->getId();
			$telefono = $protectora->getTelefono();
			$email = $protectora->getEmail();
			$direccion = $protectora->getDireccion();
			$descripcion = $protectora->getDescripcion();

			$formValida = new FormularioValidaProtectora($id);
			$BotonCambioEstado = $formValida->gestiona();

			   $html .= "<div class=\"contenedorLista\">
			   			<div>
						   <h2>$nombre</h2>
						   <span>$email</span>
						   <div>$telefono</div>
						   <div>$direccion</div>
						   <p>$descripcion</p>
						</div>
						$BotonCambioEstado
						<a href='../protectoraVista/eliminaProtectora.php?id=$id'>Dar de baja protectora</a>
					   </div>"; 
		}
		$html .= "</div>
            <ul class=\"paginacion\">";

		if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./admin.php?pagina=$aux'>
                    Anterior
                </a>
            </li>";
        }

		if($paginas > 1){
        	for ($x = 1; $x <= $paginas; $x++) {
            	$html .= "<li class=\"numPaginacion\">
                	<a href='./admin.php?pagina=$x'>$x</a>
            	</li>";
        	}
		}

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./admin.php?pagina=$aux'>
                    Siguiente
                </a>
            </li>";
        }
		$html .= "</ul>";
	}
	else{
        $html = '<p>Parece que no hay protectoras pendientes de validar en este momento...</p>';
    }
    
    return $html;
}


$auxiliar = mostrarProtectoras();
if (isset($_SESSION["esAdmin"]) && ($_SESSION["esAdmin"]===true)) {
	$contenidoPrincipal = <<<EOS
	<div class="seccion">
		<h1>Validar protectoras</h1>
		$auxiliar
	</div>
	EOS;
}
else {
		$contenidoPrincipal = '<h1>Contenido no accesible sin la sesión de admin iniciada</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>

