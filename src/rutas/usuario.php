<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/usuario', function (Request $request, Response $response) {
    $sql = "SELECT * FROM usuario";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $usuarios = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($usuarios);
        } else {
            echo json_encode('No existen usuarios en la BBDD');
        }
        $resultado = null;
        $db = null;
    } catch (PDOException $th) {
        echo '{"error": {"text":' . $th->getMessage() . '}';
    }
});

$app->post('/api/usuario/nuevo', function (Request $request, Response $response) {
    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $password = $request->getParam('password');
    $usuario = $request->getParam('usuario');
    $telefono = $request->getParam('telefono');
    $isAdmin = $request->getParam('isAdmin');

    $sqlSelect = "SELECT * FROM usuario WHERE usuario = :usuario";

    $sqlInsert = "INSERT INTO usuario (nombre, apellido, password, usuario, telefono, isAdmin) VALUES
                    (:nombre, :apellido, :password, :usuario, :telefono, :isAdmin)";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultado = $db->prepare($sqlSelect);
        $resultado->bindParam(':usuario', $usuario);
        $resultado->execute();

        if ($resultado->rowCount() > 0) {
            echo json_encode("El usuario ya existe");
        } else {
            $resultado = $db->prepare($sqlInsert);
            $resultado->bindParam(':nombre', $nombre);
            $resultado->bindParam(':apellido', $apellido);
            $resultado->bindParam(':password', $password);
            $resultado->bindParam(':usuario', $usuario);
            $resultado->bindParam(':telefono', $telefono);
            $resultado->bindParam(':isAdmin', $isAdmin);
            $resultado->execute();
            echo json_encode('Usuario añadido');
        }

        $resultado = null;
        $db = null;
    } catch (PDOException $th) {
        echo '{"error": {"text":' . $th->getMessage() . '}';
    }
});
