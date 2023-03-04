<?php
namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';

$tituloPagina = 'SolicitudesColabora';

function mostrarColaboradoresPendientes(){
	$idProtectora = $_GET['id'];
    $colaboradoresPorPagina = 1;
    $pagina = 1;
    
    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }
    # El límite es el número de productos por página
    $limit = $colaboradoresPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $colaboradoresPorPagina;

	$conteo = Colabora::cuentaSolicitudesColabora($idProtectora);
	$paginas = ceil($conteo / $limit);
    $colaboradores = Colabora::buscaColaboradoresPendientesPorProtectora($_GET['id'], $offset, $limit); 
	$html = ' ';
	if($colaboradores){
		$html .= "<div class=\"listaSeccion\">";
		foreach($colaboradores as $colaborador){
			$nombre = $colaborador->getNombre();
			$email = $colaborador->getEmail();
			$id = $colaborador->getId();
			$telefono = $colaborador->getTelefono();
            $formValida = new FormularioValidaColaborador($_GET['id'], $id);
			$BotonCambioEstado = $formValida->gestiona();
			$formElimina = new FormularioEliminaColaborador($id);
			$htmlFormEliminaProtectora = $formElimina->gestiona();
			   //Metemos el rol tambien del colaborador, en este caso pendiente.
                $html .= "<div class=\"contenedorLista\">
			   			<div>
						   <h2>$nombre</h2>
						   <span>$email</span>
						</div>
						<div class=\"opcionConfirmacion\">
							$BotonCambioEstado
							$htmlFormEliminaProtectora
						</div>
					</div>"; 
		}
		$html .= "</div>
		<ul class=\"paginacion\">";

		if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./solicitudesColabora.php?pagina=$aux&id=$idProtectora'>
                    Anterior
                </a>
            </li>";
        }

		if($paginas > 1){
        	for ($x = 1; $x <= $paginas; $x++) {
            	$html .= "<li class=\"numPaginacion\">
                	<a href='./solicitudesColabora.php?pagina=$x&id=$idProtectora'>$x</a>
            	</li>";
        	}
		}

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./solicitudesColabora.php?pagina=$aux&id=$idProtectora'>
                    Siguiente
                </a>
            </li>";
        }
		$html .= "</ul>";
	}
	else{
        $html = '<p>Parece que no hay solicitudes para colaborar...</p>';
    }
    
    return $html;
}


$auxiliar = mostrarColaboradoresPendientes();
$protectora= Protectora::buscaPorId($_GET['id']);
$nombreProtectora= $protectora->getNombre();
if (Colabora::isCreador($_SESSION['id'], $_GET['id']) || $_SESSION["esAdmin"]===true) {
	$contenidoPrincipal = <<<EOS
	<div class="seccion">
		<h1>Solicitud Colaboradores de $nombreProtectora</h1>
		$auxiliar
	</div>
	EOS;
}
else {
		$contenidoPrincipal = '<h1>Contenido no accesible, debes ser creador y estar logeado.</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>
