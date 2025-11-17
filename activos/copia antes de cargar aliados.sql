-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql:3306
-- Tiempo de generación: 15-11-2025 a las 15:27:41
-- Versión del servidor: 8.0.43
-- Versión de PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `webci`
--
CREATE DATABASE IF NOT EXISTS `webci` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `webci`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_user`
--

CREATE TABLE `admin_user` (
  `id` int NOT NULL,
  `username` varchar(64) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(160) NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `last_login_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `admin_user`
--

INSERT INTO `admin_user` (`id`, `username`, `auth_key`, `password_hash`, `email`, `status`, `created_at`, `updated_at`, `last_login_at`) VALUES
(1, 'ronald', '7e5369dcd58e5d0b807bebed076745f5', '$2y$10$efft/0vEbwelRcvABUWXs.9VFRSewr5SMqGdq455KF4GfjwNBdnwC', 'ronald@camarainversionistas.com', 10, 1763214685, 1763214685, 1763214901);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `benefit`
--

CREATE TABLE `benefit` (
  `id` int NOT NULL,
  `category_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `logo` varchar(120) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `benefit`
--

INSERT INTO `benefit` (`id`, `category_id`, `title`, `description`, `logo`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Asesoría para su mejor financiamiento en diversas entidades debidamente inscritas ante SUGEF.', NULL, 'finanzas', 10, 1, 1763214321, 1763214321),
(2, 1, 'Servicios de presentación de IVA y renta. Contabilidad.', NULL, 'impuestos', 20, 1, 1763214321, 1763214321),
(3, 1, 'Asesoría Tributaria.', NULL, 'impuestos', 30, 1, 1763214321, 1763214321),
(4, 1, 'Acceso a nuestro programa de microcréditos*.', NULL, 'microcreditos', 40, 1, 1763214321, 1763214321),
(5, 1, 'Asesoría para su mejor financiamiento en diversas entidades debidamente inscritas ante SUGEF.', NULL, 'finanzas', 10, 1, 1763216135, 1763216135),
(6, 1, 'Servicios de presentación de IVA y renta. Contabilidad. Asesoría Tributaria.', NULL, 'impuestos', 20, 1, 1763216135, 1763216135),
(7, 1, 'Acceso a nuestro programa de microcréditos*.', NULL, 'microcreditos', 30, 1, 1763216135, 1763216135),
(8, 3, 'Servicio de facturación electrónica.', NULL, 'impuestos', 10, 1, 1763216135, 1763216135),
(9, 3, 'Asesoría completa para la obtención del sello PYME del MEIC (Exención IVA en alquileres y otros beneficios).', NULL, 'crecimiento', 20, 1, 1763216135, 1763216135),
(10, 3, 'Creación de sitios web.', NULL, 'crecimiento', 30, 1, 1763216135, 1763216135),
(11, 3, 'Asesoría en Registro de Marca.', NULL, 'crecimiento', 40, 1, 1763216135, 1763216135),
(12, 3, 'Acceso preferencial a nuestro programa de oficina virtual.', NULL, 'crecimiento', 50, 1, 1763216135, 1763216135),
(13, 3, 'Acceso preferencial a nuestro servicio de call center.', NULL, 'crecimiento', 60, 1, 1763216135, 1763216135),
(14, 4, 'Acceso a la Cobertura de Gastos Fúnebres.', NULL, 'finanzas', 10, 1, 1763216135, 1763216135),
(15, 4, 'Acceso a nuestra red privada de salud empresarial.', NULL, 'microcreditos', 20, 1, 1763216135, 1763216135),
(16, 5, 'Acceso preferencial en nuestra revista digital.', NULL, 'impuestos', 10, 1, 1763216135, 1763216135),
(17, 5, 'Promoción de su negocio en el encadenamiento y redes de la Cámara.', NULL, 'crecimiento', 20, 1, 1763216135, 1763216135),
(18, 6, 'Participación en talleres, ruedas de negocios, capacitaciones, cursos, charlas y ferias estratégicas.', NULL, 'crecimiento', 10, 1, 1763216135, 1763216135),
(19, 6, 'Participar en reconocimientos anuales.', NULL, 'crecimiento', 20, 1, 1763216135, 1763216135),
(20, 7, 'Uso 2 horas al mes (con cita) de oficinas, sala de capacitación (20 personas) y sala de juntas (8 personas).', NULL, 'finanzas', 10, 1, 1763216135, 1763216135),
(21, 7, '1 hora diaria de parqueo gratuito en el estacionamiento del Templo Parroquial San Ramón (no aplica domingos).', NULL, 'impuestos', 20, 1, 1763216135, 1763216135),
(22, 8, 'Acceso a carnet con descuentos en comercios, clínicas, veterinarias y más.', NULL, 'finanzas', 10, 1, 1763216135, 1763216135),
(23, 8, 'Servicio de rent a car sin depósito de garantía con autos propios y cuenta corporativa.', NULL, 'impuestos', 20, 1, 1763216135, 1763216135),
(24, 9, 'Acceso a nuestro programa de pasantías para contar con pasantes.', NULL, 'crecimiento', 10, 1, 1763216135, 1763216135),
(25, 9, 'Acceso a nuestra bolsa de empleo.', NULL, 'crecimiento', 20, 1, 1763216135, 1763216135);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `benefit_category`
--

CREATE TABLE `benefit_category` (
  `id` int NOT NULL,
  `name` varchar(180) NOT NULL,
  `description` text,
  `logo` varchar(120) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `benefit_category`
--

INSERT INTO `benefit_category` (`id`, `name`, `description`, `logo`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Servicios Financieros y Tributarios', 'Primer categoría de beneficios según lineamientos de noviembre 2024.', 'finanzas', 10, 1, 1763214321, 1763214321),
(2, 'Servicios Financieros y Tributarios', 'Programas y asesorías financieras y tributarias.', 'finanzas', 10, 1, 1763216135, 1763216135),
(3, 'Servicios Empresariales y Administrativos', 'Procesos administrativos, marca y soporte empresarial.', 'crecimiento', 20, 1, 1763216135, 1763216135),
(4, 'Beneficios de Salud y Bienestar', 'Coberturas y red privada de salud.', 'microcreditos', 30, 1, 1763216135, 1763216135),
(5, 'Servicios de Promoción y Publicidad', 'Difusión en medios propios de la Cámara.', 'impuestos', 40, 1, 1763216135, 1763216135),
(6, 'Capacitación y Desarrollo', 'Talleres, ruedas de negocios y reconocimientos.', 'crecimiento', 50, 1, 1763216135, 1763216135),
(7, 'Infraestructura y Espacios', 'Uso de oficinas, salas y parqueo.', 'finanzas', 60, 1, 1763216135, 1763216135),
(8, 'Descuentos y Convenios Especiales', 'Carnet y rent a car corporativo.', 'impuestos', 70, 1, 1763216135, 1763216135),
(9, 'Programas de Apoyo y Desarrollo Laboral', 'Pasantías y bolsa de empleo.', 'crecimiento', 80, 1, 1763216135, 1763216135);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business`
--

CREATE TABLE `business` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` varchar(500) DEFAULT NULL,
  `description` text,
  `email` varchar(160) DEFAULT NULL,
  `whatsapp` varchar(32) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `tiktok` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `cover_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `show_on_home` tinyint(1) NOT NULL DEFAULT '0',
  `email_template_id` int DEFAULT NULL,
  `email_template_slug` varchar(160) DEFAULT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business_category`
--

CREATE TABLE `business_category` (
  `business_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contact_submission`
--

CREATE TABLE `contact_submission` (
  `id` int NOT NULL,
  `business_id` int DEFAULT NULL,
  `fullname` varchar(160) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `email` varchar(160) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `subject` text,
  `created_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_template`
--

CREATE TABLE `email_template` (
  `id` int NOT NULL,
  `name` varchar(160) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` mediumtext NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1763090611),
