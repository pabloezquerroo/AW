<?php

namespace es\ucm\fdi\aw;

use es\ucm\fdi\aw\Aplicacion;

/**
 * Parámetros de conexión a la BD
 */
define('BD_HOST', 'localhost');
define('BD_NAME', 'pet2safe');
define('BD_USER', 'pet2safe');
define('BD_PASS', 'pet2safe');

/**
 * Parámetros de configuración utilizados para generar las URLs y las rutas a ficheros en la aplicación
 */
define('RAIZ_APP', __DIR__);
define('RUTA_APP', '/AW-SW/Proyecto');
define('RUTA_IMGS', RUTA_APP.'/img');
define('RUTA_INCLUDE', RUTA_APP.'/includes');
define('RUTA_CSS', RUTA_APP.'/css');
define('RUTA_JS', RUTA_APP.'/js');
define('RUTA_IMAGENES_PROTECTORAS', implode(DIRECTORY_SEPARATOR, [__DIR__, 'imagenes/protectoras']));
define('RUTA_IMAGENES_PROTECTORAS_MOSTRAR', implode(DIRECTORY_SEPARATOR, [RUTA_INCLUDE, 'imagenes/protectoras']));
define('RUTA_IMAGENES_ANIMALES', implode(DIRECTORY_SEPARATOR, [__DIR__, 'imagenes/animales']));
define('RUTA_IMAGENES_ANIMALES_MOSTRAR', implode(DIRECTORY_SEPARATOR, [RUTA_INCLUDE, 'imagenes/animales']));
define('RUTA_IMAGENES_USUARIOS', implode(DIRECTORY_SEPARATOR, [__DIR__, 'imagenes/usuarios']));
define('RUTA_IMAGENES_USUARIOS_MOSTRAR', implode(DIRECTORY_SEPARATOR, [RUTA_INCLUDE, 'imagenes/usuarios']));
define('RUTA_IMAGENES_EVENTOS', implode(DIRECTORY_SEPARATOR, [__DIR__, 'imagenes/eventos']));
define('RUTA_IMAGENES_EVENTOS_MOSTRAR', implode(DIRECTORY_SEPARATOR, [RUTA_INCLUDE, 'imagenes/eventos']));
define('RUTA_IMAGENES_POST', implode(DIRECTORY_SEPARATOR, [__DIR__, 'imagenes/post']));
define('RUTA_IMAGENES_POST_MOSTRAR', implode(DIRECTORY_SEPARATOR, [RUTA_INCLUDE, 'imagenes/post']));

define('RUTA_ANIMALES_VISTA', RUTA_APP.'/animalesVista');
define('RUTA_USUARIO_VISTA', RUTA_APP.'/usuarioVista');
define('RUTA_PROTECTORA_VISTA', RUTA_APP.'/protectoraVista');
define('RUTA_EVENTOS_VISTA', RUTA_APP.'/eventosVista');
define('RUTA_BLOG_VISTA', RUTA_APP.'/blogVista');
define('RUTA_MENSAJES_VISTA', RUTA_APP.'/mensajesVista');

define('RUTA_ANIMALES', RUTA_INCLUDE.'/animales');
define('RUTA_USUARIO', RUTA_INCLUDE.'/usuario');
define('RUTA_PROTECTORA', RUTA_INCLUDE.'/protectora');
define('RUTA_EVENTOS', RUTA_INCLUDE.'/eventos');
define('RUTA_BLOG', RUTA_INCLUDE.'/blog');
define('RUTA_MENSAJES', RUTA_INCLUDE.'/mensajes');

/**
 * Configuración del soporte de UTF-8, localización (idioma y país) y zona horaria
 */
ini_set('default_charset', 'UTF-8');
setLocale(LC_ALL, 'es_ES.UTF.8');
date_default_timezone_set('Europe/Madrid');

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'es\\ucm\\fdi\\aw\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . '/';
    //$base_dir = '/AW-SW/Proyecto/include';
    // does the class use the namespace prefix?
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }
    
    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});

// Inicializa la aplicación
$app = Aplicacion::getInstance();
$app->init(['host'=>BD_HOST, 'bd'=>BD_NAME, 'user'=>BD_USER, 'pass'=>BD_PASS]);

/**
 * @see http://php.net/manual/en/function.register-shutdown-function.php
 * @see http://php.net/manual/en/language.types.callable.php
 */
register_shutdown_function([$app, 'shutdown']);