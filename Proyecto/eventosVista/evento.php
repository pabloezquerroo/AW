<?php
namespace es\ucm\fdi\aw\eventos;
require_once dirname(__DIR__,1).'/includes/config.php';

use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\protectora\Colabora;
use es\ucm\fdi\aw\Imagen;
use \DateTime;

$tituloPagina = 'Evento';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

$rutaApp = RUTA_APP;

function mostrarEvento(){

    $evento = Evento::buscaPorId($_GET['id']);
    $html = ' ';
    if($evento){
        $idEvento = $evento->getId();
        $titulo = $evento->getTitulo();
        $descripcion = $evento->getDescripcion();
        $tipo = $evento->getTipo();
        $fechaIni = new DateTime();
        $dateIni = $evento->getFechaIni();
        $fechaIni = DateTime::createFromFormat(Evento::FORMAT_MYSQL, $dateIni);
        $fechaIni = $fechaIni->format('d/m/Y H:i');
        $fechaFin = new DateTime();
        $dateFin = $evento->getFechaFin();
        $fechaFin = DateTime::createFromFormat(Evento::FORMAT_MYSQL, $dateFin);
        $fechaFin = $fechaFin->format('d/m/Y H:i');
        $nUsuAsistentes = Asiste::cuentaUsuariosAsistentes($idEvento);
        $protectoraCreadora=Participa::buscaCreadorEvento($idEvento);
        $idProtectoraCreadora = $protectoraCreadora->getId();
        $nombreProtectoraCreadora = $protectoraCreadora->getNombre();

        if(!Asiste::esAsistenteEvento($_SESSION['id'], $_GET['id'])){
            $form=new FormularioRegistroAsistencia();
            $htmlFormAsistir = $form->gestiona();
            $enlaceAsistir = "<div>$htmlFormAsistir</div>";
        }else{
            $form=new FormularioEliminaAsistencia();
            $htmlFormEliminaAsistencia = $form->gestiona();
            $enlaceAsistir = "<div>$htmlFormEliminaAsistencia</div>";
        }

        //Modificar evento lo puede hacer cualquier colaborador???
        if (Colabora::isColaboraOrCreadorProtectora($_SESSION['id'], $idProtectoraCreadora) || $_SESSION["esAdmin"]===true ) {
            $enlaceModifica = "<div>
                                    <a href='./modificaEvento.php?id=$idEvento' id=\"botonModifica\">Modificar</a>
                                </div>";
        }
        else{
            $enlaceModifica = ' ';
        }

        $imagen = $evento->getImagen();
        $htmlImagenes = ' ';
        if ($imagen){
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_EVENTOS_MOSTRAR, $imagen]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgUsuario\"></a>                        
            </a>";
        }else{
            $rutaImagenes = "pordefecto.jpeg";
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_ANIMALES_MOSTRAR, $rutaImagenes]);
            $htmlImagenes = "<img src=\"$rutaImg\"id=\"imgUsuario\"></a>                        
            </a>";
        }
        if($tipo == Evento::CAMINATA){
            $tipo = "Caminata";
        }
        else{
            $tipo = "Mercadillo";
        }
       
       $html .= "<div id='contenedorEvento'>
                    $htmlImagenes
                    <div id=\"infoEvento\">
                        <h1>$titulo</h1>
                        <span>$tipo</span>
                        <span> $fechaIni - $fechaFin </span>
                        <span> Organiza $nombreProtectoraCreadora </span>
                        <p>$descripcion</p>
                        <div id='asiste'>
                            <div>
                                <div>Asistentes</div>
                                <div>$nUsuAsistentes</div>
                            </div>
                            $enlaceAsistir
                        </div>
                    </div>
                </div>
                <div id=\"botones\">
                    $enlaceModifica
                </div>"; 
    }
    else{
        $html = '<p>Parece que hubo un error al mostrar el evento...</p>';
    }
    return $html;
}