('m130524_201442_init', 1763090612),
('m190124_110200_add_verification_token_column_to_user_table', 1763090612),
('m241114_000001_create_category_table', 1763213983);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `site_config`
--

CREATE TABLE `site_config` (
  `id` int NOT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `logo_width` int DEFAULT NULL,
  `logo_height` int DEFAULT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `site_config`
--

INSERT INTO `site_config` (`id`, `logo_path`, `logo_width`, `logo_height`, `updated_at`) VALUES
(1, NULL, NULL, NULL, 1763103006);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsor`
--

CREATE TABLE `sponsor` (
  `id` int NOT NULL,
  `name` varchar(160) NOT NULL,
  `logo_path` varchar(255) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sponsor_set`
--

CREATE TABLE `sponsor_set` (
  `id` int NOT NULL,
  `title` varchar(120) DEFAULT 'Patrocinadores',
  `image_one` varchar(255) DEFAULT NULL,
  `image_two` varchar(255) DEFAULT NULL,
  `image_three` varchar(255) DEFAULT NULL,
  `image_four` varchar(255) DEFAULT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `sponsor_set`
--

INSERT INTO `sponsor_set` (`id`, `title`, `image_one`, `image_two`, `image_three`, `image_four`, `updated_at`) VALUES
(1, 'Patrocinadores', NULL, NULL, NULL, NULL, 1763089090);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb3_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `verification_token` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `benefit`
--
ALTER TABLE `benefit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_benefit_category` (`category_id`),
  ADD KEY `idx_benefit_sort` (`is_active`,`sort_order`);

--
-- Indices de la tabla `benefit_category`
--
ALTER TABLE `benefit_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_benefit_category_sort` (`is_active`,`sort_order`);

--
-- Indices de la tabla `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_business_email_template` (`email_template_id`);

--
-- Indices de la tabla `business_category`
--
ALTER TABLE `business_category`
  ADD PRIMARY KEY (`business_id`,`category_id`),
  ADD KEY `fk_business_category_category` (`category_id`);

--
-- Indices de la tabla `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `contact_submission`
--
ALTER TABLE `contact_submission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contact_business` (`business_id`);

--
-- Indices de la tabla `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indices de la tabla `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indices de la tabla `site_config`
--
ALTER TABLE `site_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sponsor`
--
ALTER TABLE `sponsor`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sponsor_set`
--
ALTER TABLE `sponsor_set`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `benefit`
--
ALTER TABLE `benefit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `benefit_category`
--
ALTER TABLE `benefit_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `business`
--
ALTER TABLE `business`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `contact_submission`
--
ALTER TABLE `contact_submission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `email_template`
--
ALTER TABLE `email_template`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `site_config`
--
ALTER TABLE `site_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `sponsor`
--
ALTER TABLE `sponsor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sponsor_set`
--
ALTER TABLE `sponsor_set`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `benefit`
--
ALTER TABLE `benefit`
  ADD CONSTRAINT `fk_benefit_category` FOREIGN KEY (`category_id`) REFERENCES `benefit_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `business`
--
ALTER TABLE `business`
  ADD CONSTRAINT `fk_business_email_template` FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `business_category`
--
ALTER TABLE `business_category`
  ADD CONSTRAINT `fk_business_category_business` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_business_category_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `contact_submission`
--
ALTER TABLE `contact_submission`
  ADD CONSTRAINT `fk_contact_business` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
