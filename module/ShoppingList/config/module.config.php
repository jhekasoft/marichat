<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'ShoppingList\Controller\ShoppingList' => 'ShoppingList\Controller\ShoppingListController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'shopping-list' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/shopping-list[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ShoppingList\Controller\ShoppingList',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'shopping-list' => __DIR__ . '/../view',
        ),
    ),
);
