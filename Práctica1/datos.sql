-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: vm12.db.swarm.test
-- Generation Time: May 11, 2022 at 06:47 PM
-- Server version: 10.8.2-MariaDB-1:10.8.2+maria~focal
-- PHP Version: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `pet2safe`
--

--
-- Dumping data for table `adopta`
--

INSERT INTO `adopta` (`id_usuario`, `id_animal`, `estado`) VALUES
(3, 5, 1);

--
-- Dumping data for table `animales`
--

INSERT INTO `animales` (`ID`, `nombre`, `tipo`, `protectora`, `edad`, `raza`, `genero`, `peso`, `imagen`) VALUES
(1, 'Toby', 'Perro', 3, 5, 'Yorkshire', 'Macho', 6, '1.jpeg'),
(2, 'Missi', 'Gato', 1, 3, 'Persa', 'Macho', 8, '2.jpeg'),
(3, 'Gus', 'Perro', 2, 2, 'Beagle', 'Macho', 10, '3.jpeg'),
(4, 'Noke', 'Perro', 6, 4, 'Fox Terrier', 'Macho', 10, '4.png'),
(5, 'Katia', 'Gato', 4, 3, 'Siames', 'Hembra', 5, '5.jpeg'),
(6, 'Rambo', 'Perro', 5, 5, 'Pug', 'Macho', 6, '6.jpeg'),
(7, 'Beatle', 'Perro', 4, 6, 'Beagle', 'Macho', 11, '7.jpeg'),
(8, 'Fox', 'Perro', 6, 1, 'Shiba', 'Hembra', 10, '8.jpg');

--
-- Dumping data for table `colabora`
--

INSERT INTO `colabora` (`IDprotectora`, `IDusuario`, `rol`) VALUES
(1, 1, 3),
(1, 2, 2),
(2, 1, 2),
(3, 1, 2),
(4, 2, 2),
(5, 2, 2),
(6, 1, 2),
(6, 2, 1),
(6, 4, 3),
(7, 3, 2),
(8, 3, 2),
(9, 2, 1),
(9, 4, 2);

--
-- Dumping data for table `mensaje`
--

INSERT INTO `mensaje` (`ID`, `IdEmisor`, `IdReceptor`, `mensaje`, `fecha`, `tipo`, `IdAnimal`) VALUES
(1, 9, 6, 'Hola, &iquest;me puedes ayudar?', '2022-05-11 18:01:16', 1, 0),
(6, 9, 6, 'Traslado de animal completado correctamente', '2022-05-11 18:21:52', 1, 8),
(7, 6, 9, 'Claro!', '2022-05-11 18:22:28', 1, 0);

--
-- Dumping data for table `protectora`
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
(9, 'VidaAnimal', 1, 987654321, 'vidanimal@correo.es', 'C/ La Pérgola 13, 2ºA', 'Protectora amante de los animales repleta de amor.', '9.png');

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `password`, `email`, `rol`, `direccion`, `num_convivientes`, `tipo_vivienda`, `dedicacion`, `terraza`, `num_mascotas`, `telefono`, `m2_vivienda`, `imagen`) VALUES
(1, 'admin', '$2y$10$rHq0lI7deVLcfq3wX8OCWuMmE95HdCKlN3mFiRVMj0qj04vy0fHbS', 'admin@admin.es', 1, 'c/ San Francisco n&deg;10', 2, 'Piso', 'Administrador Pet2Safe', 0, 0, 678123456, 60, '1.png'),
(2, 'usuario', '$2y$10$Ga6Q6jZaBZRBQfj.7K4MAO.Gv2DpeYlX8IvJGyKtgR6soRbqgoF2y', 'user@user.es', 2, 'C/ Los Naranjos, 8', 4, 'Piso', 'Constructor', 1, 0, 654321987, 80, '2.jpeg'),
(3, 'usuario2', '$2y$10$l7CD6DXrc1Qz.Ceemk/zP.Rx/n8McflrKOKxZ0ZdpJymUrjCRRa96', 'user2@user.es', 2, 'C/ La Reina, 3', 0, 'Piso', 'Dise&ntilde;ador', 0, 0, 693582471, 45, '3.jpg'),
(4, 'Eladia', '$2y$10$H6ZATuRz9bQuPHvRYWiYxOT6kQsQ07FQSRBUkKCn2lNgVobqzizV.', 'elagomez@ucm.es', 2, 'C/ Profesor Garc&iacute;a Santesmases 9', 6, 'Casa', 'Estudiante', 1, 0, 654321987, 150, '4.jpg');
COMMIT;