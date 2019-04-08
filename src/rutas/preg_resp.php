<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/api/preguntas', function(Request $request, Response $response){
    $sql = "SELECT * FROM usuario";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultado = $db->query($sql);
        if($resultado->rowCount() > 0){
            $usuarios = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($usuarios);
        }else{
            echo json_encode('No existen usuarios en la BBDD');
        }
        $resultado = null;
        $db = null;
    } catch (PDOException $th) {
        echo '{"error": {"text":' .$th->getMessage(). '}';
    }
});