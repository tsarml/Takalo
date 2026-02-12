<?php
session_start();

require __DIR__ . '/app/vendor/autoload.php';

Flight::set('flight.views.path', __DIR__ . '/app/views');

Flight::register('db', 'PDO', [
    'mysql:host=localhost;dbname=takalo;charset=utf8',
    'root',   // user XAMPP par défaut
    '',       // password XAMPP par défaut (vide)
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
]);

require __DIR__ . '/app/controllers/ObjectController.php';

require __DIR__ . '/app/routes.php';

Flight::start();