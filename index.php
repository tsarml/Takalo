<?php
session_start();

require __DIR__ . '/app/vendor/autoload.php';  

require __DIR__ . '/app/controllers/ObjectController.php';
require __DIR__ . '/app/routes.php';

Flight::start();