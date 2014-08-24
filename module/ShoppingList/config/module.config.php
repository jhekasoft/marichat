<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'ShoppingList\Controller\ShoppingList' => 'ShoppingList\Controller\ShoppingListController',
            'ShoppingList\Controller\Server' => 'ShoppingList\Controller\ServerController',
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
    'console' => array(
        'router' => array(
            'routes' => array(
                'shopping-list-server-start' => array(
                    'options' => array(
                        'route'    => 'shopping-list-server start',
                        'defaults' => array(
                            'controller' => 'ShoppingList\Controller\Server',
                            'action'     => 'start'
                        )
                    )
                )
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'shopping-list' => __DIR__ . '/../view',
        ),
    ),
    'web_sockets' => array(
        'port' => 9000,
    ),
);
