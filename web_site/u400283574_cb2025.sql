-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 19-07-2025 a las 01:46:42
-- Versión del servidor: 10.11.10-MariaDB-log
-- Versión de PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `u400283574_cb2025`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attachments`
--

CREATE TABLE `attachments` (
  `id_attachment` int(11) NOT NULL,
  `id_post` int(11) NOT NULL,
  `type` enum('pdf','youtube','slider_image','gallery_image') NOT NULL,
  `value` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `attachments`
--

INSERT INTO `attachments` (`id_attachment`, `id_post`, `type`, `value`, `file_name`, `file_path`) VALUES
(23, 22, 'slider_image', 'public/uploads/attachments/slider_image_6879cdbdc830b2.44392810-Mesa de trabajo 1@300x-8.png', 'public/uploads/attachments/slider_image_6879cdbdc830b2.44392810-Mesa de trabajo 1@300x-8.png', 'public/uploads/attachments/slider_image_6879cdbdc830b2.44392810-Mesa de trabajo 1@300x-8.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id_category` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id_category`, `name`) VALUES
(11, 'Maestría'),
(12, 'Profesor'),
(13, 'Actividades'),
(14, 'Carrera'),
(15, 'Testimonio'),
(16, 'Admision');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulario_matricula`
--

CREATE TABLE `formulario_matricula` (
  `id_matricula` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `programa` varchar(255) NOT NULL,
  `nacionalidad` varchar(100) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `whatsapp` varchar(50) NOT NULL,
  `documentos` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `formulario_matricula`
--

INSERT INTO `formulario_matricula` (`id_matricula`, `nombre`, `programa`, `nacionalidad`, `foto`, `email`, `whatsapp`, `documentos`, `fecha_nacimiento`, `fecha_solicitud`) VALUES
(2, 'Renan Galvan', 'Maestria', 'Perú', 'uploads/fotos/foto_687a865a667c05.26783891-renan.jpg', 'renangalvan@gmail.com', '87777849', 'uploads/documentos/doc_687a865a667cf1.60039844-practica_gambito_morra.pdf', '1971-02-08', '2025-07-18 17:37:30'),
(4, 'Lorena Galvan', 'Maestria', 'Argentina', 'uploads/fotos/foto_687a96df2a5a19.35171804-WhatsApp Image 2025-07-17 at 16.39.12.jpeg', 'lorena@gmail.com', '8436587', 'uploads/documentos/doc_687a96df2a5ae3.34090210-practica_gambito_morra.pdf', '1965-01-14', '2025-07-18 18:47:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--

CREATE TABLE `menus` (
  `id_menu` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `menus`
--

INSERT INTO `menus` (`id_menu`, `title`, `url`, `display_order`, `parent_id`) VALUES
(26, 'Unela Virtual', '#', 3, NULL),
(27, 'Carreras en UNELA', '#', 1, NULL),
(31, 'Admisión', '/learner/admision.php', 2, NULL),
(47, 'Origenes', '#', 4, NULL),
(48, 'Instalaciones', '#', 5, NULL),
(54, 'Inicio', '/learner/index.php', 0, NULL),
(55, 'Técnico', '#', 0, 27),
(56, 'Bachiller', '#', 1, 27),
(57, 'Licenciatura', '#', 2, 27),
(58, 'Maestría', '#', 3, 27),
(59, 'Doctorado', '#', 4, 27),
(60, 'Contacto', '#', 6, NULL),
(61, 'Login', '#', 7, NULL),
(63, 'Cerrar sesión', '/learner/logout.php', 1, 61),
(64, 'Iniciar Sesión', '/learner/login.php', 0, 61);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posts`
--

CREATE TABLE `posts` (
  `id_post` int(11) NOT NULL,
  `id_category` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `synopsis` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `posts`
--

INSERT INTO `posts` (`id_post`, `id_category`, `title`, `synopsis`, `content`, `main_image`, `created_at`, `id_user`) VALUES
(21, 14, 'Licenciatura ', 'Aprovecha nuestra oferta académica para aprender más de la palabra de Dios con profesores altamente calificados en la Universidad Evangélica de las Américas.', '<p>Aqui va todo el contenido</p>', 'public/uploads/images/post_6879b461b4c432.97089848-licenciatura.png', '2025-07-16 04:00:39', 1),
(22, 14, 'Doctorado ', 'Da ese gran salto de calidad para desarrollar ese potencial y aprender mas en este doctorado internacional.', '<p>Aqui va todo el contenido del doctorado</p>', 'public/uploads/images/post_6879b4096d57d9.35336244-doctorado.png', '2025-07-16 16:08:28', 1),
(23, 12, 'Phd. Charles Van Engen', 'Profesor de Teología Bíblica de la Misión y ha impartido clases en la Escuela de Estudios Interculturales desde 1988. Antes de llegar a Fuller fue misionero en México, trabajando principalmente en la educación teológica.', '<p>Desarrollo de su biografia</p>', 'public/uploads/images/post_687943ef9e2835.39182285-van_engen.jpg', '2025-07-17 18:41:51', 1),
(24, 15, 'Ing. Renán Galván', 'Creo que contar con una universidad reconocida por el estado costarricense es un privilegio que ahora está al alcance de todo aquel que quiera tener una educación superior de calidad.', '', 'public/uploads/images/post_687944a97ee039.03019454-renan.jpg', '2025-07-17 18:44:57', 1),
(25, 14, 'Bachiller', 'Inicia tu carrera universitaria y fórmate sólidamente para la obra misionera, con un programa altamente calificado.', '<p>Aqui va el contenido</p>', 'public/uploads/images/post_6879b4e168add7.97269684-bacchiller.png', '2025-07-18 02:43:45', 1),
(26, 14, 'Maestría', 'Una vez que hayas terminado tu bachillerato universitario, es momento de iniciar tu maestría para completar tu preparación.', '<p>desarrollo</p>', 'public/uploads/images/post_6879b58298b988.24311711-maestria.png', '2025-07-18 02:46:26', 1),
(27, 14, 'Técnico', 'Es momento de afinar tus destrezas convirtiéndote en un especialista en nuevas tecnologías que será un plus en tu trabajo cotidiano.', '', 'public/uploads/images/post_6879b5d6157ef8.71477802-tecnico.png', '2025-07-18 02:47:50', 1),
(28, 12, 'Dr. Leonel Jiménez Nieto', 'Doctorado en Teología con énfasis en Eclesiología, complementado con una Maestría en Educación. Su dedicación al desarrollo de líderes se refleja en su rol como Coordinador Nacional del Instituto Internacional de Liderazgo.', '', 'public/uploads/images/post_6879c5d4c95563.43373649-leonel.jpg', '2025-07-18 03:56:04', 1),
(29, 16, 'Técnico', 'Aqui van todos los requisitos', '<ol>\r\n<li>2 fotos tama&ntilde;o pasaporte.</li>\r\n<li>Llenar la solicitud de admisi&oacute;n.</li>\r\n<li>Fotocopia de la c&eacute;dula o pasaporte.</li>\r\n<li>Bachillerato de secundaria.</li>\r\n</ol>', '', '2025-07-18 04:58:26', 1),
(30, 16, 'Bachillerato', '', '<ol>\r\n<li>Bachillerato de secundaria.</li>\r\n<li>2 fotos tama&ntilde;o pasaporte.</li>\r\n<li>Llenar la solicitud de admisi&oacute;n.</li>\r\n<li>Fotocopia de la c&eacute;dula o pasaporte.</li>\r\n</ol>', '', '2025-07-18 05:16:39', 1),
(31, 16, 'Maestría', '', '<ol>\r\n<li>Bachillerato Universitario, si este no es en teolog&iacute;a debe hacer una nivelaci&oacute;n</li>\r\n<li>Llenar la solicitud de admisi&oacute;n.</li>\r\n<li>Fotocopia de la c&eacute;dula o pasaporte.</li>\r\n<li>2 fotos tama&ntilde;o pasaporte.</li>\r\n</ol>', '', '2025-07-18 05:19:15', 1),
(32, 12, 'Dra. Amada Naranjo', 'Misionera por 25 años en varios países del mundo.\r\nDra. en Enfermería Obstetra \r\nDocente en universidades privadas y del Estado. \r\nPresidenta de la Universidad Evangélica de las Américas.', '', 'public/uploads/images/post_687aae26d4a0a8.07058030-amada.jpg', '2025-07-18 20:27:18', 1),
(33, 12, 'Dr. Salvador Marín', 'Doctor en Teología \r\nMáster en Ciencias de la Educación \r\nLicenciado en Administración Educativa', '', 'public/uploads/images/post_687ac9931191b9.31742046-salvador.jpg', '2025-07-18 22:24:19', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `full_name`) VALUES
(1, 'admin', '$2y$10$hfAydOAkHjcc2oi9XciIQeIZI4JT7qQeGiUZKZ0hGrIc60M5SuO0K', 'Administrator'),
(15, 'merlin', '$2y$10$1WIBxbJVO9ATZ4Ogn/h0SO0AW0WgNq0Mgiv0zETGqQdYiwl94K.L6', 'Merlin Salas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users_frontend`
--

CREATE TABLE `users_frontend` (
  `id_user_frontend` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users_frontend`
--

INSERT INTO `users_frontend` (`id_user_frontend`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'lorena', '$2y$10$Y6zYKFvlr6Hy50TaGyTn6OmDoxl.vcrzgN2G/PpgdCnj2q2c.Hlqi', 'lorena@gmail.com', '2025-07-18 18:24:30'),
(2, 'Kim', '$2y$10$Av1DUiapObkxUk3ac2pRnu/lE1AQ9NxQPVhF1pEvRE4sbDBJiAaj.', 'kim.chavarria@hotmail.com', '2025-07-18 22:41:00');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id_attachment`),
  ADD KEY `id_post` (`id_post`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_category`);

--
-- Indices de la tabla `formulario_matricula`
--
ALTER TABLE `formulario_matricula`
  ADD PRIMARY KEY (`id_matricula`);

--
-- Indices de la tabla `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id_menu`),
  ADD KEY `fk_menus_parent_id` (`parent_id`);

--
-- Indices de la tabla `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `fk_posts_user` (`id_user`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `users_frontend`
--
ALTER TABLE `users_frontend`
  ADD PRIMARY KEY (`id_user_frontend`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id_attachment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id_category` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `formulario_matricula`
--
ALTER TABLE `formulario_matricula`
  MODIFY `id_matricula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `menus`
--
ALTER TABLE `menus`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id_post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `users_frontend`
--
ALTER TABLE `users_frontend`
  MODIFY `id_user_frontend` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`id_post`) REFERENCES `posts` (`id_post`) ON DELETE CASCADE;

--
-- Filtros para la tabla `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `fk_menus_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id_menu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `fk_posts_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL,
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id_category`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
