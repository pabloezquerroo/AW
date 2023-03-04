<?php
namespace es\ucm\fdi\aw\animales;

require_once dirname(__DIR__,1).'/includes/config.php';

use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\protectora\Colabora;
use es\ucm\fdi\aw\Imagen;

$tituloPagina = 'Animal';

function mostrarAnimalAdoptado(){
    $rutaApp = RUTA_ANIMALES_VISTA;
    $rutaProtectoraVista = RUTA_PROTECTORA_VISTA;
    
    $animal = Animal::buscaPorID($_GET['id']); 
    $html = ' ';
    if($animal){
        $nombre = $animal->getNombre();
        $raza = $animal->getRaza();
        $edad = $animal->getEdad();
        $peso = $animal->getPeso();
        $genero = $animal->getGenero();
        $idAnimal=$animal->getId();
        
        
        $imagen=$animal->getImagen();
        $htmlImagenes = ' ';
        if ($imagen){
            $rutaImagenes = $imagen;
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgAnimal\">                       
            </a>";
        }else{
            $rutaImagenes = "pordefecto.jpeg";
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgAnimal\">                       
            </a>";
        }

        $html .= "<div id=\"contenedorAnimal\">
            $htmlImagenes
            <div id=\"infoAnimal\">
                <h1> $nombre </h1>
                <div>$genero </div>
                <div>$raza </div>
                <div>$edad años</div>
                <div>$peso Kg</div>
            </div>
        </div>
        <a href='{$rutaApp}/modificaAnimal.php?id=$idAnimal' id=\"botonModifica\">Modificar</a>";
    }
    else{
        $html = '<p>Animal no encontrado.</p>';
    }
    return $html;
}

function mostrarAnimal(){
    $rutaApp = RUTA_ANIMALES_VISTA;
    $rutaProtectoraVista = RUTA_PROTECTORA_VISTA;
    if(isset($_SESSION['id'])){
    $form = new FormularioSolicitaAdopcion($_GET['id']);
    $htmlFormSolicitaAdopcion = $form->gestiona();
    }
    $animal = Animal::buscaPorID($_GET['id']); 
    $html = ' ';
    if($animal){
        $nombre = $animal->getNombre();
        $raza = $animal->getRaza();
        $edad = $animal->getEdad();
        $peso = $animal->getPeso();
        $genero = $animal->getGenero();
        $idAnimal=$animal->getId();
        
        $idprotectora = $animal->getProtectora();
        $protectora = Protectora::buscaPorId($idprotectora);
        $nombreProtectora= $protectora->getNombre();
        $imagen= $animal->getImagen();
        $htmlImagenes = ' ';
        if ($imagen){
            $rutaImagenes = $imagen;
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgAnimal\">                       
            </a>";
        }else{
            $rutaImagenes = "pordefecto.jpeg";
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgAnimal\">                       
            </a>";
        }

        $html .= "<div id=\"contenedorAnimal\">
            $htmlImagenes
            <div id=\"infoAnimal\">
                <h1> $nombre </h1>
                <div>$genero </div>
                <div>$raza </div>
                <div>$edad años</div>
                <div>$peso Kg</div>
                <div class='pie-form'>
                <a href='$rutaProtectoraVista/protectora.php?id=$idprotectora'>Gestiona la adopcion $nombreProtectora</a>
                </div>
            </div>
        </div>";
        if(isset($_SESSION['id'])){
            if(Adopta::existeProcesoAdopcion($_SESSION['id'], $animal->getId())) {
                $html .= <<<EOF
                        <div><p>Solicitud de adopción pedida</p></div>
                        EOF;
            }
            else if (!Colabora::buscaColaborador($_SESSION['id'], $idprotectora)){
                $html .=  $htmlFormSolicitaAdopcion;
            }
            $MisProtectoras= Colabora::ProtectorasPertenecientes($_SESSION['id']);
            $protectoraAnimal=$animal->getProtectora();
            if ($MisProtectoras){   
                foreach($MisProtectoras as $Protectora){
                    if ($Protectora->getId()==$protectoraAnimal&& Colabora::isColaboraOrCreadorProtectora(($_SESSION['id']), $Protectora->getId())){
                        $html .="<a href='{$rutaApp}/modificaAnimal.php?id=$idAnimal' id=\"botonModifica\">Modificar</a>";
                     }
                 }   
             }
        }
    }
    else{
        $html = '<p>No tienes acceso a este contenido...</p>';
    }
    return $html;
}

