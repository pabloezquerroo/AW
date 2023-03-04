<?php
namespace es\ucm\fdi\aw\usuarios;

require_once dirname(__DIR__,1).'/includes/config.php';

$contenidoSideBarIzq = '';
$contenidoSideBarDer = '';

$tituloPagina = 'Modifica Usuario';

if (isset($_SESSION["login"])) {
    $formModifica = new FormularioModifica();
    $htmlFormModifica = $formModifica->gestiona();

    $contenidoPrincipal = <<<EOS
    <div class="inicioSesion">
        <h1>Ajustes del usuario</h1>
        $htmlFormModifica
        <ul class="pie-form">
            <li><a href='./cambiaPassword.php'>Cambiar password</a></li>
            <li><a href='./eliminaUsuario.php'>Dar de baja</a></li>
        </ul>
    </div>
    EOS;
}
else {
    $contenidoPrincipal = '<h1>Contenido no accesible sin iniciar sesión</h1>';
}

require dirname(__DIR__,1).'/includes/vistas/plantillas/plantilla.php';