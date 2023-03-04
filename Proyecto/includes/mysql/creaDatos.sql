-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-04-2022 a las 19:50:55
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `pet2safe`
--

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `password`, `email`, `rol`, `direccion`, `num_convivientes`, `tipo_vivienda`, `dedicacion`, `terraza`, `num_mascotas`, `telefono`, `m2_vivienda`, `imagen`) VALUES
(1, 'admin', '$2y$10$rHq0lI7deVLcfq3wX8OCWuMmE95HdCKlN3mFiRVMj0qj04vy0fHbS', 'admin@admin.es', 1, 'c/ San Francisco n&deg;10', 2, 'Piso', 'Administrador Pet2Safe', 0, 0, 678123456, 60, '1.png'),
(2, 'usuario', '$2y$10$Ga6Q6jZaBZRBQfj.7K4MAO.Gv2DpeYlX8IvJGyKtgR6soRbqgoF2y', 'user@user.es', 2, 'C/ Los Naranjos, 8', 4, 'Piso', 'Constructor', 1, 0, 654321987, 80, '2.jpeg'),
(3, 'usuario2', '$2y$10$l7CD6DXrc1Qz.Ceemk/zP.Rx/n8McflrKOKxZ0ZdpJymUrjCRRa96', 'user2@user.es', 2, 'C/ La Reina, 3', 0, 'Piso', 'Dise&ntilde;ador', 0, 0, 693582471, 45, '3.jpg'),
(4, 'Eladia', '$2y$10$YbFLiPx6o6l4Upjp2tNtA.HxJJLS7KZxEE3JI9wq4g9Qf72NIkF.C', 'elagomez@ucm.es', 2, 'C/ Profesor Garc&iacute;a Santesmases 9', 6, 'Casa', 'Estudiante', 1, 1, 654321987, 150, '4.jpg'),
(5, 'Pablo', '$2y$10$ikxObssl3RPAuWYGsHnh5e3Yym9avk.yKL3xQ6E6OqYLELP4x1RGm', 'pabloezq@ucm.es', 2, 'Calle del Prof. Jos&eacute; Garc&iacute;a Santesmases, 9, 28040 Madrid', 6, 'Casa', 'Estudiante', 1, 1, 654321971, 150, '5.jpg'),
(6, 'Javier', '$2y$10$P1XoCRqBWy0WhPGaxXIlAOB8jF3jkGNWVL7mHJdT30XRI6cY5ubAa', 'javiro17@ucm.es', 2, 'Calle del Prof. Jos&eacute; Garc&iacute;a Santesmases, 9, 28040 Madrid', 6, 'Casa', 'Programaci&oacute;n', 1, 0, 653246875, 150, '6.jpg'),
(7, 'Adrian', '$2y$10$/RBy/GhLL/IL8RWg9uZvVe6gG.cxZ2wllSA8QeGnrrSk3WL1Z8iBS', 'adrcorra@ucm.es', 2, 'Calle del Prof. Jos&eacute; Garc&iacute;a Santesmases, 9, 28040 Madrid', 6, 'Casa', 'Estudiante', 1, 1, 635241856, 150, '7.jpg'),
(8, 'Agustin', '$2y$10$Zy0A9uwhFcYaHDRNJEd3LOqzyA4TG5hxqkee.u0KXDeircbkK2zb6', 'agusbuen@ucm.es', 2, 'Calle del Prof. Jos&eacute; Garc&iacute;a Santesmases, 9, 28040 Madrid', 6, 'Casa', 'estudiante a veces', 1, 0, 654123987, 150, '8.jpg'),
(9, 'Manuel', '$2y$10$6yHO8AqExJqnKhdOnq8.0e35O5JV07cfUmxfVi0/e6s.qCU/gdSXK', 'mandiazd@ucm.es', 2, 'Calle del Prof. Jos&eacute; Garc&iacute;a Santesmases, 9, 28040 Madrid', 6, 'Casa', 'Estudiante', 1, 0, 654321987, 150, '9.jpg');

--
-- Volcado de datos para la tabla `protectora`
--

