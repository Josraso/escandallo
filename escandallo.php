<?php
/**
 * Módulo Escandallo para PrestaShop 1.7, 8 y 9
 * Versión 2.0 - Multiidioma
 *
 * @author    Tu Nombre
 * @copyright Copyright (c) 2025
 * @license   MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Escandallo extends Module
{
    public function __construct()
    {
        $this->name = 'escandallo';
        $this->tab = 'administration';
        $this->version = '2.0.0';
        $this->author = 'Tu Nombre';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Escandallo');
        $this->description = $this->l('Módulo de escandallo para gestión de productos principales, partes y productos finales');
        $this->confirmUninstall = $this->l('¿Estás seguro de que deseas desinstalar este módulo?');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        // Crear tablas
        if (!$this->createTables()) {
            return false;
        }

        // Registrar hooks
        if (!$this->registerHook('displayHeader') ||
            !$this->registerHook('moduleRoutes')) {
            return false;
        }

        // Forzar regeneración de rutas
        $this->clearRoutingCache();

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        // Eliminar tablas
        if (!$this->deleteTables()) {
            return false;
        }

        return true;
    }

    private function createTables()
    {
        $sql = [];

        // Tabla de principales (sin nombre, ahora en _lang)
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_principal` (
            `id_principal` int(11) NOT NULL AUTO_INCREMENT,
            `imagen` varchar(255) DEFAULT NULL,
            `activo` tinyint(1) DEFAULT 1,
            `position` int(11) DEFAULT 0,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_principal`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Tabla de principales - multiidioma
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_principal_lang` (
            `id_principal` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `nombre` varchar(255) NOT NULL,
            PRIMARY KEY (`id_principal`, `id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Tabla de partes/diagramas (sin nombre, ahora en _lang)
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_parte` (
            `id_parte` int(11) NOT NULL AUTO_INCREMENT,
            `id_principal` int(11) NOT NULL,
            `imagen` varchar(255) DEFAULT NULL,
            `activo` tinyint(1) DEFAULT 1,
            `position` int(11) DEFAULT 0,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_parte`),
            KEY `id_principal` (`id_principal`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Tabla de partes - multiidioma
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_parte_lang` (
            `id_parte` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `nombre` varchar(255) NOT NULL,
            PRIMARY KEY (`id_parte`, `id_lang`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Tabla de relaciones producto-parte
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_producto_parte` (
            `id_escandallo_producto` int(11) NOT NULL AUTO_INCREMENT,
            `id_parte` int(11) NOT NULL,
            `id_product` int(11) NOT NULL,
            `numero_imagen` int(11) NOT NULL,
            `position` int(11) DEFAULT 0,
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_escandallo_producto`),
            KEY `id_parte` (`id_parte`),
            KEY `id_product` (`id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    private function deleteTables()
    {
        $sql = [
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'escandallo_producto_parte`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'escandallo_parte_lang`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'escandallo_parte`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'escandallo_principal_lang`',
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'escandallo_principal`'
        ];

        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }

    public function hookModuleRoutes($params)
    {
        return [
            // IMPORTANTE: Las rutas más específicas DEBEN ir PRIMERO
            // Si ponemos 'escandallo' primero, captura todo y no deja pasar las otras

            'module-escandallo-buscar' => [
                'controller' => 'buscar',
                'rule' => 'escandallo/buscar',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'escandallo',
                    'controller' => 'buscar'
                ]
            ],
            'module-escandallo-partes' => [
                'controller' => 'partes',
                'rule' => 'escandallo/partes/{id_principal}',
                'keywords' => [
                    'id_principal' => ['regexp' => '[0-9]+', 'param' => 'id_principal']
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'escandallo',
                    'controller' => 'partes'
                ]
            ],
            'module-escandallo-productos' => [
                'controller' => 'productos',
                'rule' => 'escandallo/productos/{id_parte}',
                'keywords' => [
                    'id_parte' => ['regexp' => '[0-9]+', 'param' => 'id_parte']
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'escandallo',
                    'controller' => 'productos'
                ]
            ],
            // Ruta principal - cambiado de 'index' a 'principal' para evitar conflictos
            'module-escandallo-principal' => [
                'controller' => 'principal',
                'rule' => 'escandallo',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'escandallo',
                    'controller' => 'principal'
                ]
            ]
        ];
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/escandallo.css');
        $this->context->controller->addJS($this->_path . 'views/js/escandallo.js');
    }

    public function getContent()
    {
        $output = '';

        // Regenerar rutas si se solicita
        if (Tools::isSubmit('regenerateRoutes')) {
            $this->clearRoutingCache();
            $output .= $this->displayConfirmation($this->l('Caché limpiada correctamente.'))
                . $this->displayWarning($this->l('IMPORTANTE: Ahora debes ir a Parámetros Avanzados > Rendimiento y hacer clic en "Limpiar caché". También verifica que las URLs amigables estén activadas en Tráfico y SEO > SEO y URLs.'));
        }

        // Procesar formularios
        if (Tools::isSubmit('submitAddPrincipal')) {
            $output .= $this->processAddPrincipal();
        }

        if (Tools::isSubmit('submitAddParte')) {
            $output .= $this->processAddParte();
        }

        if (Tools::isSubmit('submitAddProductoParte')) {
            $output .= $this->processAddProductoParte();
        }

        if (Tools::isSubmit('submitEditPrincipal')) {
            $output .= $this->processEditPrincipal();
        }

        if (Tools::isSubmit('submitEditParte')) {
            $output .= $this->processEditParte();
        }

        if (Tools::isSubmit('submitEditProductoParte')) {
            $output .= $this->processEditProductoParte();
        }

        if (Tools::isSubmit('submitImportCSV')) {
            $output .= $this->processImportCSV();
        }

        if (Tools::isSubmit('submitExportCSV')) {
            $this->processExportCSV();
            exit;
        }

        if (Tools::isSubmit('submitExportZIP')) {
            $this->processExportZIP();
            exit;
        }

        // Acciones de eliminar
        if (Tools::isSubmit('deletePrincipal')) {
            $output .= $this->deletePrincipal(Tools::getValue('id_principal'));
        }

        if (Tools::isSubmit('deleteParte')) {
            $output .= $this->deleteParte(Tools::getValue('id_parte'));
        }

        if (Tools::isSubmit('deleteProductoParte')) {
            $output .= $this->deleteProductoParte(Tools::getValue('id_escandallo_producto'));
        }

        return $output . $this->renderConfigForm();
    }

    private function processAddPrincipal()
    {
        $nombres = []; // Array de nombres por idioma
        $languages = Language::getLanguages(false);

        // Recoger nombres de todos los idiomas
        foreach ($languages as $lang) {
            $nombre = Tools::getValue('nombre_principal_' . $lang['id_lang']);
            if (!empty($nombre)) {
                $nombres[$lang['id_lang']] = $nombre;
            }
        }

        if (empty($nombres)) {
            return $this->displayError($this->l('El nombre del principal es obligatorio (al menos un idioma)'));
        }

        $imagen = $this->uploadImage('imagen_principal', 'principales');

        // 1. Insertar en tabla principal (sin nombre)
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'escandallo_principal`
                (`imagen`, `date_add`, `date_upd`)
                VALUES ("' . pSQL($imagen) . '", NOW(), NOW())';

        if (!Db::getInstance()->execute($sql)) {
            return $this->displayError($this->l('Error al añadir el principal'));
        }

        // 2. Obtener ID insertado
        $id_principal = (int)Db::getInstance()->Insert_ID();

        // 3. Insertar nombres en todas las lenguas
        foreach ($nombres as $id_lang => $nombre) {
            Db::getInstance()->insert('escandallo_principal_lang', [
                'id_principal' => $id_principal,
                'id_lang' => (int)$id_lang,
                'nombre' => pSQL($nombre)
            ]);
        }

        return $this->displayConfirmation($this->l('Principal añadido correctamente'));
    }

    private function processAddParte()
    {
        $id_principal = (int)Tools::getValue('id_principal_parte');
        $nombres = []; // Array de nombres por idioma
        $languages = Language::getLanguages(false);

        // Recoger nombres de todos los idiomas
        foreach ($languages as $lang) {
            $nombre = Tools::getValue('nombre_parte_' . $lang['id_lang']);
            if (!empty($nombre)) {
                $nombres[$lang['id_lang']] = $nombre;
            }
        }

        if (empty($nombres) || $id_principal <= 0) {
            return $this->displayError($this->l('Todos los campos son obligatorios'));
        }

        $imagen = $this->uploadImage('imagen_parte', 'partes');

        // 1. Insertar en tabla parte (sin nombre)
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'escandallo_parte`
                (`id_principal`, `imagen`, `date_add`, `date_upd`)
                VALUES (' . $id_principal . ', "' . pSQL($imagen) . '", NOW(), NOW())';

        if (!Db::getInstance()->execute($sql)) {
            return $this->displayError($this->l('Error al añadir la parte'));
        }

        // 2. Obtener ID insertado
        $id_parte = (int)Db::getInstance()->Insert_ID();

        // 3. Insertar nombres en todas las lenguas
        foreach ($nombres as $id_lang => $nombre) {
            Db::getInstance()->insert('escandallo_parte_lang', [
                'id_parte' => $id_parte,
                'id_lang' => (int)$id_lang,
                'nombre' => pSQL($nombre)
            ]);
        }

        return $this->displayConfirmation($this->l('Parte añadida correctamente'));
    }

    private function processAddProductoParte()
    {
        $id_parte = (int)Tools::getValue('id_parte_producto');
        $id_product = (int)Tools::getValue('id_product');
        $numero_imagen = (int)Tools::getValue('numero_imagen');

        if ($id_parte <= 0 || $id_product <= 0 || $numero_imagen <= 0) {
            return $this->displayError($this->l('Todos los campos son obligatorios'));
        }

        // Verificar que el producto existe
        $product = new Product($id_product, false, $this->context->language->id);
        if (!Validate::isLoadedObject($product)) {
            return $this->displayError($this->l('El producto no existe'));
        }

        // Marcar producto como oculto
        $product->visibility = 'none';
        $product->save();

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'escandallo_producto_parte` 
                (`id_parte`, `id_product`, `numero_imagen`, `date_add`) 
                VALUES (' . $id_parte . ', ' . $id_product . ', ' . $numero_imagen . ', NOW())';

        if (Db::getInstance()->execute($sql)) {
            return $this->displayConfirmation($this->l('Producto asociado correctamente'));
        }

        return $this->displayError($this->l('Error al asociar el producto'));
    }

    private function processEditPrincipal()
    {
        $id_principal = (int)Tools::getValue('id_principal_edit');
        $imagen_actual = Tools::getValue('imagen_actual_principal');

        if ($id_principal <= 0) {
            return $this->displayError($this->l('ID principal es obligatorio'));
        }

        // Recoger nombres de todos los idiomas
        $nombres = [];
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $nombre = Tools::getValue('nombre_principal_edit_' . $lang['id_lang']);
            if (!empty($nombre)) {
                $nombres[$lang['id_lang']] = $nombre;
            }
        }

        if (empty($nombres)) {
            return $this->displayError($this->l('El nombre es obligatorio (al menos un idioma)'));
        }

        // Intentar subir nueva imagen si se proporcionó
        $imagen = $this->uploadImage('imagen_principal_edit', 'principales');
        if ($imagen === null) {
            $imagen = $imagen_actual;
        }

        // Actualizar tabla principal (sin nombre)
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'escandallo_principal`
                SET `imagen` = "' . pSQL($imagen) . '",
                    `date_upd` = NOW()
                WHERE `id_principal` = ' . $id_principal;

        if (!Db::getInstance()->execute($sql)) {
            return $this->displayError($this->l('Error al actualizar el principal'));
        }

        // Actualizar nombres en TODOS los idiomas
        foreach ($nombres as $id_lang => $nombre) {
            $exists = Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'escandallo_principal_lang`
                WHERE `id_principal` = ' . $id_principal . ' AND `id_lang` = ' . (int)$id_lang
            );

            if ($exists) {
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'escandallo_principal_lang`
                    SET `nombre` = "' . pSQL($nombre) . '"
                    WHERE `id_principal` = ' . $id_principal . ' AND `id_lang` = ' . (int)$id_lang
                );
            } else {
                Db::getInstance()->insert('escandallo_principal_lang', [
                    'id_principal' => $id_principal,
                    'id_lang' => (int)$id_lang,
                    'nombre' => pSQL($nombre)
                ]);
            }
        }

        return $this->displayConfirmation($this->l('Principal actualizado correctamente'));
    }

    private function processEditParte()
    {
        $id_parte = (int)Tools::getValue('id_parte_edit');
        $id_principal = (int)Tools::getValue('id_principal_parte_edit');
        $imagen_actual = Tools::getValue('imagen_actual_parte');

        if ($id_parte <= 0 || $id_principal <= 0) {
            return $this->displayError($this->l('Todos los campos son obligatorios'));
        }

        // Recoger nombres de todos los idiomas
        $nombres = [];
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $nombre = Tools::getValue('nombre_parte_edit_' . $lang['id_lang']);
            if (!empty($nombre)) {
                $nombres[$lang['id_lang']] = $nombre;
            }
        }

        if (empty($nombres)) {
            return $this->displayError($this->l('El nombre es obligatorio (al menos un idioma)'));
        }

        // Intentar subir nueva imagen si se proporcionó
        $imagen = $this->uploadImage('imagen_parte_edit', 'partes');
        if ($imagen === null) {
            $imagen = $imagen_actual;
        }

        // Actualizar tabla parte (sin nombre)
        $sql = 'UPDATE `' . _DB_PREFIX_ . 'escandallo_parte`
                SET `id_principal` = ' . $id_principal . ',
                    `imagen` = "' . pSQL($imagen) . '",
                    `date_upd` = NOW()
                WHERE `id_parte` = ' . $id_parte;

        if (!Db::getInstance()->execute($sql)) {
            return $this->displayError($this->l('Error al actualizar la parte'));
        }

        // Actualizar nombres en TODOS los idiomas
        foreach ($nombres as $id_lang => $nombre) {
            $exists = Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'escandallo_parte_lang`
                WHERE `id_parte` = ' . $id_parte . ' AND `id_lang` = ' . (int)$id_lang
            );

            if ($exists) {
                Db::getInstance()->execute(
                    'UPDATE `' . _DB_PREFIX_ . 'escandallo_parte_lang`
                    SET `nombre` = "' . pSQL($nombre) . '"
                    WHERE `id_parte` = ' . $id_parte . ' AND `id_lang` = ' . (int)$id_lang
                );
            } else {
                Db::getInstance()->insert('escandallo_parte_lang', [
                    'id_parte' => $id_parte,
                    'id_lang' => (int)$id_lang,
                    'nombre' => pSQL($nombre)
                ]);
            }
        }

        return $this->displayConfirmation($this->l('Parte actualizada correctamente'));
    }

    private function processEditProductoParte()
    {
        $id_escandallo_producto = (int)Tools::getValue('id_escandallo_producto_edit');
        $id_parte = (int)Tools::getValue('id_parte_producto_edit');
        $id_product = (int)Tools::getValue('id_product_edit');
        $numero_imagen = (int)Tools::getValue('numero_imagen_edit');

        if ($id_escandallo_producto <= 0 || $id_parte <= 0 || $id_product <= 0 || $numero_imagen <= 0) {
            return $this->displayError($this->l('Todos los campos son obligatorios'));
        }

        // Verificar que el producto existe
        $product = new Product($id_product, false, $this->context->language->id);
        if (!Validate::isLoadedObject($product)) {
            return $this->displayError($this->l('El producto no existe'));
        }

        // Marcar producto como oculto
        $product->visibility = 'none';
        $product->save();

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'escandallo_producto_parte`
                SET `id_parte` = ' . $id_parte . ',
                    `id_product` = ' . $id_product . ',
                    `numero_imagen` = ' . $numero_imagen . '
                WHERE `id_escandallo_producto` = ' . $id_escandallo_producto;

        if (Db::getInstance()->execute($sql)) {
            return $this->displayConfirmation($this->l('Producto asociado actualizado correctamente'));
        }

        return $this->displayError($this->l('Error al actualizar el producto asociado'));
    }

    private function buildLanguageMap()
    {
        $languages = Language::getLanguages(false);
        $map = [];
        foreach ($languages as $lang) {
            $map[strtolower($lang['iso_code'])] = (int)$lang['id_lang'];
        }
        return $map;
    }

    private function processImportCSV()
    {
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != UPLOAD_ERR_OK) {
            return $this->displayError($this->l('Error al subir el archivo CSV'));
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($file, 'r');

        if ($handle === false) {
            return $this->displayError($this->l('No se pudo abrir el archivo CSV'));
        }

        $header = fgetcsv($handle, 0, ';');
        $imported = 0;
        $errors = 0;

        // Detectar formato multiidioma analizando cabeceras
        $langMap = $this->buildLanguageMap();
        $isMultilang = false;
        $colMap = []; // Mapeo de columnas por nombre

        foreach ($header as $index => $column) {
            $colMap[trim(strtolower($column))] = $index;
        }

        // Detectar columnas de idioma para principales y partes
        $langCols = ['principal' => [], 'parte' => [], 'producto' => [], 'descripcion' => []];
        foreach ($header as $index => $column) {
            $col = trim(strtolower($column));
            if (preg_match('/^nombre_principal_([a-z]{2})$/', $col, $m)) {
                $isMultilang = true;
                if (isset($langMap[$m[1]])) {
                    $langCols['principal'][$langMap[$m[1]]] = $index;
                }
            }
            if (preg_match('/^nombre_parte_([a-z]{2})$/', $col, $m)) {
                if (isset($langMap[$m[1]])) {
                    $langCols['parte'][$langMap[$m[1]]] = $index;
                }
            }
            if (preg_match('/^nombre_producto_([a-z]{2})$/', $col, $m)) {
                if (isset($langMap[$m[1]])) {
                    $langCols['producto'][$langMap[$m[1]]] = $index;
                }
            }
            if (preg_match('/^descripcion_([a-z]{2})$/', $col, $m)) {
                if (isset($langMap[$m[1]])) {
                    $langCols['descripcion'][$langMap[$m[1]]] = $index;
                }
            }
        }

        // Determinar índices de columnas fijas
        // Formato viejo: posiciones fijas 0-15
        // Formato nuevo: buscar por nombre de cabecera
        $getCol = function($name, $default = null) use ($colMap) {
            return isset($colMap[$name]) ? $colMap[$name] : $default;
        };

        $col_id_principal = $getCol('id_principal', 0);
        $col_imagen_principal = $getCol('imagen_principal', $isMultilang ? null : 2);
        $col_id_parte = $getCol('id_parte', $isMultilang ? null : 3);
        $col_imagen_parte = $getCol('imagen_parte', $isMultilang ? null : 5);
        $col_id_product = $getCol('id_product', $isMultilang ? null : 6);
        $col_numero_imagen = $getCol('numero_imagen', $isMultilang ? null : 7);
        $col_referencia = $getCol('referencia', $isMultilang ? null : 8);
        $col_precio = $getCol('precio', $isMultilang ? null : 11);
        $col_imagen_producto = $getCol('imagen_producto', $isMultilang ? null : 12);
        $col_stock = $getCol('stock', $isMultilang ? null : 13);
        $col_id_categoria = $getCol('id_categoria', $isMultilang ? null : 14);
        $col_id_tax = $getCol('id_tax_rules_group', $isMultilang ? null : 15);

        // Columnas de formato viejo (sin multiidioma)
        $col_nombre_principal = $getCol('nombre_principal', 1);
        $col_nombre_parte = $getCol('nombre_parte', 4);
        $col_nombre_producto = $getCol('nombre_producto', 9);
        $col_descripcion = $getCol('descripcion', 10);

        $languages = Language::getLanguages(false);

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $id_principal = isset($data[$col_id_principal]) ? (int)$data[$col_id_principal] : 0;
            $imagen_principal = ($col_imagen_principal !== null && isset($data[$col_imagen_principal])) ? $data[$col_imagen_principal] : '';
            $id_parte = ($col_id_parte !== null && isset($data[$col_id_parte])) ? (int)$data[$col_id_parte] : 0;
            $imagen_parte = ($col_imagen_parte !== null && isset($data[$col_imagen_parte])) ? $data[$col_imagen_parte] : '';
            $id_product = ($col_id_product !== null && isset($data[$col_id_product])) ? (int)$data[$col_id_product] : 0;
            $numero_imagen = ($col_numero_imagen !== null && isset($data[$col_numero_imagen])) ? (int)$data[$col_numero_imagen] : 0;
            $referencia = ($col_referencia !== null && isset($data[$col_referencia])) ? $data[$col_referencia] : '';
            $precio = ($col_precio !== null && isset($data[$col_precio])) ? (float)$data[$col_precio] : 0;
            $imagen_producto = ($col_imagen_producto !== null && isset($data[$col_imagen_producto])) ? $data[$col_imagen_producto] : '';
            $stock = ($col_stock !== null && isset($data[$col_stock])) ? (int)$data[$col_stock] : 0;
            $id_category = ($col_id_categoria !== null && isset($data[$col_id_categoria])) ? (int)$data[$col_id_categoria] : 0;
            $id_tax_rules_group = ($col_id_tax !== null && isset($data[$col_id_tax])) ? (int)$data[$col_id_tax] : 0;

            // Obtener nombres multiidioma o formato viejo
            $nombres_principal = [];
            $nombres_parte = [];
            $nombres_producto = [];
            $descripciones = [];

            if ($isMultilang && !empty($langCols['principal'])) {
                foreach ($langCols['principal'] as $id_lang => $colIndex) {
                    if (isset($data[$colIndex]) && !empty(trim($data[$colIndex]))) {
                        $nombres_principal[$id_lang] = $data[$colIndex];
                    }
                }
            } else {
                $nombre = isset($data[$col_nombre_principal]) ? $data[$col_nombre_principal] : '';
                if (!empty(trim($nombre))) {
                    foreach ($languages as $lang) {
                        $nombres_principal[$lang['id_lang']] = $nombre;
                    }
                }
            }

            if ($isMultilang && !empty($langCols['parte'])) {
                foreach ($langCols['parte'] as $id_lang => $colIndex) {
                    if (isset($data[$colIndex]) && !empty(trim($data[$colIndex]))) {
                        $nombres_parte[$id_lang] = $data[$colIndex];
                    }
                }
            } else {
                $nombre = isset($data[$col_nombre_parte]) ? $data[$col_nombre_parte] : '';
                if (!empty(trim($nombre))) {
                    foreach ($languages as $lang) {
                        $nombres_parte[$lang['id_lang']] = $nombre;
                    }
                }
            }

            if ($isMultilang && !empty($langCols['producto'])) {
                foreach ($langCols['producto'] as $id_lang => $colIndex) {
                    if (isset($data[$colIndex]) && !empty(trim($data[$colIndex]))) {
                        $nombres_producto[$id_lang] = $data[$colIndex];
                    }
                }
            } else {
                $nombre = isset($data[$col_nombre_producto]) ? $data[$col_nombre_producto] : '';
                if (!empty(trim($nombre))) {
                    $nombres_producto[$this->context->language->id] = $nombre;
                }
            }

            if ($isMultilang && !empty($langCols['descripcion'])) {
                foreach ($langCols['descripcion'] as $id_lang => $colIndex) {
                    if (isset($data[$colIndex]) && !empty(trim($data[$colIndex]))) {
                        $descripciones[$id_lang] = $data[$colIndex];
                    }
                }
            } else {
                $desc = isset($data[$col_descripcion]) ? $data[$col_descripcion] : '';
                if (!empty(trim($desc))) {
                    $descripciones[$this->context->language->id] = $desc;
                }
            }

            // Para compatibilidad con el nombre de producto (necesitamos al menos uno)
            $nombre_producto = reset($nombres_producto) ?: '';
            $descripcion = reset($descripciones) ?: '';

            try {
                // VALIDACIÓN: Solo crear principal si tiene datos válidos
                if ($id_principal > 0 && !empty($nombres_principal)) {
                    $principal_exists = Db::getInstance()->getValue(
                        'SELECT id_principal FROM `' . _DB_PREFIX_ . 'escandallo_principal` WHERE id_principal = ' . $id_principal
                    );

                    if (!$principal_exists) {
                        Db::getInstance()->insert('escandallo_principal', [
                            'id_principal' => $id_principal,
                            'imagen' => pSQL($imagen_principal),
                            'date_add' => date('Y-m-d H:i:s'),
                            'date_upd' => date('Y-m-d H:i:s')
                        ]);

                        // Insertar nombres en los idiomas disponibles
                        foreach ($nombres_principal as $id_lang => $nombre) {
                            Db::getInstance()->insert('escandallo_principal_lang', [
                                'id_principal' => $id_principal,
                                'id_lang' => (int)$id_lang,
                                'nombre' => pSQL($nombre)
                            ]);
                        }
                    }
                }

                // VALIDACIÓN: Solo crear parte si tiene datos válidos
                if ($id_parte > 0 && !empty($nombres_parte) && $id_principal > 0) {
                    $parte_exists = Db::getInstance()->getValue(
                        'SELECT id_parte FROM `' . _DB_PREFIX_ . 'escandallo_parte` WHERE id_parte = ' . $id_parte
                    );

                    if (!$parte_exists) {
                        Db::getInstance()->insert('escandallo_parte', [
                            'id_parte' => $id_parte,
                            'id_principal' => $id_principal,
                            'imagen' => pSQL($imagen_parte),
                            'date_add' => date('Y-m-d H:i:s'),
                            'date_upd' => date('Y-m-d H:i:s')
                        ]);

                        // Insertar nombres en los idiomas disponibles
                        foreach ($nombres_parte as $id_lang => $nombre) {
                            Db::getInstance()->insert('escandallo_parte_lang', [
                                'id_parte' => $id_parte,
                                'id_lang' => (int)$id_lang,
                                'nombre' => pSQL($nombre)
                            ]);
                        }
                    }
                }

                // VALIDACIÓN: Solo procesar producto si tiene datos válidos
                if ($id_product > 0 && !empty(trim($nombre_producto)) && !empty(trim($referencia))) {
                    // Crear o actualizar producto
                    $product_exists = Db::getInstance()->getValue(
                        'SELECT id_product FROM `' . _DB_PREFIX_ . 'product` WHERE id_product = ' . $id_product
                    );

                    // Preparar arrays multiidioma para productos
                    $product_names = !empty($nombres_producto) ? $nombres_producto : [$this->context->language->id => $nombre_producto];
                    $product_descs = !empty($descripciones) ? $descripciones : [$this->context->language->id => $descripcion];
                    $product_rewrites = [];
                    foreach ($product_names as $lid => $pname) {
                        $product_rewrites[$lid] = Tools::link_rewrite($pname);
                    }

                    if (!$product_exists) {
                        // Crear producto nuevo
                        $product = new Product();
                        $product->id_product = $id_product;
                        $product->reference = $referencia;
                        $product->name = $product_names;
                        $product->description = $product_descs;
                        $product->link_rewrite = $product_rewrites;
                        $product->price = $precio;
                        $product->visibility = 'none';
                        $product->active = 1;
                        $product->id_category_default = $id_category;
                        $product->id_tax_rules_group = $id_tax_rules_group > 0 ? $id_tax_rules_group : 1;

                        if ($product->add()) {
                            // Añadir a categoría CORRECTAMENTE
                            $product->updateCategories([$id_category]);

                            // Actualizar stock
                            StockAvailable::setQuantity($product->id, 0, $stock);

                            // Subir imagen si existe
                            if (!empty($imagen_producto)) {
                                $image_path = dirname(__FILE__) . '/views/img/productos/' . $imagen_producto;
                                if (file_exists($image_path)) {
                                    $image = new Image();
                                    $image->id_product = $product->id;
                                    $image->position = Image::getHighestPosition($product->id) + 1;
                                    $image->cover = true;

                                    if ($image->add()) {
                                        $image->associateTo($this->context->shop->id);

                                        // Copiar imagen a PrestaShop correctamente
                                        $new_path = $image->getPathForCreation();

                                        // Detectar tipo de imagen
                                        $imageInfo = @getimagesize($image_path);
                                        $imageType = $imageInfo ? $imageInfo[2] : IMAGETYPE_JPEG;

                                        // Extensión según tipo
                                        $ext = '.jpg';
                                        if ($imageType === IMAGETYPE_PNG) {
                                            $ext = '.png';
                                        } elseif ($imageType === IMAGETYPE_GIF) {
                                            $ext = '.gif';
                                        }

                                        // Copiar imagen original
                                        if (copy($image_path, $new_path . $ext)) {
                                            // Generar todas las miniaturas
                                            $imagesTypes = ImageType::getImagesTypes('products');
                                            foreach ($imagesTypes as $imageType) {
                                                ImageManager::resize(
                                                    $image_path,
                                                    $new_path . '-' . stripslashes($imageType['name']) . $ext,
                                                    $imageType['width'],
                                                    $imageType['height']
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        // Producto existe, ACTUALIZAR con datos del CSV
                        $product = new Product($id_product);
                        $product->reference = $referencia;
                        $product->name = $product_names;
                        $product->description = $product_descs;
                        $product->link_rewrite = $product_rewrites;
                        $product->price = $precio;
                        $product->visibility = 'none';
                        $product->active = 1;
                        $product->id_category_default = $id_category;
                        $product->id_tax_rules_group = $id_tax_rules_group > 0 ? $id_tax_rules_group : 1;

                        $product->save();

                        // Actualizar categorías
                        $product->updateCategories([$id_category]);

                        // Actualizar stock
                        StockAvailable::setQuantity($product->id, 0, $stock);

                        // Actualizar imagen si existe
                        if (!empty($imagen_producto)) {
                            $image_path = dirname(__FILE__) . '/views/img/productos/' . $imagen_producto;
                            if (file_exists($image_path)) {
                                // Eliminar imágenes anteriores
                                $images = $product->getImages($this->context->language->id);
                                foreach ($images as $img) {
                                    $image_obj = new Image($img['id_image']);
                                    $image_obj->delete();
                                }

                                // Añadir nueva imagen
                                $image = new Image();
                                $image->id_product = $product->id;
                                $image->position = 1;
                                $image->cover = true;

                                if ($image->add()) {
                                    $image->associateTo($this->context->shop->id);

                                    $new_path = $image->getPathForCreation();
                                    $imageInfo = @getimagesize($image_path);
                                    $imageType = $imageInfo ? $imageInfo[2] : IMAGETYPE_JPEG;

                                    $ext = '.jpg';
                                    if ($imageType === IMAGETYPE_PNG) {
                                        $ext = '.png';
                                    } elseif ($imageType === IMAGETYPE_GIF) {
                                        $ext = '.gif';
                                    }

                                    if (copy($image_path, $new_path . $ext)) {
                                        $imagesTypes = ImageType::getImagesTypes('products');
                                        foreach ($imagesTypes as $imageType) {
                                            ImageManager::resize(
                                                $image_path,
                                                $new_path . '-' . stripslashes($imageType['name']) . $ext,
                                                $imageType['width'],
                                                $imageType['height']
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                // VALIDACIÓN: Solo asociar producto a parte si AMBOS existen
                if ($id_parte > 0 && $id_product > 0) {
                    $asociacion_exists = Db::getInstance()->getValue(
                        'SELECT id_escandallo_producto FROM `' . _DB_PREFIX_ . 'escandallo_producto_parte`
                        WHERE id_parte = ' . $id_parte . ' AND id_product = ' . $id_product
                    );

                    if (!$asociacion_exists) {
                        Db::getInstance()->insert('escandallo_producto_parte', [
                            'id_parte' => $id_parte,
                            'id_product' => $id_product,
                            'numero_imagen' => $numero_imagen,
                            'date_add' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                $imported++;
            } catch (Exception $e) {
                $errors++;
            }
        }

        fclose($handle);

        return $this->displayConfirmation(
            sprintf($this->l('Importación completada: %d registros importados, %d errores'), $imported, $errors)
        );
    }

    private function processExportCSV()
    {
        // Obtener idiomas activos
        $languages = Language::getLanguages(false);
        $langMap = []; // id_lang => iso_code
        foreach ($languages as $lang) {
            $langMap[$lang['id_lang']] = strtolower($lang['iso_code']);
        }

        // Query base (sin datos de idioma, se cargan aparte)
        $sql = 'SELECT
                    ep.id_principal,
                    ep.imagen as imagen_principal,
                    epa.id_parte,
                    epa.imagen as imagen_parte,
                    epp.id_product,
                    epp.numero_imagen,
                    p.reference,
                    p.price,
                    p.id_category_default,
                    p.id_tax_rules_group
                FROM `' . _DB_PREFIX_ . 'escandallo_principal` ep
                LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte` epa ON epa.id_principal = ep.id_principal
                LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_producto_parte` epp ON epp.id_parte = epa.id_parte
                LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = epp.id_product
                ORDER BY ep.id_principal, epa.id_parte, epp.numero_imagen';

        $results = Db::getInstance()->executeS($sql);

        if (!$results || count($results) == 0) {
            header('Content-Type: text/html; charset=utf-8');
            echo $this->displayError($this->l('No hay datos para exportar'));
            return;
        }

        // Precargar todos los nombres multiidioma
        $principalNames = [];
        $rows = Db::getInstance()->executeS('SELECT id_principal, id_lang, nombre FROM `' . _DB_PREFIX_ . 'escandallo_principal_lang`');
        foreach ($rows as $r) {
            $principalNames[$r['id_principal']][$r['id_lang']] = $r['nombre'];
        }

        $parteNames = [];
        $rows = Db::getInstance()->executeS('SELECT id_parte, id_lang, nombre FROM `' . _DB_PREFIX_ . 'escandallo_parte_lang`');
        foreach ($rows as $r) {
            $parteNames[$r['id_parte']][$r['id_lang']] = $r['nombre'];
        }

        $productNames = [];
        $productDescs = [];
        $rows = Db::getInstance()->executeS('SELECT id_product, id_lang, name, description FROM `' . _DB_PREFIX_ . 'product_lang`');
        foreach ($rows as $r) {
            $productNames[$r['id_product']][$r['id_lang']] = $r['name'];
            $productDescs[$r['id_product']][$r['id_lang']] = $r['description'];
        }

        // Nombre del archivo
        $filename = 'escandallo_export_multilang_' . date('Y-m-d_H-i-s') . '.csv';

        // Headers para descarga
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Cabecera dinámica con columnas por idioma
        $headerRow = ['id_principal'];
        foreach ($langMap as $id_lang => $iso) {
            $headerRow[] = 'nombre_principal_' . $iso;
        }
        $headerRow[] = 'imagen_principal';
        $headerRow[] = 'id_parte';
        foreach ($langMap as $id_lang => $iso) {
            $headerRow[] = 'nombre_parte_' . $iso;
        }
        $headerRow[] = 'imagen_parte';
        $headerRow[] = 'id_product';
        $headerRow[] = 'numero_imagen';
        $headerRow[] = 'referencia';
        foreach ($langMap as $id_lang => $iso) {
            $headerRow[] = 'nombre_producto_' . $iso;
        }
        foreach ($langMap as $id_lang => $iso) {
            $headerRow[] = 'descripcion_' . $iso;
        }
        $headerRow = array_merge($headerRow, ['precio', 'imagen_producto', 'stock', 'id_categoria', 'id_tax_rules_group']);

        fputcsv($output, $headerRow, ';');

        // Datos
        foreach ($results as $row) {
            $dataRow = [$row['id_principal'] ?: ''];

            // Nombres principal por idioma
            foreach ($langMap as $id_lang => $iso) {
                $dataRow[] = isset($principalNames[$row['id_principal']][$id_lang])
                    ? $principalNames[$row['id_principal']][$id_lang] : '';
            }
            $dataRow[] = $row['imagen_principal'] ?: '';
            $dataRow[] = $row['id_parte'] ?: '';

            // Nombres parte por idioma
            foreach ($langMap as $id_lang => $iso) {
                $dataRow[] = ($row['id_parte'] && isset($parteNames[$row['id_parte']][$id_lang]))
                    ? $parteNames[$row['id_parte']][$id_lang] : '';
            }
            $dataRow[] = $row['imagen_parte'] ?: '';
            $dataRow[] = $row['id_product'] ?: 0;
            $dataRow[] = $row['numero_imagen'] ?: '';
            $dataRow[] = $row['reference'] ?: '';

            // Nombres producto por idioma
            foreach ($langMap as $id_lang => $iso) {
                $dataRow[] = ($row['id_product'] && isset($productNames[$row['id_product']][$id_lang]))
                    ? $productNames[$row['id_product']][$id_lang] : '';
            }

            // Descripciones por idioma
            foreach ($langMap as $id_lang => $iso) {
                $dataRow[] = ($row['id_product'] && isset($productDescs[$row['id_product']][$id_lang]))
                    ? strip_tags($productDescs[$row['id_product']][$id_lang]) : '';
            }

            // Stock e imagen producto
            $stock = 0;
            $imagen_producto = '';
            if ($row['id_product']) {
                $stock = StockAvailable::getQuantityAvailableByProduct($row['id_product'], 0);
                $images = Image::getImages($this->context->language->id, $row['id_product']);
                if (!empty($images) && isset($images[0])) {
                    $imagen_producto = $images[0]['id_image'] . '.jpg';
                }
            }

            $dataRow = array_merge($dataRow, [
                $row['price'] ?: 0,
                $imagen_producto,
                $stock,
                $row['id_category_default'] ?: 0,
                $row['id_tax_rules_group'] ?: 1
            ]);

            fputcsv($output, $dataRow, ';');
        }

        fclose($output);
    }

    private function processExportZIP()
    {
        // Verificar que ZipArchive esté disponible
        if (!class_exists('ZipArchive')) {
            header('Content-Type: text/html; charset=utf-8');
            echo $this->displayError($this->l('ZipArchive no está disponible en el servidor'));
            return;
        }

        $zipFilename = 'escandallo_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = sys_get_temp_dir() . '/' . $zipFilename;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            header('Content-Type: text/html; charset=utf-8');
            echo $this->displayError($this->l('No se pudo crear el archivo ZIP'));
            return;
        }

        // 1. Crear CSV en memoria (formato multiidioma)
        $csvContent = chr(0xEF).chr(0xBB).chr(0xBF); // BOM UTF-8

        // Obtener idiomas activos
        $languages = Language::getLanguages(false);
        $langMap = [];
        foreach ($languages as $lang) {
            $langMap[$lang['id_lang']] = strtolower($lang['iso_code']);
        }

        // Query base
        $sql = 'SELECT
                    ep.id_principal,
                    ep.imagen as imagen_principal,
                    epa.id_parte,
                    epa.imagen as imagen_parte,
                    epp.id_product,
                    epp.numero_imagen,
                    p.reference,
                    p.price,
                    p.id_category_default,
                    p.id_tax_rules_group
                FROM `' . _DB_PREFIX_ . 'escandallo_principal` ep
                LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte` epa ON epa.id_principal = ep.id_principal
                LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_producto_parte` epp ON epp.id_parte = epa.id_parte
                LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.id_product = epp.id_product
                ORDER BY ep.id_principal, epa.id_parte, epp.numero_imagen';

        $results = Db::getInstance()->executeS($sql);

        // Precargar nombres multiidioma
        $principalNames = [];
        $rows = Db::getInstance()->executeS('SELECT id_principal, id_lang, nombre FROM `' . _DB_PREFIX_ . 'escandallo_principal_lang`');
        foreach ($rows as $r) { $principalNames[$r['id_principal']][$r['id_lang']] = $r['nombre']; }

        $parteNames = [];
        $rows = Db::getInstance()->executeS('SELECT id_parte, id_lang, nombre FROM `' . _DB_PREFIX_ . 'escandallo_parte_lang`');
        foreach ($rows as $r) { $parteNames[$r['id_parte']][$r['id_lang']] = $r['nombre']; }

        $productNames = [];
        $productDescs = [];
        $rows = Db::getInstance()->executeS('SELECT id_product, id_lang, name, description FROM `' . _DB_PREFIX_ . 'product_lang`');
        foreach ($rows as $r) {
            $productNames[$r['id_product']][$r['id_lang']] = $r['name'];
            $productDescs[$r['id_product']][$r['id_lang']] = $r['description'];
        }

        // Cabecera dinámica
        $header = ['id_principal'];
        foreach ($langMap as $id_lang => $iso) { $header[] = 'nombre_principal_' . $iso; }
        $header[] = 'imagen_principal';
        $header[] = 'id_parte';
        foreach ($langMap as $id_lang => $iso) { $header[] = 'nombre_parte_' . $iso; }
        $header[] = 'imagen_parte';
        $header[] = 'id_product';
        $header[] = 'numero_imagen';
        $header[] = 'referencia';
        foreach ($langMap as $id_lang => $iso) { $header[] = 'nombre_producto_' . $iso; }
        foreach ($langMap as $id_lang => $iso) { $header[] = 'descripcion_' . $iso; }
        $header = array_merge($header, ['precio', 'imagen_producto', 'stock', 'id_categoria', 'id_tax_rules_group']);
        $csvContent .= implode(';', $header) . "\n";

        // Datos CSV
        if ($results) {
            foreach ($results as $row) {
                $stock = 0;
                $imagen_producto = '';

                if ($row['id_product']) {
                    $stock = StockAvailable::getQuantityAvailableByProduct($row['id_product'], 0);
                    $images = Image::getImages($this->context->language->id, $row['id_product']);
                    if (!empty($images) && isset($images[0])) {
                        $imagen_producto = $images[0]['id_image'] . '.jpg';
                    }
                }

                $line = [$row['id_principal'] ?: ''];
                foreach ($langMap as $id_lang => $iso) {
                    $line[] = '"' . str_replace('"', '""', isset($principalNames[$row['id_principal']][$id_lang]) ? $principalNames[$row['id_principal']][$id_lang] : '') . '"';
                }
                $line[] = '"' . str_replace('"', '""', $row['imagen_principal'] ?: '') . '"';
                $line[] = $row['id_parte'] ?: '';
                foreach ($langMap as $id_lang => $iso) {
                    $line[] = '"' . str_replace('"', '""', ($row['id_parte'] && isset($parteNames[$row['id_parte']][$id_lang])) ? $parteNames[$row['id_parte']][$id_lang] : '') . '"';
                }
                $line[] = '"' . str_replace('"', '""', $row['imagen_parte'] ?: '') . '"';
                $line[] = $row['id_product'] ?: 0;
                $line[] = $row['numero_imagen'] ?: '';
                $line[] = '"' . str_replace('"', '""', $row['reference'] ?: '') . '"';
                foreach ($langMap as $id_lang => $iso) {
                    $line[] = '"' . str_replace('"', '""', ($row['id_product'] && isset($productNames[$row['id_product']][$id_lang])) ? $productNames[$row['id_product']][$id_lang] : '') . '"';
                }
                foreach ($langMap as $id_lang => $iso) {
                    $line[] = '"' . str_replace('"', '""', ($row['id_product'] && isset($productDescs[$row['id_product']][$id_lang])) ? strip_tags($productDescs[$row['id_product']][$id_lang]) : '') . '"';
                }
                $line = array_merge($line, [
                    $row['price'] ?: 0,
                    '"' . str_replace('"', '""', $imagen_producto) . '"',
                    $stock,
                    $row['id_category_default'] ?: 0,
                    $row['id_tax_rules_group'] ?: 1
                ]);
                $csvContent .= implode(';', $line) . "\n";
            }
        }

        // Añadir CSV al ZIP
        $zip->addFromString('escandallo_export.csv', $csvContent);

        // 2. Añadir carpetas de imágenes
        $modulePath = dirname(__FILE__);

        // Añadir imágenes de principales
        $this->addFolderToZip($zip, $modulePath . '/views/img/principales', 'imagenes/principales');

        // Añadir imágenes de partes
        $this->addFolderToZip($zip, $modulePath . '/views/img/partes', 'imagenes/partes');

        // Añadir imágenes de productos (DESDE PRESTASHOP, NO DEL MÓDULO)
        // Recorrer los productos y copiar sus imágenes reales
        if ($results) {
            $productos_exportados = [];
            foreach ($results as $row) {
                if ($row['id_product'] && !in_array($row['id_product'], $productos_exportados)) {
                    $productos_exportados[] = $row['id_product'];
                    $images = Image::getImages($this->context->language->id, $row['id_product']);

                    if (!empty($images) && isset($images[0])) {
                        $image_id = $images[0]['id_image'];
                        $image_obj = new Image($image_id);

                        // Buscar el archivo de imagen (puede ser jpg, png, gif)
                        $extensions = ['jpg', 'png', 'gif'];
                        foreach ($extensions as $ext) {
                            $image_path = _PS_PROD_IMG_DIR_ . $image_obj->getExistingImgPath() . '.' . $ext;
                            if (file_exists($image_path)) {
                                $zip->addFile($image_path, 'imagenes/productos/' . $image_id . '.' . $ext);
                                break;
                            }
                        }
                    }
                }
            }
        }

        $zip->close();

        // Descargar ZIP
        if (file_exists($zipPath)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
            header('Content-Length: ' . filesize($zipPath));
            header('Pragma: no-cache');
            header('Expires: 0');
            readfile($zipPath);
            unlink($zipPath); // Eliminar archivo temporal
        }
    }

    private function addFolderToZip($zip, $folderPath, $zipPath)
    {
        if (!is_dir($folderPath)) {
            return;
        }

        $files = scandir($folderPath);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || $file == '.gitkeep') {
                continue;
            }

            $filePath = $folderPath . '/' . $file;
            if (is_file($filePath)) {
                $zip->addFile($filePath, $zipPath . '/' . $file);
            }
        }
    }

    private function deletePrincipal($id_principal)
    {
        $id_principal = (int)$id_principal;

        // Eliminar productos asociados a las partes de este principal
        $partes = Db::getInstance()->executeS(
            'SELECT id_parte FROM `' . _DB_PREFIX_ . 'escandallo_parte` WHERE id_principal = ' . $id_principal
        );

        foreach ($partes as $parte) {
            Db::getInstance()->delete('escandallo_producto_parte', 'id_parte = ' . (int)$parte['id_parte']);
        }

        // Eliminar partes
        Db::getInstance()->delete('escandallo_parte', 'id_principal = ' . $id_principal);

        // Eliminar principal
        if (Db::getInstance()->delete('escandallo_principal', 'id_principal = ' . $id_principal)) {
            return $this->displayConfirmation($this->l('Principal eliminado correctamente'));
        }

        return $this->displayError($this->l('Error al eliminar el principal'));
    }

    private function deleteParte($id_parte)
    {
        $id_parte = (int)$id_parte;

        // Eliminar productos asociados
        Db::getInstance()->delete('escandallo_producto_parte', 'id_parte = ' . $id_parte);

        // Eliminar parte
        if (Db::getInstance()->delete('escandallo_parte', 'id_parte = ' . $id_parte)) {
            return $this->displayConfirmation($this->l('Parte eliminada correctamente'));
        }

        return $this->displayError($this->l('Error al eliminar la parte'));
    }

    private function deleteProductoParte($id_escandallo_producto)
    {
        $id_escandallo_producto = (int)$id_escandallo_producto;

        if (Db::getInstance()->delete('escandallo_producto_parte', 'id_escandallo_producto = ' . $id_escandallo_producto)) {
            return $this->displayConfirmation($this->l('Producto desasociado correctamente'));
        }

        return $this->displayError($this->l('Error al desasociar el producto'));
    }

    private function uploadImage($field_name, $subfolder)
    {
        if (!isset($_FILES[$field_name]) || $_FILES[$field_name]['error'] != UPLOAD_ERR_OK) {
            return null;
        }

        $upload_dir = _PS_MODULE_DIR_ . $this->name . '/views/img/' . $subfolder . '/';
        
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $filename = uniqid() . '_' . $_FILES[$field_name]['name'];
        $filepath = $upload_dir . $filename;

        if (move_uploaded_file($_FILES[$field_name]['tmp_name'], $filepath)) {
            return $filename;
        }

        return null;
    }

private function renderConfigForm()
    {
        $principales = $this->getPrincipales();
        $partes = $this->getAllPartes();
        $productos_partes = $this->getAllProductosPartes();

        // Obtener la URL de la tienda
        $shop_url = Context::getContext()->shop->getBaseURL(true);

        // Precargar nombres en todos los idiomas para los modales de edición
        $principal_langs = [];
        $rows = Db::getInstance()->executeS('SELECT id_principal, id_lang, nombre FROM `' . _DB_PREFIX_ . 'escandallo_principal_lang`');
        if ($rows) {
            foreach ($rows as $r) {
                $principal_langs[$r['id_principal']][$r['id_lang']] = $r['nombre'];
            }
        }

        $parte_langs = [];
        $rows = Db::getInstance()->executeS('SELECT id_parte, id_lang, nombre FROM `' . _DB_PREFIX_ . 'escandallo_parte_lang`');
        if ($rows) {
            foreach ($rows as $r) {
                $parte_langs[$r['id_parte']][$r['id_lang']] = $r['nombre'];
            }
        }

        $this->context->smarty->assign([
            'module_dir' => $this->_path,
            'principales' => $principales,
            'partes' => $partes,
            'productos_partes' => $productos_partes,
            'shop_url' => $shop_url,
            'link' => $this->context->link,
            'languages' => Language::getLanguages(false),
            'default_language' => (int)Configuration::get('PS_LANG_DEFAULT'),
            'img_lang_dir' => _PS_IMG_ . 'l/',
            'principal_langs' => $principal_langs,
            'parte_langs' => $parte_langs
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function getPrincipales()
    {
        $id_lang = (int)$this->context->language->id;
        return Db::getInstance()->executeS(
            'SELECT p.*, pl.nombre
            FROM `' . _DB_PREFIX_ . 'escandallo_principal` p
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal_lang` pl
                ON (p.id_principal = pl.id_principal AND pl.id_lang = ' . $id_lang . ')
            ORDER BY p.position ASC, pl.nombre ASC'
        );
    }

    public function getPartesByPrincipal($id_principal)
    {
        $id_lang = (int)$this->context->language->id;
        return Db::getInstance()->executeS(
            'SELECT p.*, pl.nombre
            FROM `' . _DB_PREFIX_ . 'escandallo_parte` p
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte_lang` pl
                ON (p.id_parte = pl.id_parte AND pl.id_lang = ' . $id_lang . ')
            WHERE p.id_principal = ' . (int)$id_principal . '
            ORDER BY p.position ASC, pl.nombre ASC'
        );
    }

    public function getAllPartes()
    {
        $id_lang = (int)$this->context->language->id;
        return Db::getInstance()->executeS(
            'SELECT p.*, pl.nombre, prl.nombre as nombre_principal
            FROM `' . _DB_PREFIX_ . 'escandallo_parte` p
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte_lang` pl
                ON (p.id_parte = pl.id_parte AND pl.id_lang = ' . $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal` pr ON p.id_principal = pr.id_principal
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal_lang` prl
                ON (pr.id_principal = prl.id_principal AND prl.id_lang = ' . $id_lang . ')
            ORDER BY prl.nombre ASC, pl.nombre ASC'
        );
    }

    public function getProductosByParte($id_parte)
    {
        return Db::getInstance()->executeS(
            'SELECT pp.*, p.reference, p.id_product, pl.name, p.price, sa.quantity
            FROM `' . _DB_PREFIX_ . 'escandallo_producto_parte` pp
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON pp.id_product = p.id_product
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON (p.id_product = sa.id_product AND sa.id_product_attribute = 0)
            WHERE pp.id_parte = ' . (int)$id_parte . '
            ORDER BY pp.numero_imagen ASC'
        );
    }

    public function getAllProductosPartes()
    {
        $id_lang = (int)$this->context->language->id;
        return Db::getInstance()->executeS(
            'SELECT pp.*, p.reference, pl.name, ptl.nombre as nombre_parte, prl.nombre as nombre_principal
            FROM `' . _DB_PREFIX_ . 'escandallo_producto_parte` pp
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON pp.id_product = p.id_product
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte` pt ON pp.id_parte = pt.id_parte
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte_lang` ptl
                ON (pt.id_parte = ptl.id_parte AND ptl.id_lang = ' . $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal` pr ON pt.id_principal = pr.id_principal
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal_lang` prl
                ON (pr.id_principal = prl.id_principal AND prl.id_lang = ' . $id_lang . ')
            ORDER BY prl.nombre ASC, ptl.nombre ASC, pp.numero_imagen ASC'
        );
    }

    public function getPrincipalById($id_principal)
    {
        $id_lang = (int)$this->context->language->id;
        return Db::getInstance()->getRow(
            'SELECT p.*, pl.nombre
            FROM `' . _DB_PREFIX_ . 'escandallo_principal` p
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal_lang` pl
                ON (p.id_principal = pl.id_principal AND pl.id_lang = ' . $id_lang . ')
            WHERE p.id_principal = ' . (int)$id_principal
        );
    }

    public function getParteById($id_parte)
    {
        $id_lang = (int)$this->context->language->id;
        return Db::getInstance()->getRow(
            'SELECT p.*, pl.nombre, prl.nombre as nombre_principal
            FROM `' . _DB_PREFIX_ . 'escandallo_parte` p
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte_lang` pl
                ON (p.id_parte = pl.id_parte AND pl.id_lang = ' . $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal` pr ON p.id_principal = pr.id_principal
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal_lang` prl
                ON (pr.id_principal = prl.id_principal AND prl.id_lang = ' . $id_lang . ')
            WHERE p.id_parte = ' . (int)$id_parte
        );
    }

    public function buscarProductos($query)
    {
        $query = pSQL($query);
        $id_lang = (int)$this->context->language->id;

        return Db::getInstance()->executeS(
            'SELECT DISTINCT pp.*, p.reference, pl.name, ptl.nombre as nombre_parte,
                    prl.nombre as nombre_principal, pr.id_principal, pt.id_parte
            FROM `' . _DB_PREFIX_ . 'escandallo_producto_parte` pp
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON pp.id_product = p.id_product
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte` pt ON pp.id_parte = pt.id_parte
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte_lang` ptl
                ON (pt.id_parte = ptl.id_parte AND ptl.id_lang = ' . $id_lang . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal` pr ON pt.id_principal = pr.id_principal
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal_lang` prl
                ON (pr.id_principal = prl.id_principal AND prl.id_lang = ' . $id_lang . ')
            WHERE p.reference LIKE "%' . $query . '%" OR pl.name LIKE "%' . $query . '%"
            ORDER BY prl.nombre ASC, ptl.nombre ASC'
        );
    }

    /**
     * Limpia la caché de rutas para forzar regeneración
     */
    private function clearRoutingCache()
    {
        try {
            // Usar métodos nativos de PrestaShop para limpiar caché
            if (class_exists('Tools')) {
                // Limpiar caché de Smarty
                Tools::clearSmartyCache();
                Tools::clearXMLCache();

                // Limpiar caché compilada
                Tools::clearCache();
            }

            // Limpiar caché de class_index
            if (file_exists(_PS_CACHE_DIR_ . 'class_index.php')) {
                @unlink(_PS_CACHE_DIR_ . 'class_index.php');
            }

            // Limpiar archivos de caché de routing
            $cacheFiles = [
                _PS_ROOT_DIR_ . '/var/cache/dev/appParameters.php',
                _PS_ROOT_DIR_ . '/var/cache/prod/appParameters.php',
                _PS_ROOT_DIR_ . '/app/cache/dev/appParameters.php',
                _PS_ROOT_DIR_ . '/app/cache/prod/appParameters.php',
            ];

            foreach ($cacheFiles as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }

            // Limpiar directorio de caché de Smarty
            $smartyCacheDirs = [
                _PS_ROOT_DIR_ . '/var/cache/dev/smarty/compile',
                _PS_ROOT_DIR_ . '/var/cache/prod/smarty/compile',
                _PS_ROOT_DIR_ . '/cache/smarty/compile',
            ];

            foreach ($smartyCacheDirs as $dir) {
                if (is_dir($dir)) {
                    $this->deleteDirectory($dir);
                }
            }

            // Forzar regeneración del Dispatcher
            if (file_exists(_PS_CACHE_DIR_ . 'Dispatcher.php')) {
                @unlink(_PS_CACHE_DIR_ . 'Dispatcher.php');
            }

        } catch (Exception $e) {
            // Ignorar errores de limpieza de caché
        }
    }

    /**
     * Elimina recursivamente un directorio
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : @unlink($path);
        }
        @rmdir($dir);
    }
}