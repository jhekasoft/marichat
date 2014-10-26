<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Chat\Controller\Chat' => 'Chat\Controller\ChatController',
            'Chat\Controller\Server' => 'Chat\Controller\ServerController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'chat' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/chat[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Chat\Controller\Chat',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'chat-server-start' => array(
                    'options' => array(
                        'route'    => 'chat-server start',
                        'defaults' => array(
                            'controller' => 'Chat\Controller\Server',
                            'action'     => 'start'
                        )
                    )
                )
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'chat' => __DIR__ . '/../view',
        ),
    ),
    'web_sockets' => array(
        'port' => 9010,
    ),
);
