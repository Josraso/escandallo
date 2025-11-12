<?php
/**
 * Controlador para mostrar las partes/diagramas de un producto principal
 */

class EscandalloPartesModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $id_principal = (int)Tools::getValue('id_principal');

        if ($id_principal <= 0) {
            Tools::redirect($this->context->link->getModuleLink('escandallo', 'index'));
        }

        $escandallo = Module::getInstanceByName('escandallo');
        $principal = $escandallo->getPrincipalById($id_principal);

        if (!$principal) {
            Tools::redirect($this->context->link->getModuleLink('escandallo', 'index'));
        }

        $partes = $escandallo->getPartesByPrincipal($id_principal);

        // Procesar imágenes de las partes
        foreach ($partes as &$parte) {
            if (!empty($parte['imagen'])) {
                $parte['imagen_url'] = $this->context->link->getMediaLink(
                    _MODULE_DIR_ . 'escandallo/views/img/partes/' . $parte['imagen']
                );
            } else {
                $parte['imagen_url'] = $this->context->link->getMediaLink(
                    _MODULE_DIR_ . 'escandallo/views/img/no-image.jpg'
                );
            }
        }

        $this->context->smarty->assign([
            'principal' => $principal,
            'partes' => $partes,
            'module_dir' => $escandallo->getPathUri(),
            'index_url' => $this->context->link->getModuleLink('escandallo', 'index'),
            'search_url' => $this->context->link->getModuleLink('escandallo', 'buscar')
        ]);

        $this->setTemplate('module:escandallo/views/templates/front/partes.tpl');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $id_principal = (int)Tools::getValue('id_principal');
        $escandallo = Module::getInstanceByName('escandallo');
        $principal = $escandallo->getPrincipalById($id_principal);

        $breadcrumb['links'][] = [
            'title' => $this->l('Escandallo'),
            'url' => $this->context->link->getModuleLink('escandallo', 'index')
        ];

        if ($principal) {
            $breadcrumb['links'][] = [
                'title' => $principal['nombre'],
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