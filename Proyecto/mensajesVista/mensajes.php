<?php
namespace es\ucm\fdi\aw\mensajes; 

use es\ucm\fdi\aw\protectora\Protectora;
use es\ucm\fdi\aw\protectora\Colabora;
use es\ucm\fdi\aw\animales\Animal;

require_once dirname(__DIR__,1).'/includes/config.php';

$tituloPagina = 'Mensajes';

if(isset($_GET['acepta'])){
    Mensaje::aceptaTraslado($_GET['acepta']);
}
else if(isset($_GET['rechaza'])){
    Mensaje::rechazaTraslado($_GET['rechaza']);
}
function mostrarMensajes(){
    $idC = isset($_GET['chat'])? $_GET['chat'] : false;
    $html = '';
    if(Colabora::buscaColaborador($_SESSION['id'],$_GET['id'])){
        if(!$idC || $idC == $_GET['id']){
            $html .= '<p>Elige una conversación para empezar a hablar.</p>';
        }
        else{
            $mensajes = Mensaje::buscaMensajes($_GET['id'], $idC);
            $html .= "<div id=\"espacioChat\">";
            if($mensajes){
                foreach($mensajes as $mensaje){
                    $emisor = Protectora::buscaPorId($mensaje->getEmisor())->getNombre();
                    $texto = $mensaje->getMensaje();

                    if($mensaje->getTipo() == Mensaje::MENSAJE){
                        if($mensaje->getEmisor() == $_GET['id']){
                            $html .= "
                            <div class='mensajeEnviado'>
                                <h2> $emisor</h2>
                                <p>$texto</p>
                            </div>
                            ";
                        }
                        else{
                            $html .= "
                            <div class = 'mensajeRecibido'>
                                <h2> $emisor</h2>
                                <p>$texto</p>
                            </div>
                            ";
                        }
                    }
                    else{
                        $nombre = Animal::buscaPorID($mensaje->getAnimal())->getNombre();
                        $id = $_GET['id'];
                        $chat = $_GET['chat'];
                        $idA = $mensaje->getAnimal();
                        $idM = $mensaje->getIdMensaje();
                        if($mensaje->getEmisor() == $_GET['id']){
                            $html .= "
                            <div class = 'mensajeEnviado'>
                                <h2> $emisor</h2>
                                <p>Haz solicitado el traslado de este animal: $nombre</p>
                                <p>$texto</p>
                            </div>
                            ";
                        }
                        else{
                            $html .= "
                            <div class = 'mensajeRecibido'>
                                <h2> $emisor</h2>
                                <p id=\"animalTraslado\">Hola, necesitamos tu ayuda. No nos queda espacio en nuestro centro y necesitamos trasladar este animal: 
                                <a href='../animalesVista/animal.php?id=$idA '>$nombre </a></p>
                                <p>$texto</p>
                                <p> ¿tu podrias ayudarnos?</p>
                                <div class=\"opcionConfirmacion\">
                                    <a class=\"confirmar\"  href='mensajes.php?id=$id&chat=$chat&acepta=$idM'\" > Aceptar </a>
                                    <a class=\"confirmar\" href='mensajes.php?id=$id&chat=$chat&rechaza=$idM'\"> Cancelar </a>
                                </div>
                            </div>
                            ";
                        }
                    }
                }
            }
            else{
                $html .= "
                        <div>
                            <p>Envia un mensaje para empezar una conversación</p>
                        </div>";
            }
            $html .= "</div>";
            $form = new FormularioEnvioMensaje();
            $html .= $form->gestiona();
        }
    }
    else{
        $html = "
                <div>
                <p>ERROR 403: acceso no autorizado</p>
                </div>";
    }
    return $html;
}

function mostrarChats(){
    $idP = $_GET['id'];
    $chats = Mensaje::buscaChats($idP);
    $rutaApp = RUTA_APP;
    $html = '';
    if($chats){
        $html .= "<li id=\"protectorasChat\">
                <h2> Chats </h2>
            <ul>";
        foreach($chats as $chat){
            $nombre = $chat->getNombre();
            $id = $chat->getId();
            $html .= " <li><a href='./mensajes.php?id=$idP&chat=$id'>$nombre</li>";
        }
        $html .= " </ul>
        </li>";
    }
    else{
        $html .= '<li id="tituloChat">
                <h2> Chats </h2>
        <p>No tienes Chats abiertos</p>
        </li>';
    }
    return $html;
}

function mostrarProtectorasSinChat(){
    $idP = $_GET['id'];
    $chats = Mensaje::buscaChatsLibres($idP);
    $rutaApp = RUTA_APP;
    $html = '';
    if($chats){
        $html .= "<div id=\"protectorasChat\">
                        <h2> Nuevo chat </h2>
                    <ul>";
        foreach($chats as $chat){
            $nombre = $chat->getNombre();
            $id = $chat->getId();
            $html .="<li><a id=\"enlaceChat\" href='./mensajes.php?id=$idP&chat=$id'>$nombre</li>";
        }
        $html .= " </ul>
        </div>";
    }
    else{
        $html .= '<h2> Nuevo chat </h2>
        <p>No tienes Chats nuevos para abrir.</p>';
    }
    return $html;
}




$conversacion = mostrarMensajes();

$contenidoSideBarIzq = mostrarChats();

$contenidoSideBarDer = mostrarProtectorasSinChat();

$contenidoPrincipal = <<<EOS
<div class="inicioSesion">    
    <h1>Mensajes</h1>   
    $conversacion
</div>
EOS;

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';