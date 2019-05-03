<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../src/config/db.php';

$app = new \Slim\App;
    
require '../src/rutas/preguntas.php';
require '../src/rutas/usuario.php';
//require '../src/rutas/emision.php';

$app->run();

?>