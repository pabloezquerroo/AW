<?php
namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';

function mostrarMisProtectoras(){
    $protectorasPorPagina = 2;
    $pagina = 1;

    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }
    # El límite es el número de productos por página
    $limit = $protectorasPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $protectorasPorPagina;
	$conteo = Protectora::cuentaMisProtectoras();
    $paginas = ceil($conteo / $limit);
    $protectoras = Protectora::buscaMisProtectoras(Protectora::ACTIVA, $_SESSION['id'],$offset, $limit); 
    $html = ' ';
    if($protectoras){
        $html .= "<div class=\"listaSeccion\">";
        foreach($protectoras as $protectora){
            $nombre = $protectora->getNombre();
            $idProtectora = $protectora->getId();
            $email = $protectora->getEmail();
            $descripcion  = $protectora->getDescripcion();

            $imagen=$protectora->getImagen();
            $htmlImagenes = ' ';
            if ($imagen){
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS_MOSTRAR, $imagen]);
                $htmlImagenes = "<a href='./protectora.php?id=$idProtectora'><img src=\"$rutaImg\" class=\"imgLista\"></a>                        
                </a>";
            }
            else{
                $imagen = "pordefecto.jpeg";
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS_MOSTRAR, $imagen]);
                $htmlImagenes = "<a href='./protectora.php?id=$idProtectora'><img src=\"$rutaImg\" class=\"imgLista\"></a>                        
                </a>";
            }
            
            //if (es\ucm\fdi\aw\Colabora::buscaColaborador($_SESSION['id'],$idProtectora) && $protectora->isActiva()) {
                $html .= "<div class=\"contenedorLista\">
                            <div>
                                $htmlImagenes
                                <a href='./protectora.php?id=$idProtectora'><h2>$nombre</h2></a>
                                <span>$email</span>
                            </div>
                        </div>"; 
            //}
        }
        $html .= "</div>
            <ul class=\"paginacion\">";

        if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./misProtectoras.php?pagina=$aux'>
                    Anterior
                </a>
            </li>";
        }

        if($paginas >1){
            for ($x = 1; $x <= $paginas; $x++) {
                $html .= "<li class=\"numPaginacion\">
                    <a href='./misProtectoras.php?pagina=$x'>$x</a>
                </li>";
            }
        }

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./misProtectoras.php?pagina=$aux'>
                    Siguiente
                </a>
            </li>";
        }
        $html .= "</ul>";

    }else{
        $html .= "<p>Parece que no colaboras en ninguna protectora...</p>";
    }
    return $html;
}
    
$listaMisProtectoras = mostrarMisProtectoras();

$tituloPagina = 'Mis Protectoras';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

if (isset($_SESSION["login"])) {
    $contenidoPrincipal = <<<EOS
    <div class="seccion">
        <h1>Mis Protectoras</h1>
        $listaMisProtectoras
    </div>
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
