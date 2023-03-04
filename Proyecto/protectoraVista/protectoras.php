<?php
namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';


$tituloPagina = 'Protectoras';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

$rutaApp = RUTA_APP;

function mostrarProtectoras(){
    $protectorasPorPagina = 6;
    $pagina = 1;

    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }
    # El límite es el número de productos por página
    $limit = $protectorasPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $protectorasPorPagina;
	$conteo = Protectora::cuentaEstadoActivo();
    $paginas = ceil($conteo / $limit);
    $protectoras = Protectora::buscaPorEstado(Protectora::ACTIVA, $offset, $limit); 
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

                $html .= "<div class=\"contenedorLista\">
                            <div>
                                $htmlImagenes
                                <a href='./protectora.php?id=$idProtectora'><h2>$nombre</h2></a>
                                <span>$email</span>
                            </div>
                        </div>"; 
        }
        $html .= "</div>
            <ul class=\"paginacion\">";

        if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./protectoras.php?pagina=$aux'>
                    Anterior
                </a>
            </li>";
        }

        if($paginas > 1){
            for ($x = 1; $x <= $paginas; $x++) {
                $html .= "<li class=\"numPaginacion\">
                    <a href='./protectoras.php?pagina=$x'>$x</a>
                </li>";
            }
        }

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./protectoras.php?pagina=$aux'>
                    Siguiente
                </a>
            </li>";
        }
        $html .= "</ul>";

    }else{
        $html = '<p>Parece que no hay protectoras registradas en este momento...</p>';
    }
    return $html;
}

if (!isset($_SESSION["login"])) {
    $rutaUsuarioVista = RUTA_USUARIO_VISTA;
	$contenidoPrincipal = <<<EOS
	<div class="contenidoNoLogueado"><h1>¿Eres nuevo?</h1>
	<p>¡Hola! Para formar parte de esta comunidad amante de los animales y poder acceder a todas
	las funcionalidades de la página debes estar registrado.</p>
	<h2>¡¡¡Bienvenido!!!</h2>
	<ul>
		<li class="botonRegistro"><a href='$rutaUsuarioVista/registro.php'>Registrarse</a></li><li><a href='$rutaUsuarioVista/login.php'>Ya tengo cuenta</a>
	</ul></div<
	EOS;
} 
else {
    $auxiliar1 = mostrarProtectoras();
    $contenidoPrincipal = <<<EOS
    <div class="seccion">
        <h1>Protectoras</h1>
        $auxiliar1
    </div>
    EOS;
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>