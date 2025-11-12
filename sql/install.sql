-- ===================================
-- TABLAS DEL MÓDULO ESCANDALLO
-- ===================================

CREATE TABLE IF NOT EXISTS `PREFIX_escandallo_principal` (
    `id_principal` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(255) NOT NULL,
    `imagen` varchar(255) DEFAULT NULL,
    `activo` tinyint(1) DEFAULT 1,
    `position` int(11) DEFAULT 0,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_principal`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_escandallo_parte` (
    `id_parte` int(11) NOT NULL AUTO_INCREMENT,
    `id_principal` int(11) NOT NULL,
    `nombre` varchar(255) NOT NULL,
    `imagen` varchar(255) DEFAULT NULL,
    `activo` tinyint(1) DEFAULT 1,
    `position` int(11) DEFAULT 0,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_parte`),
    KEY `id_principal` (`id_principal`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_escandallo_producto_parte` (
    `id_escandallo_producto` int(11) NOT NULL AUTO_INCREMENT,
    `id_parte` int(11) NOT NULL,
    `id_product` int(11) NOT NULL,
    `numero_imagen` int(11) NOT NULL,
    `position` int(11) DEFAULT 0,
    `date_add` datetime NOT NULL,
    PRIMARY KEY (`id_escandallo_producto`),
    KEY `id_parte` (`id_parte`),
    KEY `id_product` (`id_product`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

-- ===================================
-- DATOS DE EJEMPLO
-- ===================================

-- Insertar principales de ejemplo
INSERT INTO `PREFIX_escandallo_principal` (`id_principal`, `nombre`, `imagen`, `activo`, `position`, `date_add`, `date_upd`) VALUES
(1, 'Super Furious RR 125', NULL, 1, 0, NOW(), NOW()),
(2, 'MTR MCT E4', NULL, 1, 1, NOW(), NOW()),
(3, 'MTR Adventure', NULL, 1, 2, NOW(), NOW());

-- Insertar partes de ejemplo
INSERT INTO `PREFIX_escandallo_parte` (`id_parte`, `id_principal`, `nombre`, `imagen`, `activo`, `position`, `date_add`, `date_upd`) VALUES
(1, 1, 'Cúpula', NULL, 1, 0, NOW(), NOW()),
(2, 1, 'Cuadro instrumentación', NULL, 1, 1, NOW(), NOW()),
(3, 1, 'Semi manillares', NULL, 1, 2, NOW(), NOW()),
(4, 1, 'Freno delantero', NULL, 1, 3, NOW(), NOW()),
(5, 2, 'Sistema eléctrico', NULL, 1, 0, NOW(), NOW()),
(6, 3, 'Guardabarros delantero', NULL, 1, 0, NOW(), NOW());