INSERT INTO `protectora` (`ID`, `nombre`, `estado`, `telefono`, `email`, `direccion`, `descripcion`, `imagen`) VALUES
(1, 'Protectora La Mejor', 1, 638938545, 'lamejor@correo.es', 'C/ Guerrero Nº 21', 'Protectora La Mejor es la mejor protectora.', '1.png'),
(2, 'Protectora La Mas Mejor', 1, 637489625, 'lamasmejor@correo.es', 'C/ Luz Nº 2', 'Protectora La Mas Mejor es realmente la mejor', '2.png'),
(3, 'We Love Pets', 1, 638852815, 'lovePets@correo.es', 'C/ Los Cubos Nº 4', 'En We Love Pets amamos a todos los animalitos.', '3.png'),
(4, 'Pets For All', 1, 639478565, 'petsforall@correo.es', 'C/ La fabrica Nº 7', 'Pets For All pero siempre y cuando los cuides.', '4.png'),
(5, 'AdoptPet', 1, 634964537, 'adoptPet@correo.es', 'C/ La olivilla Nº 10', 'AdoptPet, tu protectora de confianza.', '5.png'),
(6, 'Alma Peluda', 1, 687259463, 'almapeluda@correo.es', 'C/ El Pilar Nº 8', 'Alma Peluda es una protectora por el amor hacia los animales.', '6.png'),
(7, 'Amores Perros', 0, 654321987, 'amoresperros@correo.es', 'C/ La Rosa, 8', 'Amores Perros y amor por los perros', '7.jpeg'),
(8, 'Gatomania', 0, 687666999, 'gatomania@correo.es', 'C/ Buendia, 7', 'Gatomania la mejor compañia', '8.jpeg'),
(9, 'VidaAnimal', 1, 987654321, 'vidanimal@correo.es', 'C/ La Pérgola 13, 2ºA', 'Protectora amante de los animales repleta de amor.', '9.png'),
(10, 'ProteCMS', 1, 987654321, 'protecms@correo.es', 'C/Torremolinos 12', 'Protectora que lucha por los derechos de los animales de tener un hogar', '10.png'),
(11, 'S.P.A.P Fuencarral', 1, 913119133, 'info@spap.net', 'Carretera Fuencarral - El Pardo km 2.2', 'Nuestro albergue tiene como misión esencial cuidar y fomentar la adopción\r\nresponsable de los animales acogidos.', '11.jpg'),
(12, 'Adopte Protectora', 1, 654321987, 'adopteprotectora@ucm.es', 'C/ Las Rosas 12', 'Protectora empezando con ganas de ayudar a nuestros amigos peludos.', '12.png');

--
-- Volcado de datos para la tabla `colabora`
--

INSERT INTO `colabora` (`IDprotectora`, `IDusuario`, `rol`) VALUES
(1, 1, 3),
(1, 2, 2),
(1, 7, 1),
(2, 1, 2),
(3, 1, 2),
(4, 2, 2),
(5, 2, 2),
(5, 7, 1),
(5, 8, 1),
(6, 1, 2),
(6, 2, 1),
(6, 4, 3),
(6, 9, 1),
(7, 3, 2),
(8, 3, 2),
(9, 2, 1),
(9, 4, 2),
(10, 5, 2),
(10, 6, 3),
(10, 7, 1),
(11, 5, 3),
(11, 6, 2),
(12, 5, 3),
(12, 8, 3),
(12, 9, 2);

--
-- Volcado de datos para la tabla `animales`
--

