<?php
/**
 * Controlador para mostrar los productos principales del escandallo
 */

class EscandalloIndexModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $escandallo = Module::getInstanceByName('escandallo');
        $principales = $escandallo->getPrincipales();

        // Procesar imágenes
        foreach ($principales as &$principal) {
            if (!empty($principal['imagen'])) {
                $principal['imagen_url'] = $this->context->link->getMediaLink(
                    _MODULE_DIR_ . 'escandallo/views/img/principales/' . $principal['imagen']
                );
            } else {
                $principal['imagen_url'] = $this->context->link->getMediaLink(
                    _MODULE_DIR_ . 'escandallo/views/img/no-image.jpg'
                );
            }
        }

        $this->context->smarty->assign([
            'principales' => $principales,
            'module_dir' => $escandallo->getPathUri(),
            'base_url' => $this->context->link->getModuleLink('escandallo', 'index'),
            'search_url' => $this->context->link->getModuleLink('escandallo', 'buscar')
        ]);

        $this->setTemplate('module:escandallo/views/templates/front/index.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->l('Escandallo'),
            'url' => $this->context->link->getModuleLink('escandallo', 'index')
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