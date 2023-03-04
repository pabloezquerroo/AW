<?php
namespace es\ucm\fdi\aw\usuarios;

require_once dirname(__DIR__,1).'/includes/config.php';

use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\protectora\Colabora;
use es\ucm\fdi\aw\animales\Adopta;
use es\ucm\fdi\aw\animales\Animal;
use es\ucm\fdi\aw\Imagen;

$tituloPagina = 'Perfil';

function muestraMisAnimales(){
    $animalesPorPagina = 3;
    $pagina = 1;

    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }
    # El límite es el número de productos por página
    $limit = $animalesPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $animalesPorPagina;

    if(isset($_GET["filtro"])){
        $filtro = $_GET["filtro"];
    }
    $conteo = Adopta::cuentaAnimalesAdoptados($_SESSION['id']);
    $paginas = ceil($conteo / $limit);
    $animales = Adopta::buscaAnimalesAdoptados($_SESSION['id'], $offset, $limit);
    $html = ' ';
    $rutaAnimalesVista = RUTA_ANIMALES_VISTA;
    if($animales){
        $html .= " <h2 id=\"subtituloSeccion\"> Animales adoptados </h2>
        <div class=\"listaSeccion\">";
        foreach($animales as $animal){
            $nombre = $animal->getNombre();
            $idAnimal = $animal->getId();

           $imagen=$animal->getImagen();
            $htmlImagenes = ' ';
            if ($imagen){
                $rutaImagenes = $imagen;
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
                $htmlImagenes = "<a href='$rutaAnimalesVista/animal.php?id=$idAnimal'><img src=\"$rutaImg\"class=\"imgLista\"></a>                        
                </a>";
            }else{
                $rutaImagenes = "pordefecto.jpeg";
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
                $htmlImagenes = "<a href='$rutaAnimalesVista/animal.php?id=$idAnimal'><img src=\"$rutaImg\"class=\"imgLista\"></a>                        
                </a>";
            }
            $html .= "<div class=\"contenedorLista\">
                    <div> 
                        $htmlImagenes          
                        <a href='$rutaAnimalesVista/animal.php?id=$idAnimal'><h2> $nombre</h2></a>   
                    </div>
                </div>"; 
        }
        $html .= "</div>
        <ul class=\"paginacion\">";

        if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./perfil.php?pagina=$aux'>
                    Anterior
                </a>
            </li>";
        }

        if($paginas > 1){
            for ($x = 1; $x <= $paginas; $x++) {
                $html .= "<li class=\"numPaginacion\">
                    <a href='./perfil.php?pagina=$x'>$x</a>
             </li>";
            }
        }

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./perfil.php?pagina=$aux'>
                    Siguiente
                </a>
            </li>";
        }
    }
    else{
        $html = '';
    }
    return $html;
}

function mostrarInfoUsuario() {
    $usuario =  Usuario::buscaPorId($_SESSION['id']); 
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
    $html = '';
    if ($terraza == 1){
        $terraza = "con";
    }
    else {
        $terraza = "sin";
    }

    $imagen = $usuario->getImagen();
        $htmlImagenes = ' ';
        if ($imagen){
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_USUARIOS_MOSTRAR, $imagen]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgUsuario\"></a>                        
            </a>";
        }else{
            $rutaImagenes = "pordefecto.jpeg";
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_USUARIOS_MOSTRAR, $rutaImagenes]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgUsuario\"></a>                        
            </a>";
        }

    $html .= "<div id='contenedorUsuario'>
            $htmlImagenes
            <div id=\"infoUsuario\">
                <h1> $nombre </h1>
                <span>$email </span>
                <div>$direccion </div>
                <div>$telefono </div>
                <div>Se dedica a ser $dedicacion.</div>
                <div>Vive en un/una $tipo_vivienda de $m2_vivienda m² $terraza terraza.</div>
                <div>Convive con $num_convivientes personas.</div>
                <div>Actualmente tiene $num_mascotas mascotas.</div>
            </div>
            </div>
            <div id=\"botones\">
                <div><a href='./ajustesUsuario.php' id='botonAjustes'>Ajustes</a></div>
                <div><a href='../protectoraVista/misProtectoras.php' id='botonAjustes'>Mis protectoras</a></div>
            </div>";
    return $html;
}




$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

if (isset($_SESSION["login"])) {
    $infoUsuario= mostrarInfoUsuario();
    $misAnimales = muestraMisAnimales();
    $contenidoPrincipal = <<<EOS
    <div class="seccion">   
        $infoUsuario
        $misAnimales
    </div>
    EOS;
}
else{
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}


require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';