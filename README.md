Marichat
========

Installation
------------
```sh
php composer.phar install
```

```sh
php app/console assets:install web
```

```sh
php app/console assetic:dump
```

Dump MySQL database from dump.sql.

Copy app/config/parameters.yml.dist to the app/config/parameters.yml file
and set parameters

For WebSocket server you need to install `python3` and this libraries:
`asyncio`, `websockets` and `pyyaml`.
You can user `pip3` or `easy_install` utilities for installiation.

Running WebSocket server:

```sh
python3 -u websocket_server/marichat_websocket_server.py
```