function mostrarProtectorasParticipantes(){
     $protectorasPorPagina = 3;
     $pagina = 1;

     $idEvento = $_GET['id'];
     $nProtectorasParticipantes=Participa::cuentaProtectorasParticipantes($idEvento);
     $misProtectora=Protectora::buscaMisProtectorasSinLimit(Protectora::ACTIVA, $_SESSION['id']);
     $enlaceParticipa = '';
     if($misProtectora){
         $enlaceParticipa = "<div><a href= './gestionarParticipacion.php?id=$idEvento' id=\"botonColabora\"> Gestionar Participaciones</a></div>";
     }

    if (isset($_GET["pagina"])) {
         $pagina = $_GET["pagina"];
    }
     # El límite es el número de productos por página
     $limit = $protectorasPorPagina;

     # El offset es saltar X productos que viene dado por multiplicar la página - 1 * los productos por página
     $offset = ($pagina - 1) * $protectorasPorPagina;

     if(isset($_GET["filtro"])){
         $filtro = $_GET["filtro"];
     }
     $conteo = Participa::cuentaProtectorasParticipantes($_GET['id']);
     $paginas = ceil($conteo / $limit);
     $protectoras = Participa::buscaParticipantesEventoConLimite($_GET['id'], $offset, $limit); 
     $html = ' ';
     $rutaProtectorasVista = RUTA_PROTECTORA_VISTA;
    if($protectoras){
         $html .= " <h2 id=\"subtituloSeccion\"> Protectoras participantes </h2>
        <div id='participa'>
            <div>
                <div>Protectoras participantes</div>
                <div>$nProtectorasParticipantes</div>
            </div>
            $enlaceParticipa
        </div>
        <div class=\"listaSeccion\">";
         foreach($protectoras as $protectora){
            $nombre = $protectora->getNombre();
            $idProtectora = $protectora->getId();
            $email = $protectora->getEmail();
            $descripcion  = $protectora->getDescripcion();
            $imagen = $protectora->getImagen();
             
            $htmlImagenes = ' ';
            if ($imagen){
            $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS_MOSTRAR, $imagen]);
            $htmlImagenes = "<a href='$rutaProtectorasVista/protectora.php?id=$idProtectora'><img src=\"$rutaImg\"class=\"imgLista\"></a>";
            }else{
                $rutaImagenes = "pordefecto.jpeg";
                $rutaImg = implode(DIRECTORY_SEPARATOR, [RUTA_IMAGENES_PROTECTORAS_MOSTRAR, $imagen]);
                $htmlImagenes = "<a href='$rutaProtectorasVista/protectora.php?id=$idProtectora'><img src=\"$rutaImg\"class=\"imgLista\"></a>";
            }
            $html .= "<div class=\"contenedorLista\">
                     <div> 
                        $htmlImagenes          
                        <a href='$rutaProtectorasVista/protectora.php?id=$idProtectora'><h2> $nombre</h2></a>   
                     </div>
                </div>"; 
        }
        $html .= "</div>
         <ul class=\"paginacion\">";

        $idEvento = $_GET['id'];

        if ($pagina > 1) { 
            $aux = $pagina - 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./evento.php?pagina=$aux&id=$idEvento'>
                    Anterior
                </a>
             </li>";
        }

        if($paginas > 1){
            for ($x = 1; $x <= $paginas; $x++) {
                $html .= "<li class=\"numPaginacion\">
                    <a href='./evento.php?pagina=$x&id=$idEvento'>$x</a>
              </li>";
            }
        }

        if ($pagina < $paginas) { 
            $aux = $pagina + 1; 
            $html .= "<li class=\"numPaginacion\">
                <a href='./evento.php?pagina=$aux&id=$idEvento'>
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
    $evento = Evento::buscaPorId($_GET['id']);
    $titulo = $evento->getTitulo();
    $auxiliar1 = mostrarEvento();
    $auxiliar2 = mostrarProtectorasParticipantes();
    $contenidoPrincipal = <<<EOS
    <div class="seccion">
        $auxiliar1
        $auxiliar2
    </div>
    EOS;
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';
?>