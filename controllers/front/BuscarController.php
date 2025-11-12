<?php
/**
 * Controlador para buscar productos por referencia o nombre
 */

class EscandalloBuscarModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $query = Tools::getValue('q', '');
        $resultados = [];

        if (!empty($query) && strlen($query) >= 2) {
            $escandallo = Module::getInstanceByName('escandallo');
            $resultados = $escandallo->buscarProductos($query);

            // Procesar resultados
            foreach ($resultados as &$resultado) {
                // Imagen del producto
                $id_image = Product::getCover($resultado['id_product']);
                if ($id_image) {
                    $image = new Image($id_image['id_image']);
                    $resultado['imagen_url'] = $this->context->link->getImageLink(
                        $resultado['reference'],
                        $image->id,
                        'small_default'
                    );
                } else {
                    $resultado['imagen_url'] = $this->context->link->getMediaLink(
                        _MODULE_DIR_ . 'escandallo/views/img/no-image.jpg'
                    );
                }

                // URL de la parte donde se encuentra
                $resultado['parte_url'] = $this->context->link->getModuleLink(
                    'escandallo', 
                    'productos', 
                    ['id_parte' => $resultado['id_parte']]
                );

                // URL del principal
                $resultado['principal_url'] = $this->context->link->getModuleLink(
                    'escandallo', 
                    'partes', 
                    ['id_principal' => $resultado['id_principal']]
                );
            }
        }

        $this->context->smarty->assign([
            'query' => $query,
            'resultados' => $resultados,
            'module_dir' => Module::getInstanceByName('escandallo')->getPathUri(),
            'index_url' => $this->context->link->getModuleLink('escandallo', 'index'),
            'search_url' => $this->context->link->getModuleLink('escandallo', 'buscar')
        ]);

        $this->setTemplate('module:escandallo/views/templates/front/buscar.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->l('Escandallo'),
            'url' => $this->context->link->getModuleLink('escandallo', 'index')
        ];

        $breadcrumb['links'][] = [
            'title' => $this->l('B�squeda'),
            'url' => ''
        ];

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/escandallo.css');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/escandallo.js');
    }
}