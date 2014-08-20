<?php

namespace ShoppingList;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use ShoppingList\Model\ShoppingListTable;
use ShoppingList\Model\ShoppingList;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'ShoppingList\Model\ShoppingListTable' => function($sm) {
                    $tableGateway = $sm->get('ShoppingListTableGateway');
                    $table = new ShoppingListTable($tableGateway);
                    return $table;
                },
                'ShoppingListTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ShoppingList());
                    return new TableGateway('shopping_list', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}