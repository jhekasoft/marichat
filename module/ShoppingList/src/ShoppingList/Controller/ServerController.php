<?php

namespace ShoppingList\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use ShoppingList\Server\ShoppingList;

class ServerController extends AbstractActionController
{
    public function startAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }
        
        $config = $this->getServiceLocator()->get('Config');
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new ShoppingList()
                )
            ),
            $config['web_sockets']['port']
        );
        
        echo 'Running...' . PHP_EOL;

        $server->run();
    }

}