INSERT INTO `animales` (`ID`, `nombre`, `tipo`, `protectora`, `edad`, `raza`, `genero`, `peso`, `imagen`) VALUES
(1, 'Toby', 'Perro', 3, 5, 'Yorkshire', 'Macho', 6, '1.jpeg'),
(2, 'Missi', 'Gato', 1, 3, 'Persa', 'Macho', 8, '2.jpeg'),
(3, 'Lolo', 'Perro', NULL, 2, 'Beagle', 'Macho', 10, '3.jpeg'),
(4, 'Noke', 'Perro', 6, 4, 'Fox Terrier', 'Macho', 10, '4.png'),
(5, 'Katia', 'Gato', 4, 3, 'Siames', 'Hembra', 5, '5.jpeg'),
(6, 'Rambo', 'Perro', 5, 5, 'Pug', 'Macho', 6, '6.jpeg'),
(7, 'Beatle', 'Perro', 4, 6, 'Beagle', 'Macho', 11, '7.jpeg'),
(8, 'Fox', 'Perro', NULL, 1, 'Shiba', 'Hembra', 10, '8.jpg'),
(9, 'Feli', 'Perro', 10, 3, 'Shiba', 'Hembra', 10, '9.jpeg'),
(10, 'Luna', 'Perro', 11, 9, 'Mestizo de yorkshire terrier', 'Hembra', 5, '10.jpg'),
(11, 'Ce&ntilde;o', 'Gato', 11, 7, 'Comun', 'Macho', 10, '11.jpg'),
(12, 'Linda', 'Gato', 10, 1, 'Gato Persa', 'Hembra', 4, '12.jpeg'),
(13, 'Dorito', 'Gato', 11, 4, 'Comun', 'Macho', 10, '13.jpg'),
(14, 'Bala', 'Perro', 12, 6, 'Labrador', 'Macho', 30, '14.jpeg'),
(15, 'Borja', 'Perro', 9, 2, 'Fox Terrier', 'Macho', 12, '15.jpg'),
(16, 'Ramontxu', 'Perro', 3, 5, 'Comun', 'Macho', 25, '16.jpg'),
(17, 'Terry', 'Perro', 2, 5, 'Fox Terrier', 'Macho', 10, '17.jpg'),
(18, 'Yorky', 'Perro', 2, 3, 'Yorkshire', 'Hembra', 2, '18.jpg'),
(19, 'Lazy', 'Gato', 1, 5, 'Gato persa', 'Hembra', 4, '19.webp');

--
-- Dumping data for table `adopta`
--

INSERT INTO `adopta` (`id_usuario`, `id_animal`, `estado`) VALUES
(3, 5, 1),
(4, 3, 2),
(5, 5, 1),
(5, 8, 2),
(6, 9, 1),
(9, 13, 1);

--
-- Dumping data for table `mensaje`
--

INSERT INTO `mensaje` (`ID`, `IdEmisor`, `IdReceptor`, `mensaje`, `fecha`, `tipo`, `IdAnimal`) VALUES
(1, 9, 6, 'Hola, &iquest;me puedes ayudar?', '2022-05-11 18:01:16', 1, 0),
(6, 9, 6, 'Traslado de animal completado correctamente', '2022-05-11 18:21:52', 1, 8),
(7, 6, 9, 'Claro!', '2022-05-11 18:22:28', 1, 0),
(8, 12, 9, 'Hola!! Como vais de espacio en tu terreno?', '2022-05-12 09:56:49', 1, 0),
(9, 9, 12, 'La verdad es que nos hemos quedado sin animalitos por suerte!', '2022-05-12 09:57:40', 1, 0),
(10, 12, 9, 'Joo genial!! podr&iacute;as hacerte cargo de un fox terrier?', '2022-05-12 09:58:31', 1, 0),
(11, 9, 12, 'Claro! Manda el traslado...', '2022-05-12 09:58:52', 1, 0),
(12, 12, 9, 'Traslado de animal completado correctamente', '2022-05-12 09:59:32', 1, 15),
(13, 12, 1, 'Buenos d&iacute;as! Necesit&aacute;is ayuda con la fuga de agua del otro d&iacute;a?', '2022-05-12 10:00:55', 1, 0),
(14, 1, 12, 'No os preocup&eacute;is que esta apa&ntilde;ado. Much&iacute;simas gracias!', '2022-05-12 10:02:32', 1, 0),
(15, 11, 6, 'Alla va el animal que te coment&eacute; en el mercadillo', '2022-05-12 10:05:28', 1, 0),
(16, 11, 6, 'a ver si lo podeis ayudar', '2022-05-12 10:05:37', 1, 0),
(17, 11, 6, 'Necesita ayuda', '2022-05-12 10:06:12', 2, 11);

--
-- Dumping data for table `eventos`
--