function solicitudAdopcion() {
    $idAnimal = $_GET['id'];
    $solicitudesAdopcionPorPagina = 1;
    $pagina = 1;
    
    if (isset($_GET["pagina"])) {
        $pagina = $_GET["pagina"];
    }
    # El límite es el número de productos por página
    $limit = $solicitudesAdopcionPorPagina;

    # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
    $offset = ($pagina - 1) * $solicitudesAdopcionPorPagina;

	$conteo =  Adopta::cuentaSolicitudesAdopcion($idAnimal);
	$paginas = ceil($conteo / $limit);
    $usuarios =  Adopta::buscaUsuarioPendienteConLimite($idAnimal, $offset, $limit);
    $html = ' ';
    if($usuarios) {
        $animal =  Animal::buscaPorID($_GET['id']); 
        $idprotectora = $animal->getProtectora();
        if( isset($_SESSION['id']) && Colabora::buscaColaborador($_SESSION['id'], $idprotectora)){
            $html .= "<h2 id=\"subtituloSeccion\"> Solicitudes de adopción </h2>";
        }
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

            $formAcepta = new  FormularioAceptaAdopcion($_GET['id'], $usuario->getId());
            $htmlFormAceptaAdopcion = $formAcepta->gestiona();

            $formCancela = new  FormularioCancelarAdopcion($_GET['id'], $usuario->getId());
            $htmlFormCancelaAdopcion = $formCancela->gestiona();

           
            if (isset($_SESSION['id'])){
                if ( Adopta::existeProcesoAdopcion($usuario->getId(), $_GET['id']) &&  Colabora::buscaColaborador($_SESSION['id'], $idprotectora))
                {
                    $html .= "<div class=\"contenedorLista\">
                    <h2> $nombre </h2>
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
                    <div >
                        <a href='../protectoraVista/gestionarAdopcion.php?idU=$idUsuario&idA=$idAnimal&idP=$idprotectora'> Gestionar adopción </a>
                    </div>  
                </div>";
                }
            }
        }
        $html .= "</div>
		<ul class=\"paginacion\">";

        if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./animal.php?pagina=$aux&id=$idAnimal#subtituloSeccion'>
                    Anterior
                </a>
            </li>";
        }

		if($paginas > 1){
        	for ($x = 1; $x <= $paginas; $x++) {
            	$html .= "<li class=\"numPaginacion\">
                	<a href='./animal.php?pagina=$x&id=$idAnimal#subtituloSeccion' >$x</a>
            	</li>";
        	}
		}

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./animal.php?pagina=$aux&id=$idAnimal#subtituloSeccion'>
                    Siguiente
                </a>
            </li>";
        }
		$html .= "</ul>";
    }
    else {
        $html = ' ';
    }
    return $html;
}

$animal =  Animal::buscaPorID($_GET['id']); 
if ($animal){
    $nombreAnimal = $animal->getNombre();
    if($animal->getProtectora() != null){
        $idprotectora = $animal->getProtectora();
        $infoAnimal = mostrarAnimal();
        if (Colabora::isColaboraOrCreadorProtectora($_SESSION['id'], $idprotectora))
            $infoUsuario = solicitudAdopcion();
        else
            $infoUsuario = "";
    }
    else{
        if(isset($_SESSION['id']) && Adopta::esAnimalAdoptadoUsuario($_SESSION['id'], $_GET['id']))
            $infoAnimal = mostrarAnimalAdoptado();
        else
            $infoAnimal = "<p>Debe ser el propietario del animal para ver este contenido<p>";
        $infoUsuario = "";
    }
}
else{
    $infoAnimal = "<p> El animal no existe <p>";
    $infoUsuario = "";
}

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

$contenidoPrincipal = <<<EOS
<div class="seccion">   
    $infoAnimal
    <div id="contenedorSolicitud">
        $infoUsuario
    </div>
</div>
EOS;

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';