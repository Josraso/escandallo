<?php
/**
 * Módulo Escandallo para PrestaShop 1.7, 8 y 9
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
        $this->version = '1.0.0';
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

        // Tabla de principales
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_principal` (
            `id_principal` int(11) NOT NULL AUTO_INCREMENT,
            `nombre` varchar(255) NOT NULL,
            `imagen` varchar(255) DEFAULT NULL,
            `activo` tinyint(1) DEFAULT 1,
            `position` int(11) DEFAULT 0,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_principal`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        // Tabla de partes/diagramas
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'escandallo_parte` (
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
            'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'escandallo_parte`',
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
            // Ruta principal VA AL FINAL para que no capture las otras
            'module-escandallo-index' => [
                'controller' => 'index',
                'rule' => 'escandallo-piezas',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'escandallo',
                    'controller' => 'index'
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
        $nombre = Tools::getValue('nombre_principal');
        $imagen = $this->uploadImage('imagen_principal', 'principales');

        if (empty($nombre)) {
            return $this->displayError($this->l('El nombre del principal es obligatorio'));
        }

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'escandallo_principal` 
                (`nombre`, `imagen`, `date_add`, `date_upd`) 
                VALUES ("' . pSQL($nombre) . '", "' . pSQL($imagen) . '", NOW(), NOW())';

        if (Db::getInstance()->execute($sql)) {
            return $this->displayConfirmation($this->l('Principal añadido correctamente'));
        }

        return $this->displayError($this->l('Error al añadir el principal'));
    }

    private function processAddParte()
    {
        $id_principal = (int)Tools::getValue('id_principal_parte');
        $nombre = Tools::getValue('nombre_parte');
        $imagen = $this->uploadImage('imagen_parte', 'partes');

        if (empty($nombre) || $id_principal <= 0) {
            return $this->displayError($this->l('Todos los campos son obligatorios'));
        }

        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'escandallo_parte` 
                (`id_principal`, `nombre`, `imagen`, `date_add`, `date_upd`) 
                VALUES (' . $id_principal . ', "' . pSQL($nombre) . '", "' . pSQL($imagen) . '", NOW(), NOW())';

        if (Db::getInstance()->execute($sql)) {
            return $this->displayConfirmation($this->l('Parte a�adida correctamente'));
        }

        return $this->displayError($this->l('Error al añadir la parte'));
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
        $nombre = Tools::getValue('nombre_principal_edit');
        $imagen_actual = Tools::getValue('imagen_actual_principal');

        if (empty($nombre) || $id_principal <= 0) {
            return $this->displayError($this->l('Todos los campos son obligatorios'));
        }

        // Intentar subir nueva imagen si se proporcionó
        $imagen = $this->uploadImage('imagen_principal_edit', 'principales');
        if ($imagen === null) {
            $imagen = $imagen_actual; // Mantener imagen actual si no se subió nueva
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'escandallo_principal`
                SET `nombre` = "' . pSQL($nombre) . '",
                    `imagen` = "' . pSQL($imagen) . '",
                    `date_upd` = NOW()
                WHERE `id_principal` = ' . $id_principal;

        if (Db::getInstance()->execute($sql)) {
            return $this->displayConfirmation($this->l('Principal actualizado correctamente'));
        }

        return $this->displayError($this->l('Error al actualizar el principal'));
    }

    private function processEditParte()
    {
        $id_parte = (int)Tools::getValue('id_parte_edit');
        $id_principal = (int)Tools::getValue('id_principal_parte_edit');
        $nombre = Tools::getValue('nombre_parte_edit');
        $imagen_actual = Tools::getValue('imagen_actual_parte');

        if (empty($nombre) || $id_parte <= 0 || $id_principal <= 0) {
            return $this->displayError($this->l('Todos los campos son obligatorios'));
        }

        // Intentar subir nueva imagen si se proporcionó
        $imagen = $this->uploadImage('imagen_parte_edit', 'partes');
        if ($imagen === null) {
            $imagen = $imagen_actual; // Mantener imagen actual si no se subió nueva
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'escandallo_parte`
                SET `id_principal` = ' . $id_principal . ',
                    `nombre` = "' . pSQL($nombre) . '",
                    `imagen` = "' . pSQL($imagen) . '",
                    `date_upd` = NOW()
                WHERE `id_parte` = ' . $id_parte;

        if (Db::getInstance()->execute($sql)) {
            return $this->displayConfirmation($this->l('Parte actualizada correctamente'));
        }

        return $this->displayError($this->l('Error al actualizar la parte'));
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

        $header = fgetcsv($handle, 0, ',');
        $imported = 0;
        $errors = 0;

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if (count($data) < 14) {
                $errors++;
                continue;
            }

            $id_principal = (int)$data[0];
            $nombre_principal = $data[1];
            $id_parte = (int)$data[2];
            $nombre_parte = $data[3];
            $imagen_parte = $data[4];
            $id_product = (int)$data[5];
            $numero_imagen = (int)$data[6];
            $referencia = $data[7];
            $nombre_producto = $data[8];
            $descripcion = $data[9];
            $precio = (float)$data[10];
            $imagen_producto = $data[11];
            $stock = (int)$data[12];
            $id_category = (int)$data[13];

            try {
                // Crear o actualizar principal
                $principal_exists = Db::getInstance()->getValue(
                    'SELECT id_principal FROM `' . _DB_PREFIX_ . 'escandallo_principal` WHERE id_principal = ' . $id_principal
                );

                if (!$principal_exists) {
                    Db::getInstance()->insert('escandallo_principal', [
                        'id_principal' => $id_principal,
                        'nombre' => pSQL($nombre_principal),
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_upd' => date('Y-m-d H:i:s')
                    ]);
                }

                // Crear o actualizar parte
                $parte_exists = Db::getInstance()->getValue(
                    'SELECT id_parte FROM `' . _DB_PREFIX_ . 'escandallo_parte` WHERE id_parte = ' . $id_parte
                );

                if (!$parte_exists) {
                    Db::getInstance()->insert('escandallo_parte', [
                        'id_parte' => $id_parte,
                        'id_principal' => $id_principal,
                        'nombre' => pSQL($nombre_parte),
                        'imagen' => pSQL($imagen_parte),
                        'date_add' => date('Y-m-d H:i:s'),
                        'date_upd' => date('Y-m-d H:i:s')
                    ]);
                }

                // Crear o actualizar producto
                $product_exists = Db::getInstance()->getValue(
                    'SELECT id_product FROM `' . _DB_PREFIX_ . 'product` WHERE id_product = ' . $id_product
                );

                if (!$product_exists && $id_product > 0) {
                    // Crear producto nuevo
                    $product = new Product();
                    $product->id_product = $id_product;
                    $product->reference = $referencia;
                    $product->name = [$this->context->language->id => $nombre_producto];
                    $product->description = [$this->context->language->id => $descripcion];
                    $product->price = $precio;
                    $product->visibility = 'none';
                    $product->active = 1;
                    $product->id_category_default = $id_category;
                    
                    if ($product->add()) {
                        // A�adir a categor�a
                        $product->addToCategories([$id_category]);
                        
                        // Actualizar stock
                        StockAvailable::setQuantity($product->id, 0, $stock);
                    }
                } elseif ($product_exists) {
                    // Producto existe, solo asociar
                    $product = new Product($id_product);
                    $product->visibility = 'none';
                    $product->save();
                }

                // Asociar producto a parte
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

        $this->context->smarty->assign([
            'module_dir' => $this->_path,
            'principales' => $principales,
            'partes' => $partes,
            'productos_partes' => $productos_partes,
            'shop_url' => $shop_url,
            'link' => $this->context->link
        ]);

        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function getPrincipales()
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'escandallo_principal` ORDER BY position ASC, nombre ASC'
        );
    }

    public function getPartesByPrincipal($id_principal)
    {
        return Db::getInstance()->executeS(
            'SELECT * FROM `' . _DB_PREFIX_ . 'escandallo_parte` 
            WHERE id_principal = ' . (int)$id_principal . ' 
            ORDER BY position ASC, nombre ASC'
        );
    }

    public function getAllPartes()
    {
        return Db::getInstance()->executeS(
            'SELECT p.*, pr.nombre as nombre_principal 
            FROM `' . _DB_PREFIX_ . 'escandallo_parte` p
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal` pr ON p.id_principal = pr.id_principal
            ORDER BY pr.nombre ASC, p.nombre ASC'
        );
    }

    public function getProductosByParte($id_parte)
    {
        return Db::getInstance()->executeS(
            'SELECT pp.*, p.reference, p.id_product, pl.name, p.price, sa.quantity
            FROM `' . _DB_PREFIX_ . 'escandallo_producto_parte` pp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON pp.id_product = p.id_product
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON (p.id_product = sa.id_product AND sa.id_product_attribute = 0)
            WHERE pp.id_parte = ' . (int)$id_parte . '
            ORDER BY pp.numero_imagen ASC'
        );
    }

    public function getAllProductosPartes()
    {
        return Db::getInstance()->executeS(
            'SELECT pp.*, p.reference, pl.name, pt.nombre as nombre_parte, pr.nombre as nombre_principal
            FROM `' . _DB_PREFIX_ . 'escandallo_producto_parte` pp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON pp.id_product = p.id_product
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte` pt ON pp.id_parte = pt.id_parte
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal` pr ON pt.id_principal = pr.id_principal
            ORDER BY pr.nombre ASC, pt.nombre ASC, pp.numero_imagen ASC'
        );
    }

    public function getPrincipalById($id_principal)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'escandallo_principal` WHERE id_principal = ' . (int)$id_principal
        );
    }

    public function getParteById($id_parte)
    {
        return Db::getInstance()->getRow(
            'SELECT p.*, pr.nombre as nombre_principal 
            FROM `' . _DB_PREFIX_ . 'escandallo_parte` p
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal` pr ON p.id_principal = pr.id_principal
            WHERE p.id_parte = ' . (int)$id_parte
        );
    }

    public function buscarProductos($query)
    {
        $query = pSQL($query);
        
        return Db::getInstance()->executeS(
            'SELECT DISTINCT pp.*, p.reference, pl.name, pt.nombre as nombre_parte, 
                    pr.nombre as nombre_principal, pr.id_principal, pt.id_parte
            FROM `' . _DB_PREFIX_ . 'escandallo_producto_parte` pp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON pp.id_product = p.id_product
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . (int)$this->context->language->id . ')
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_parte` pt ON pp.id_parte = pt.id_parte
            LEFT JOIN `' . _DB_PREFIX_ . 'escandallo_principal` pr ON pt.id_principal = pr.id_principal
            WHERE p.reference LIKE "%' . $query . '%" OR pl.name LIKE "%' . $query . '%"
            ORDER BY pr.nombre ASC, pt.nombre ASC'
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