-- ===================================
-- TABLAS DEL MÓDULO ESCANDALLO
-- ===================================

CREATE TABLE IF NOT EXISTS `PREFIX_escandallo_principal` (
    `id_principal` int(11) NOT NULL AUTO_INCREMENT,
    `imagen` varchar(255) DEFAULT NULL,
    `activo` tinyint(1) DEFAULT 1,
    `position` int(11) DEFAULT 0,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_principal`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_escandallo_principal_lang` (
    `id_principal` int(11) NOT NULL,
    `id_lang` int(11) NOT NULL,
    `nombre` varchar(255) NOT NULL,
    PRIMARY KEY (`id_principal`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_escandallo_parte` (
    `id_parte` int(11) NOT NULL AUTO_INCREMENT,
    `id_principal` int(11) NOT NULL,
    `imagen` varchar(255) DEFAULT NULL,
    `activo` tinyint(1) DEFAULT 1,
    `position` int(11) DEFAULT 0,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_parte`),
    KEY `id_principal` (`id_principal`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_escandallo_parte_lang` (
    `id_parte` int(11) NOT NULL,
    `id_lang` int(11) NOT NULL,
    `nombre` varchar(255) NOT NULL,
    PRIMARY KEY (`id_parte`, `id_lang`)
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
