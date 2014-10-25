Shopping-list
=============

Installation
------------

Configure your virtual host to `public` direcroty.

Create the `shopping_list` MySQL-database and dump data from `shopping-list/dump.sql`.

Setup database credential in `shopping-list/config/autoload/local.php.dist`.

Set web-sockets port in file `shopping-list/module/ShoppingList/config/module.config.php`:

return array(
    //...
    'web_sockets' => array(
        'port' => 9000,
    ),
);

Run `php composer.phar update`.

Run web-sockets server in `shopping-list/public/`: `php -q index.php shopping-list-server start`

You may use `supervisor` utility for running web-socket server. Example of config
is placed here `config/external/supervisor/shopping-list.conf`.
