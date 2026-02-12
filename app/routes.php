<?php
// app/routes.php

// Page d'accueil = liste des objets (solution la plus simple pour commencer)
Flight::route('GET /', ['ObjectController', 'listAll']);

// Les autres routes
Flight::route('GET /objects',    ['ObjectController', 'listAll']);
Flight::route('GET /objects/my', ['ObjectController', 'listMyObjects']);

// Plus tard :
// Flight::route('GET /login',  ...);
// Flight::route('POST /exchange/propose', ...);