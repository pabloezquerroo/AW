<?php
namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';

use es\ucm\fdi\aw\animales\Animal;
use es\ucm\fdi\aw\Imagen;

$tituloPagina = 'Protectora';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

$rutaApp = RUTA_APP;

function mostrarProtectora(){

    $protectora = Protectora::buscaPorId($_GET['id']); 
    $html = ' ';
    if($protectora){
        $nombre = $protectora->getNombre();
        $idProtectora = $protectora->getId();
        $telefono = $protectora->getTelefono();
        $email = $protectora->getEmail();
        $direccion = $protectora->getDireccion();
        $descripcion = $protectora->getDescripcion();

        if(!Colabora::buscaColaborador($_SESSION['id'], $idProtectora)){
            $enlaceColabora = "<div><a href= './colaboraProtectora.php?id=$idProtectora' id=\"botonColabora\">Colaborar</a></div>";
        }
        else if (Colabora::esColaboradorPendiente($idProtectora, $_SESSION['id'])){
            $enlaceColabora = '<p> Solicitud de colaboración pendiente <p>';
        }
        else{
            $enlaceColabora = ' ';
        }

        if(Colabora::isCreador($_SESSION['id'], $idProtectora)){
            $enlaceSolicitudColaboradores = "<div>
                                                <a href='./solicitudesColabora.php?id=$idProtectora' id=\"botonSolicitudesColaboracion\">Solicitudes colaboracion</a>
                                            </div>";
            $enlaceColaboradores = "<div>
                                        <a href='./colaboradores.php?id=$idProtectora' id=\"botonGestionProtectora\">Gestion Colaboradores</a>
                                    </div>";
        }
        else{
            $enlaceSolicitudColaboradores = ' ';
            $enlaceColaboradores = ' ';
        }

        if (Colabora::isColaboraOrCreadorProtectora($_SESSION['id'],$idProtectora) || (isset($_SESSION["esAdmin"]) && $_SESSION["esAdmin"]===true)){
                $enlaceModifica = "<div>
                <a href='./modificaProtectora.php?id=$idProtectora' id=\"botonModifica\">Modificar</a>
            </div>";
        }
        else{
            $enlaceModifica = ' ';
        }
        //Modificar protectora lo puede hacer cualquier colaborador???
        if (Colabora::isColaboraOrCreadorProtectora($_SESSION['id'],$idProtectora)){
            $rutaAnimalesVista = RUTA_ANIMALES_VISTA;
            $rutaMensajesVista = RUTA_MENSAJES_VISTA;
            
            $enlaceRegistroAnimal = "<div>
                                        <a href='$rutaAnimalesVista/registroAnimal.php?id=$idProtectora' id=\"botonRegistro\">Añadir Animal</a>
                                    </div>";
            $enlaceMensajes = "<div>
                                <a href='$rutaMensajesVista/mensajes.php?id=$idProtectora' id=\"botonMensajes\">Mensajes</a>
                                </div>";
            $enlaceSolicitudAdopcion = "<div>
            <a href='./solicitudesAdopcion.php?id=$idProtectora' id=\"botonSolicitudesColaboracion\">Solicitudes Adopcion</a>
            </div>";
    }
        else{
            $enlaceRegistroAnimal = ' ';
            $enlaceMensajes = '';
            $enlaceSolicitudAdopcion = '';
        }

        $imagen = $protectora->getImagen();
        $htmlImagenes = ' ';
        if ($imagen){
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS_MOSTRAR, $imagen]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgUsuario\"></a>                        
            </a>";
        }else{
            $rutaImagenes = "pordefecto.jpeg";
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgUsuario\"></a>                        
            </a>";
        }
       
       $html .= "<div id='contenedorProtectora'>
                    $htmlImagenes
                    <div id=\"infoProtectora\">
                        <h1>$nombre</h1>
                        <span>$email</span>
                        <div>$telefono</div>
                        <div>$direccion</div>
                        <p>$descripcion</p>
                    </div>
                </div>
                <div id=\"botones\">
                    $enlaceColabora
                    $enlaceColaboradores
                    $enlaceSolicitudColaboradores
                    $enlaceModifica
                    $enlaceRegistroAnimal
                    $enlaceMensajes
                    $enlaceSolicitudAdopcion
                </div>"; 
    }
    else{
        $html = '<p>Parece que hubo un error al mostrar la protectora...</p>';
    }
    return $html;
}

function mostrarAnimales(){
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
    $conteo = Animal::cuentaAnimalesPorProtectora($_GET['id']);
    $paginas = ceil($conteo / $limit);
    $animales = Animal::buscaPorProtectora($_GET['id'], $offset, $limit); 
    $html = ' ';
    $rutaAnimalesVista = RUTA_ANIMALES_VISTA;
    if($animales){
        $html .= " <h2 id=\"subtituloSeccion\"> Animales en adopción </h2>
        <div class=\"listaSeccion\">";
        foreach($animales as $animal){
            $nombre = $animal->getNombre();
            $idAnimal = $animal->getId();

            $idprotectora = $animal->getProtectora();
            $protectora = Protectora::buscaPorId($idprotectora);
            $nombreProtectora= $protectora->getNombre();

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

        $idProtectora = $_GET['id'];

        if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./protectora.php?pagina=$aux&id=$idProtectora'>
                    Anterior
                </a>
            </li>";
        }

        if($paginas > 1){
            for ($x = 1; $x <= $paginas; $x++) {
                $html .= "<li class=\"numPaginacion\">
                    <a href='./protectora.php?pagina=$x&id=$idProtectora'>$x</a>
             </li>";
            }
        }

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./protectora.php?pagina=$aux&id=$idProtectora'>
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

if (!isset($_SESSION["login"])) {
	$contenidoPrincipal = <<<EOS
	<h1>¿Eres nuevo?</h1>
	<p>¡Hola! Para formar parte de esta comunidad amante de los animales y poder acceder a todas
	las funcionalidades de la página debes estar registrado.</p>
	<h2>¡¡¡Bienvenido!!!</h2>
	<ul>
		<li><a href='{$rutaApp}/registro.php'>Registrarse</a></li><li><a href='{$rutaApp}/login.php'>Ya tengo cuenta</a>
	</ul>
	EOS;
} 
else {
    $protectora = Protectora::buscaPorId($_GET['id']);
    $nombre = $protectora->getNombre();
    $auxiliar1 = mostrarProtectora();
    $auxiliar2 = mostrarAnimales();
    $contenidoPrincipal = <<<EOS
    <div class="seccion">
        $auxiliar1
        $auxiliar2
    </div>
    EOS;
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>