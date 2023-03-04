<?php

namespace es\ucm\fdi\aw\eventos;

use JsonSerializable;

require_once dirname(__DIR__,1).'/includes/config.php';

header('Content-Type: application/json');
    $eventos=Evento::buscaEventos();
    echo json_encode($eventos);

?>