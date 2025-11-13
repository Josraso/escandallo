<?php
/**
 * Script de actualización de 1.x a 2.0.0
 * Migra el sistema a multiidioma
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Función ejecutada automáticamente por PrestaShop al actualizar
 */
function upgrade_module_2_0_0($module)
{
    // 1. Verificar si las tablas ya tienen el campo "nombre" (instalación nueva vs actualización)
    $has_nombre_principal = Db::getInstance()->executeS(
        'SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'escandallo_principal` LIKE "nombre"'
    );

    // Solo migrar si es una actualización (tiene campo nombre)
    if (!empty($has_nombre_principal)) {
        // 2. Crear tablas _lang
        $sql = [];

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_principal_lang` (
            `id_principal` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `nombre` varchar(255) NOT NULL,
            PRIMARY KEY (`id_principal`, `id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_parte_lang` (
            `id_parte` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `nombre` varchar(255) NOT NULL,
            PRIMARY KEY (`id_parte`, `id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        // 3. Migrar datos existentes a TODOS los idiomas
        $languages = Language::getLanguages(false);

        // Migrar principales
        $principales = Db::getInstance()->executeS(
            'SELECT id_principal, nombre FROM `' . _DB_PREFIX_ . 'escandallo_principal`'
        );

        if ($principales) {
            foreach ($principales as $principal) {
                foreach ($languages as $lang) {
                    Db::getInstance()->insert('escandallo_principal_lang', [
                        'id_principal' => (int)$principal['id_principal'],
                        'id_lang' => (int)$lang['id_lang'],
                        'nombre' => pSQL($principal['nombre'])
                    ]);
                }
            }
        }

        // Migrar partes
        $partes = Db::getInstance()->executeS(
            'SELECT id_parte, nombre FROM `' . _DB_PREFIX_ . 'escandallo_parte`'
        );

        if ($partes) {
            foreach ($partes as $parte) {
                foreach ($languages as $lang) {
                    Db::getInstance()->insert('escandallo_parte_lang', [
                        'id_parte' => (int)$parte['id_parte'],
                        'id_lang' => (int)$lang['id_lang'],
                        'nombre' => pSQL($parte['nombre'])
                    ]);
                }
            }
        }

        // 4. Eliminar columna "nombre" de las tablas principales
        $sql = [];
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'escandallo_principal` DROP COLUMN `nombre`';
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'escandallo_parte` DROP COLUMN `nombre`';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }
    }

    return true;
}
