<!DOCTYPE html>
<?php use es\ucm\fdi\aw\config; ?>
<html>
<head>
	<meta charset="UTF-8">
    <title><?= $tituloPagina ?></title>
    <link rel="stylesheet" type="text/css" href="<?= RUTA_CSS ?>/estilo.css" />

	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Open+Sans&display=swap" rel="stylesheet"/>
	<script type="text/javascript" src="<?= RUTA_JS ?>/jquery-3.6.0.min.js"></script>
	<script type ="text/javaScript" src="<?= RUTA_JS ?>/script.js"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha256-YvdLHPgkqJ8DVUxjjnGVlMMJtNimJ6dYkowFFvp4kKs=" crossorigin="anonymous">
	<script src="<?= RUTA_EVENTOS_VISTA ?>/js/jquery.min.js"></script>
	<script src="<?= RUTA_EVENTOS_VISTA ?>/js/moment.min.js"></script>
	<!-- Full Calendar -->
	<link rel="stylesheet" href="<?= RUTA_EVENTOS_VISTA ?>/css/fullcalendar.min.css">
	<script src="<?= RUTA_EVENTOS_VISTA ?>/js/fullcalendar.min.js"></script>
	<script src="<?= RUTA_EVENTOS_VISTA ?>/js/eventos.js"></script>
	<script src="<?= RUTA_EVENTOS_VISTA ?>/js/es.js"></script>
</head>
<body>
	<?php require(RAIZ_APP.'/vistas/comun/cabecera.php');?>
	<div id="contenedor">
		<?php require(RAIZ_APP.'/vistas/comun/sidebarIzq.php');?>
		<main>
			<article>
				<?= $contenidoPrincipal ?>
			</article>
		</main>
		<?php require(RAIZ_APP.'/vistas/comun/sidebarDer.php'); ?>
	</div>
	<?php require(RAIZ_APP.'/vistas/comun/pie.php');?>
</body>
</html>
