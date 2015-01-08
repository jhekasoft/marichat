#!/usr/bin/env python

__author__ = "Evgeniy Efremov aka jhekasoft"
__copyright__ = "Copyright 2014, jhekasoft"
__license__ = "GPL"
__version__ = "1.0.1"
__email__ = "jheka@mail.ru"

import asyncio
import websockets
import time
import os
import yaml

@asyncio.coroutine
def handler(websocket, path):
    global clients
    clients.append(websocket)
    print("%s New connection" % time.ctime())

    while True:
        # Recieving message
        message = yield from websocket.recv()
        if message is None:
            break
        print("%s < %s" % (time.ctime(), message))

        # Sending to the all clients except himself
        # We need to copy list because we will remove elements
        for client in clients[:]:
            if client == websocket:
                continue
            if not client.open:
                clients.remove(client)
                continue
            yield from client.send(message)
        print("%s Sended to the all clients except himself" % time.ctime())

    print("%s Connection closed" % time.ctime())

# Reading Symfony params config
try:
    f = open(os.path.dirname(os.path.realpath(__file__))+'/../app/config/parameters.yml')
    config = yaml.safe_load(f)
    f.close()
    port = config['parameters']['marichat_websocket_port']
except Exception as e:
    print('%s Exception while reading configuration: %s' % (time.ctime(), e));
    port = 9000

clients = [] # all connected websockets
start_server = websockets.serve(handler, '', port)
print("%s Marichat WebSocket server started at the port: %s" % (time.ctime(), port))

asyncio.get_event_loop().run_until_complete(start_server)
asyncio.get_event_loop().run_forever()
