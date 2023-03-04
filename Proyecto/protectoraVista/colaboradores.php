<?php

namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';

use es\ucm\fdi\aw\usuarios\Usuario;

function mostrarColaboradoesActivos(){
    $idProtectora = $_GET['id'];
    $protectorasPorPagina = 2;
    $pagina = 1;
    
    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }
    # El límite es el número de productos por página
    $limit = $protectorasPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $protectorasPorPagina;

    $conteo = Colabora::cuentaMiembrosProtectora($idProtectora);
	$paginas = ceil($conteo / $limit);
    
    
    $colaboradores = Colabora::buscaMiembrosProtectora($idProtectora,  $offset, $limit); 
    $html = ' ';
    if($colaboradores){
        $html .= "<div class=\"listaSeccion\">";
        foreach($colaboradores as $colaborador){
            $rol = ' ';
            $usuario = Usuario::buscaPorId($colaborador->getIdUsuario());
            $nombreColaborador = $usuario->getNombre();
            $idColaborador = $colaborador->getIdUsuario()/*$usuario->getId()*/;
            $emailColaborador = $usuario->getEmail();
            $rolColaborador = 'Colaborador';
            if(Colabora::CREADOR == $colaborador->getRol()){
                $rolColaborador = 'Creador';
            }

            //if ($_SESSION['id'] != $colaborador->getIdUsuario()) {
                $rol = $colaborador->getRol();
                $form = new FormularioModificaColaborador($idProtectora, $idColaborador);
                
                if ($_SESSION['id'] != $colaborador->getIdUsuario()){
                    $htmlFormModifica = $form->gestiona();
                    $enlaceEliminaColaborador = "<a href='./eliminaColaborador.php?id=$idProtectora&idu=$idColaborador'>Eliminar Colaborador</a>";
                }
                else{
                    $htmlFormModifica = '';
                    $enlaceEliminaColaborador = '';
                }
                
                $html .= "<div class=\"contenedorLista\">
                            <div>
                                <h2>$nombreColaborador </h2>
                                <span>$emailColaborador </span>
                            </div>
                            $htmlFormModifica
                            $enlaceEliminaColaborador
                        </div>"; 
           //}
        }
        $html .= "</div>
            <ul class=\"paginacion\">";

        if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./colaboradores.php?pagina=$aux&id=$idProtectora'>
                    Anterior
                </a>
            </li>";
        }

		if($paginas > 1){
        	for ($x = 1; $x <= $paginas; $x++) {
            	$html .= "<li class=\"numPaginacion\">
                	<a href='./colaboradores.php?pagina=$x&id=$idProtectora'>$x</a>
            	</li>";
        	}
		}

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./colaboradores.php?pagina=$aux&id=$idProtectora'>
                    Siguiente
                </a>
            </li>";
        }
        $html .= "</ul>";

    }else{
        $html .= "<p>Parece que no hay colaboradores...</p>";
    }
    return $html;
}
    
$listaColaboradores = mostrarColaboradoesActivos();

$tituloPagina = 'Colaboradores';
if (isset($_SESSION["login"])) {
    $protectora= Protectora::buscaPorId($_GET['id']);
    $nombreProtectora= $protectora->getNombre();
    $contenidoPrincipal = <<<EOS
    <div class="seccion">
        <h1>Colaboradores de $nombreProtectora</h1>
        $listaColaboradores
    </div>
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
