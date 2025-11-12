<?php
/**
 * Controlador para mostrar los productos de una parte/diagrama específico
 */

class EscandalloProductosModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $id_parte = (int)Tools::getValue('id_parte');

        if ($id_parte <= 0) {
            Tools::redirect($this->context->link->getModuleLink('escandallo', 'index'));
        }

        $escandallo = Module::getInstanceByName('escandallo');
        $parte = $escandallo->getParteById($id_parte);

        if (!$parte) {
            Tools::redirect($this->context->link->getModuleLink('escandallo', 'index'));
        }

        $productos = $escandallo->getProductosByParte($id_parte);

        // Procesar datos de productos
        foreach ($productos as &$producto) {
            // Imagen del producto
            $id_image = Product::getCover($producto['id_product']);
            if ($id_image) {
                $image = new Image($id_image['id_image']);
                $producto['imagen_url'] = $this->context->link->getImageLink(
                    $producto['reference'],
                    $image->id,
                    'home_default'
                );
            } else {
                $producto['imagen_url'] = $this->context->link->getMediaLink(
                    _MODULE_DIR_ . 'escandallo/views/img/no-image.jpg'
                );
            }

            // Precio formateado
            $product_obj = new Product($producto['id_product'], false, $this->context->language->id);
            $producto['precio_formateado'] = Tools::displayPrice($product_obj->getPrice(true));
            
            // Stock
            $producto['tiene_stock'] = $producto['quantity'] > 0;
            
            // URL del producto
            $producto['product_url'] = $this->context->link->getProductLink($producto['id_product']);
            
            // Puede comprar
            $producto['puede_comprar'] = $producto['tiene_stock'] && $product_obj->price > 0;
        }

        // Imagen del diagrama
        if (!empty($parte['imagen'])) {
            $parte['imagen_url'] = $this->context->link->getMediaLink(
                _MODULE_DIR_ . 'escandallo/views/img/partes/' . $parte['imagen']
            );
        } else {
            $parte['imagen_url'] = null;
        }

        $this->context->smarty->assign([
            'parte' => $parte,
            'productos' => $productos,
            'module_dir' => $escandallo->getPathUri(),
            'index_url' => $this->context->link->getModuleLink('escandallo', 'index'),
            'partes_url' => $this->context->link->getModuleLink('escandallo', 'partes', ['id_principal' => $parte['id_principal']]),
            'search_url' => $this->context->link->getModuleLink('escandallo', 'buscar'),
            'cart_url' => $this->context->link->getPageLink('cart', true, null, ['action' => 'add'])
        ]);

        $this->setTemplate('module:escandallo/views/templates/front/productos.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $id_parte = (int)Tools::getValue('id_parte');
        $escandallo = Module::getInstanceByName('escandallo');
        $parte = $escandallo->getParteById($id_parte);

        $breadcrumb['links'][] = [
            'title' => $this->l('Escandallo'),
            'url' => $this->context->link->getModuleLink('escandallo', 'index')
        ];

        if ($parte) {
            $breadcrumb['links'][] = [
                'title' => $parte['nombre_principal'],
                'url' => $this->context->link->getModuleLink('escandallo', 'partes', ['id_principal' => $parte['id_principal']])
            ];

            $breadcrumb['links'][] = [
                'title' => $parte['nombre'],
                'url' => ''
            ];
        }

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/escandallo.css');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/escandallo.js');
    }
}