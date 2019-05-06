<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->post('api/usuarioService/{name}', function(Request $request, Response $response, array $args){
    $name = $args['name'];
    $name = $request->getParam('name');

    $mensaje = "Hola " . $name;

    echo json_encode(array('mensaje' => $mensaje));
});
