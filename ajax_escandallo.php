<?php
/**
 * AJAX handler para el módulo Escandallo
 * Obtiene nombres en todos los idiomas para los modales de edición
 */

require_once(dirname(__FILE__) . '/../../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../../init.php');

// Verificar que el usuario está autenticado como empleado
$context = Context::getContext();
if (!$context->employee || !$context->employee->id) {
    header('Content-Type: application/json');
    die(json_encode(['error' => 'Unauthorized']));
}

header('Content-Type: application/json');

$action = Tools::getValue('action');

switch ($action) {
    case 'getPrincipalNames':
        $id_principal = (int)Tools::getValue('id_principal');

        if ($id_principal <= 0) {
            echo json_encode(['error' => 'Invalid ID']);
            break;
        }

        $names = Db::getInstance()->executeS(
            'SELECT id_lang, nombre
             FROM `' . _DB_PREFIX_ . 'escandallo_principal_lang`
             WHERE id_principal = ' . $id_principal
        );

        $result = [];
        if ($names) {
            foreach ($names as $name) {
                $result[$name['id_lang']] = $name['nombre'];
            }
        }

        echo json_encode(['names' => $result]);
        break;

    case 'getParteNames':
        $id_parte = (int)Tools::getValue('id_parte');

        if ($id_parte <= 0) {
            echo json_encode(['error' => 'Invalid ID']);
            break;
        }

        $names = Db::getInstance()->executeS(
            'SELECT id_lang, nombre
             FROM `' . _DB_PREFIX_ . 'escandallo_parte_lang`
             WHERE id_parte = ' . $id_parte
        );

        $result = [];
        if ($names) {
            foreach ($names as $name) {
                $result[$name['id_lang']] = $name['nombre'];
            }
        }

        echo json_encode(['names' => $result]);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
}
exit;