INSERT INTO `eventos` (`id`, `title`, `start`, `end`, `descripcion`, `tipo`, `imagen`) VALUES
(4, 'Mercadillo Solidario', '2022-05-13 10:30:00', '2022-05-13 20:30:00', 'Mercadillo con fines benéficos para subsanar los gastos de la protectora VidaAnimal.', 1, '4.png'),
(6, 'Camino de Santiago Perruno', '2022-05-25 11:31:00', '2022-05-28 11:31:00', 'Únete al equipo de aventuras. En esta recorreremos la parte final del camino. Pronto publicaremos la información en el blog!', 0, '6.jpg'),
(7, 'Mercadillo Sostenible', '2022-05-30 09:00:00', '2022-05-30 18:00:00', 'Mercadillo con distintos puestos con productos artesanales de nuestras protectoras participantes!! Tendrá lugar en el Retiro de Madrid!', 1, '7.jpeg'),
(8, 'Mirador de la Dehesa de la Villa', '2022-05-16 10:00:00', '2022-05-16 19:00:00', 'Paseo por el mirador y comida con todo tipo de animales.', 0, '8.webp'),
(9, 'Caminata por la adopcion', '2022-06-16 12:00:00', '2022-06-16 15:00:00', 'Una caminata bien chula para fomentar la adopción de nuestros peludos', 1, '9.webp');

--
-- Dumping data for table `participa`
--

INSERT INTO `participa` (`id_protectora`, `id_evento`, `rol`) VALUES
(6, 4, 2),
(6, 9, 1),
(9, 4, 1),
(9, 9, 2),
(10, 6, 1),
(10, 7, 2),
(11, 6, 2),
(11, 7, 1),
(11, 8, 1),
(12, 4, 2),
(12, 6, 2),
(12, 8, 2);

--
-- Dumping data for table `asiste`
--

INSERT INTO `asiste` (`id_usuario`, `id_evento`) VALUES
(1, 4),
(4, 4),
(4, 9),
(5, 6),
(5, 7),
(5, 8),
(6, 4),
(6, 6),
(6, 7),
(6, 8),
(7, 6),
(8, 4),
(8, 6),
(9, 4),
(9, 6),
(9, 8);

--
-- Dumping data for table `post`
--

INSERT INTO `post` (`ID`, `descripcion`, `titular`, `IdProtectora`, `fechaCreacion`, `idUsuario`, `imagen`) VALUES
(5, 'Estamos deseando que conozcáis a la protectora VidaAnimal y sobre todo a nuestros animalitos, que están deseando ser adoptados.', '&iexcl;Nuestro primer post!', 9, '12/5/2022', 4, '5.gif'),
(6, 'Muy contenta de darle una mejor vida a Lolo. ¡Nuestra vida cambió desde su adopción! ¡Te queremos Lolo!', 'Qu&eacute; guapo mi Lolo...', 0, '12/5/2022', 4, '6.jpeg'),
(7, 'Debido a la falta de espacio en nuestras instalaciones y las ganas que tiene Luna de tener un gran dueño, urge su adopción. Si está interesado, responda a este post para más información.', '&iexcl;Urge adopci&oacute;n!', 11, '12/5/2022', 6, NULL),
(8, 'Me he encontrado un perro abandonado en el campo, por mi situación laboral me es imposible quedarme con el, alguna protectora que tenga espacio en sus instalaciones se ponga en contacto conmigo, un saludo!', 'Animal abandonado encontrado!', 0, '12/5/2022', 7, '8.jpg');

--
-- Dumping data for table `favoritos`
--

INSERT INTO `favoritos` (`idUsuario`, `idPost`) VALUES
(3, 6),
(4, 5),
(4, 6),
(4, 8),
(6, 5),
(6, 6),
(7, 6),
(7, 7),
(7, 8),
(8, 7),
(8, 8),
(9, 6);

--
-- Dumping data for table `comentarios`
--

INSERT INTO `comentarios` (`id`, `idUsuario`, `idPost`, `fechaCrea`, `descripcion`) VALUES
(1, 4, 5, '12/5/2022', 'Hola, soy la administradora de la protectora VidaAnimal!'),
(2, 6, 6, '12/5/2022', 'Oleeee qué guapooo'),
(3, 5, 6, '12/5/2022', 'oooooh! Que bonitoo!!!!'),
(5, 4, 8, '12/5/2022', 'qué pena :(');


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
commit;