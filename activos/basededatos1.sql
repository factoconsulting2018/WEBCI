-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql:3306
-- Tiempo de generación: 15-11-2025 a las 13:41:50
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admin_user`
--

CREATE TABLE `admin_user` (
  `id` int NOT NULL,
  `username` varchar(64) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `access_token` varchar(64) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `last_login_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `admin_user`
--

INSERT INTO `admin_user` (`id`, `username`, `password_hash`, `auth_key`, `access_token`, `status`, `created_at`, `updated_at`, `last_login_at`) VALUES
(1, 'ronald', '$2y$10$J4bj89FLi9bJom5rywofNe1XeLCFx9ONzBEEwEH/erI3PVyVHpRMm', 'X7e0rUOqx0doXlYPsN1rFJvv1JrS6gQ9', 'VJ7p9P1fGfMxwV7p5A3cYfWqDQ6mQe3Pk0lY4boYEn2lP8O6q2yCeVdGJXwLrH2Q', 10, 1763090450, 1763090450, 1763092249);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business`
--

CREATE TABLE `business` (
  `id` int NOT NULL,
  `name` varchar(160) NOT NULL,
  `slug` varchar(180) NOT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `description` text,
  `whatsapp` varchar(32) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(180) NOT NULL,
  `social_links` text,
  `logo_path` varchar(255) DEFAULT NULL,
  `show_on_home` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email_template_id` int DEFAULT '1',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `business`
--

INSERT INTO `business` (`id`, `name`, `slug`, `summary`, `description`, `whatsapp`, `address`, `email`, `social_links`, `logo_path`, `show_on_home`, `is_active`, `email_template_id`, `created_at`, `updated_at`) VALUES
(1, 'FACTOCONSULTING', 'FACTOCONSULTING', 'FACTOCONSULTING', 'SERVICIO DE CONSULTORIA', '88781108', '25 OESTE Y 100 NORTE DEL ASERRADERO DE SAN JUAN.', 'INFO@FACTOCONSULTING.COM', '[]', '/uploads/business/factoconsulting-1763091624.png', 1, 1, 1, 1763091624, 1763099068),
(2, 'FACTO RENTA CAR', 'FACTO-RENTA-CAR', 'ES UNA EMPRESA DE ALQUILER DE VEHÍCULOS UBICADA EN SAN RAMÓN, ALAJUELA, ENFOCADA EN BRINDAR UN SERVICIO RÁPIDO, CONFIABLE', 'Es una empresa de alquiler de vehículos ubicada en San Ramón, Alajuela, enfocada en brindar un servicio rápido, confiable.', '88781108', 'SAN RAMÓN', 'INFO@FACTORENTACAR.COM', '[]', '/uploads/business/facto-renta-car-1763093535.png', 1, 1, 1, 1763093535, 1763180257),
(3, 'SUPER POLLO', 'SUPER-POLLO', 'Soda', 'Soda en el centro de San Ramón', '88781108', 'SAN RAMÓN CENTRO', 'SUPERPOLLO@GMAIL.COM', '[]', NULL, 1, 1, 1, 1763180360, 1763180360),
(4, 'ABOGADO PARTHER BLANCO', 'ABOGADO-PARTHER-BLANCO', 'ABOGADO Y NOTARIO', 'ABOGADO Y NOTARIO EN SAN RAMON', '88781108', 'SAN RAMON', 'NOTARIO@GMAIL.COM', '[]', NULL, 1, 1, 1, 1763180582, 1763180582);

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
  `business_id` int NOT NULL,
  `fullname` varchar(160) NOT NULL,
  `phone` varchar(64) NOT NULL,
  `address` varchar(255) NOT NULL,
  `subject` varchar(180) NOT NULL,
  `created_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `email_template`
--

CREATE TABLE `email_template` (
  `id` int NOT NULL,
  `name` varchar(120) NOT NULL,
  `subject` varchar(180) NOT NULL,
  `html_body` text NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `email_template`
--

INSERT INTO `email_template` (`id`, `name`, `subject`, `html_body`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Plantilla básica', 'Nuevo contacto desde el portal', '<p>Hola {{businessName}},</p><p>{{fullName}} desea ponerse en contacto contigo.</p><ul><li>Teléfono: {{phone}}</li><li>Dirección: {{address}}</li><li>Asunto: {{subject}}</li></ul><p>Equipo WebCI</p>', 1, 1763089089, 1763089089);

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
  ADD UNIQUE KEY `uq-admin_user-username` (`username`);

--
-- Indices de la tabla `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx-business-email` (`email`),
  ADD UNIQUE KEY `idx-business-slug` (`slug`),
  ADD KEY `idx-business-name` (`name`),
  ADD KEY `idx-business-show_on_home` (`show_on_home`),
  ADD KEY `fk-business-email_template_id` (`email_template_id`);

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
  ADD KEY `idx-contact_submission-business_id` (`business_id`);

--
-- Indices de la tabla `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-email_template-name` (`name`),
  ADD KEY `idx-email_template-is_default` (`is_default`);

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
-- AUTO_INCREMENT de la tabla `business`
--
ALTER TABLE `business`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `site_config`
--
ALTER TABLE `site_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Filtros para la tabla `business`
--
ALTER TABLE `business`
  ADD CONSTRAINT `fk-business-email_template_id` FOREIGN KEY (`email_template_id`) REFERENCES `email_template` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `contact_submission`
--
ALTER TABLE `contact_submission`
  ADD CONSTRAINT `fk-contact_submission-business_id` FOREIGN KEY (`business_id`) REFERENCES `business` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
