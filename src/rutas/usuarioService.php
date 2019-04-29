<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

/*function enviarSMS($telefono, $pin)
{
    $apiKey = '6ZbxSPSwh0t0HlSR6vkSpg0Dh';

    $messagebird = new MessageBird\Client($apiKey);
    $message = new MessageBird\Objects\Message;
    $message->originator = '+522282729094';
    $message->recipients = ['+52' . $telefono];
    $message->body = 'Proyecto Confetti: José Alberto, Jethran Enrique, Angel Sanchez, Paola Marai: PIN: ' . $pin;
    $response = $messagebird->messages->create($message);
}*/

$app->get('/api/usuario', function (Request $request, Response $response, array $args) {
    $sql = "SELECT usuario, apellido, clave, autenticado, isAdmin, correo, pin, telefono, idUsuario FROM usuario";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $usuarios = $resultado->fetchAll(PDO::FETCH_OBJ);
            return $response->withJson($usuarios, 200);
        } else {
            return $response->withJson("No se encontro la info solicitada", 404);
        }
        $resultado = null;
        $db = null;
    } catch (PDOException $th) {
        return $response->withJson($th . getMessage(), 500);
    }
});

