<?php
namespace es\ucm\fdi\aw\animales;
require_once dirname(__DIR__,1).'/includes/config.php';

use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\Imagen;

$tituloPagina = 'Animales';

function removeGet($url, $get){
    $partesUrl = parse_url($url);
    $queryParams = array();
    parse_str($partesUrl['query'], $queryParams);
    unset($queryParams[$get]);
    if(isset($_GET['formId']))
        return $partesUrl['path'] . '?' . http_build_query($queryParams);
    else
        return $partesUrl['path'];
} 

function mostrarAnimales(){
    $animalesPorPagina = 9;
    $pagina = 1;
    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
        $url = $_SERVER['REQUEST_URI'];
        $url = removeGet($url, 'pagina');
    }else{
        $url = $_SERVER['REQUEST_URI'];
    }
    # El límite es el número de productos por página
    $limit = $animalesPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $animalesPorPagina;

    $filtro = filtro();

  
    $conteo = Animal::cuentaAnimalesAdoptables($filtro);
    $paginas = ceil($conteo / $limit);
    $animales = Animal::animalesAdoptables($filtro, $offset, $limit); 
    $html = ' ';
    if($animales){
        $html .= "<div class=\"listaSeccion\">";
        foreach($animales as $animal){
            $nombre = $animal->getNombre();
            $raza = $animal->getRaza();
            $edad = $animal->getEdad();
            $peso = $animal->getPeso();
            $genero = $animal->getGenero();
            $idAnimal = $animal->getId();
            /*
            $imagenes = Imagen::buscaImagenes($idAnimal);
            $imagen = $imagenes[0]->getUrl();
            */
            $idprotectora = $animal->getProtectora();
            $protectora = Protectora::buscaPorId($idprotectora);
            $nombreProtectora= $protectora->getNombre();
            /*
            $html .= "<div class=\"contenedorLista\">
                        <div>
                            <a href='./animal.php?id=$idAnimal'><img src=\"/AW-SW/Proyecto/img/$imagen\" class=\"imgAnimal\"></a> 
                            <h2> $nombre</h2>
                        </div>
                        <a href='./animal.php?id=$idAnimal'>Saber más</a>
                    </div>";
            */
            $imagen=$animal->getImagen();
            $htmlImagenes = ' ';
            if ($imagen){
                $rutaImagenes = $imagen;
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
                $htmlImagenes = "<a href='./animal.php?id=$idAnimal'><img src=\"$rutaImg\"class=\"imgLista\"></a>                        
                </a>";
            }else{
                $rutaImagenes = "pordefecto.jpeg";
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
                $htmlImagenes = "<a href='./animal.php?id=$idAnimal'><img src=\"$rutaImg\"class=\"imgLista\"></a>                        
                </a>";
            }
            $html .= "<div class=\"contenedorLista\">
                         <div>
                            $htmlImagenes
                            <a href='./animal.php?id=$idAnimal'><h2> $nombre</h2></a> 
                        </div>
                    </div>";
            
        }
        $html .= "</div>
            <ul class=\"paginacion\">";
        if ($pagina > 1) { 
            $aux = $pagina - 1; 
            if(isset($_GET['formId'])){
                $html .= "<li class=\"numPaginacion\">
                <a href='$url&pagina=$aux'>
                    Anterior
                </a>
                </li>";  
            }else{
                $html .= "<li class=\"numPaginacion\">
                <a href='$url?pagina=$aux'>
                    Anterior
                </a>
                </li>";
            }
        }

        if($paginas > 1){
            if(isset($_GET['formId'])){
                for ($x = 1; $x <= $paginas; $x++) {
                    $html .= "<li class=\"numPaginacion\">
                    <a href='$url&pagina=$x'>$x</a>
                    </li>";
                }
            }else{
                for ($x = 1; $x <= $paginas; $x++) {
                    $html .= "<li class=\"numPaginacion\">
                    <a href='$url?pagina=$x'>$x</a>
                    </li>";
                }
            }
        }

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            if(isset($_GET['formId'])){
                $html .= "<li class=\"numPaginacion\">
                <a href='$url&pagina=$aux'>
                Siguiente
                </a>
                </li>";
            }else{
                $html .= "<li class=\"numPaginacion\">
                <a href='$url?pagina=$aux'>
                Siguiente
                </a>
                </li>";
            }
        }
        $html .= "</ul>";
    }
    else{
        $html = '<p>¡VAYA! Parece que no hay animales en adopción...</p>
                <a href= \'/animalesVista/animales.php\'>
                <img src="/img/errorbusquedaAnimales.gif" id ="errorAnimales" alt = "error">
                </a>';
    }
    return $html;
}

function filtro(){
    $filtro = "";
    if(isset($_GET['tipo'])){
        $filtro .="(A.tipo=\"". implode("\" OR A.tipo=\"", $_GET['tipo'] )."\") AND";
    }
    if(isset($_GET['raza'])){
        $filtro .= "(A.raza=\"".implode("\" OR A.raza=\"",$_GET['raza'])."\") AND ";
    }
    if(isset($_GET['protectora'])){
        $filtro .= "(A.protectora=\"".implode("\" OR A.protectora=\"",$_GET['protectora'])."\") AND ";
    }
    if(isset($_GET['genero'])){
        $filtro .= "(A.genero=\"".implode("\" OR A.genero=\"",$_GET['genero'])."\") AND ";
    }
    if(isset($_GET['edadmin'])&&isset($_GET['edadmax'])){
        if($_GET['edadmin']!=''&&$_GET['edadmax']!=''){
        $filtro .= "(A.edad >= ".$_GET['edadmin']." AND ";
        $filtro .= "A.edad <= ".$_GET['edadmax'].") AND ";
        }
    }
    if(isset($_GET['pesomin']) && isset($_GET['pesomax'])){
        if($_GET['pesomin']!=''&&$_GET['pesomax']!=''){
        $filtro .= "(A.peso >= ".$_GET['pesomin']." AND ";
        $filtro .= "A.peso <= ".$_GET['pesomax']." ) AND ";
        }
    }
    return $filtro;
}

$listarAnimales = mostrarAnimales();
/*
$contenidoSideBarIzq='';
*/
$form = new FormularioFiltroAnimales();
$formFiltro = $form->gestiona();
$contenidoSideBarIzq = <<< EOS
<div id="filtro">
    <h2> Filtro </h2>
    $formFiltro
</div>
EOS;
$contenidoSideBarDer = '';

$contenidoPrincipal = <<<EOS
<div class="seccion">
    <h1>Animales</h1>
    $listarAnimales
</div>
EOS;

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
