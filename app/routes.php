<?php

Flight::route('GET /objects', ['ObjectController', 'index']);

Flight::route('GET /objects/add',    ['ObjectController', 'addForm']);
Flight::route('POST /objects/add',   ['ObjectController', 'addSave']);

Flight::route('GET /objects/edit/@id',  ['ObjectController', 'editForm']);
Flight::route('POST /objects/edit/@id', ['ObjectController', 'editSave']);

Flight::route('GET /objects/delete/@id', ['ObjectController', 'delete']);

Flight::route('POST /exchanges/propose', ['ObjectController', 'propose']);

Flight::route('POST /exchanges/accept',  ['ObjectController', 'accept']);