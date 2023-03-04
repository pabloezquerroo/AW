CREATE DATABASE IF NOT EXISTS `pet2safe` DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;
-- --------------------------------------------------------


CREATE USER 'pet2safe'@'localhost' IDENTIFIED BY 'pet2safe';
GRANT ALL PRIVILEGES ON `pet2safe`.* TO 'pet2safe'@'localhost';