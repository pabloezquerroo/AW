<?php
namespace es\ucm\fdi\aw\protectora;
require_once dirname(__DIR__,1).'/includes/config.php';
use es\ucm\fdi\aw\usuarios\Usuario;

$form = new FormularioEliminaColaborador($_GET['idu']);
$htmlFormEliminaColaborador = $form->gestiona();

$tituloPagina = 'Elimina Colaborador';

if (isset($_SESSION["login"]) && Colabora::isCreador($_SESSION['id'], $_GET['id'])) {
    $usuario = Usuario::buscaPorId($_GET['idu']);
    $nombreUsuario = $usuario->getNombre();
    
    $contenidoPrincipal = <<<EOS
        <div class="inicioSesion">
            <h1> Eliminar Colaborador </h1>
            <p> ¿Estás seguro de querer eliminar a $nombreUsuario como colaborador? </p>
            $htmlFormEliminaColaborador
        </div>
        EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible, debes ser creador y estar logeado.</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';