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
            return $response->withJson($usuarios, 200);
        } else {
            return $response->withJson("No se encontro la info solicitada", 404);
        }
        $resultado = null;
        $db = null;
    } catch (PDOException $th) {
        return $response->withJson($th.getMessage(), 500);
    }
});

$app->get('/api/usuario/{parametro}', function (Request $request, Response $response, array $args) {

    $tipoBusqueda->get_Params('tipo');

    switch ($tipoBusqueda) {
        //Busqueda por nombre de usuario
        case 1:
            $usuario = $args['parametro'];
            $sql = "SELECT * FROM usuario WHERE usuario = :usuario";
            try {
                $db = new db();
                $db = $db->conDB();
                $resultado = $db->prepare($sql);
                $resultado->bindParam(':usuario', $usuario);
                $resultado->execute();
                if ($resultado->rowCount() > 0) {
                    $usuarioEncontrado = $resultado->fetchAll(PDO::FETCH_OBJ);
                    return $response->withJson($usuarioEncontrado, 200);
                } else {
                    echo json_encode('No se encontró el usuario solicitado');
                    return $response->withJson('No se encontró el usuario solicitado', 404);
                }
                $resultado = null;
                $db = null;
            } catch (PDOException $th) {
                return $response->withJson($th.getMessage(), 500);
            }
            break;
        //Busqueda por
        case 2:
            $correo = $args['parametro'];
            $sql = "SELECT * FROM usuario WHERE correo = :correo";
            try {
                $db = new db();
                $db = $db->conDB();
                $resultado = $db->prepare($sql);
                $resultado->bindParam(':correo', $correo);
                $resultado->execute();
                if ($resultado->rowCount() > 0) {
                    $usuarioEncontrado = $resultado->fetchAll(PDO::FETCH_OBJ);
                    return $response->withJson($usuarioEncontrado, 200);
                } else {
                    return $response->withJson('No se encontró el usuario solicitado', 404);
                }
                $resultado = null;
                $db = null;
            } catch (PDOException $th) {
                return $response->withJson($th.getMessage(), 500);
            }
            break;
    }
    
});



$app->post('/api/usuario/nuevo', function (Request $request, Response $response) {
    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $password = $request->getParam('password');
    $correo = $request->getParam('correo');
    $usuario = $request->getParam('usuario');
    $telefono = $request->getParam('telefono');
    $isAdmin = $request->getParam('isAdmin');

    $sqlSelectUsuario = "SELECT * FROM usuario WHERE usuario = :usuario";
    $sqlSelectCorreo = "SELECT * FROM usuario WHERE correo = :correo";

    $sqlInsert = "INSERT INTO usuario (nombre, apellido, password, correo, usuario, telefono, isAdmin) VALUES
                    (:nombre, :apellido, :password, :correo, :usuario, :telefono, :isAdmin)";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultadoUsuario = $db->prepare($sqlSelectUsuario);
        $resultadoUsuario->bindParam(':usuario', $usuario);
        $resultadoUsuario->execute();
        
        //Se revisa si el usuario ya está registrado en la BD
        if ($resultadoUsuario->rowCount() > 0) {
            return $response->withJson("El usuario ya está registrado", 400);
        } else {
            $resultadoCorreo = $db->prepare($sqlSelectCorreo);
            $resultadoCorreo->bindParam(':correo', $correo);
            $resultadoCorreo->execute();

            //Se revisa que el correo no este en uso
            if($resultadoCorreo->rowCount() > 0) {
                return $response->withJson("El correo ya está registrado", 400);
            } else {

                //Se termina de preparar la consulta y se ejecuta
                $resultadoInsert = $db->prepare($sqlInsert);
                $resultadoInsert->bindParam(':nombre', $nombre);
                $resultadoInsert->bindParam(':apellido', $apellido);
                $resultadoInsert->bindParam(':password', $password);
                $resultadoInsert->bindParam(':correo', $correo);
                $resultadoInsert->bindParam(':usuario', $usuario);
                $resultadoInsert->bindParam(':telefono', $telefono);
                $resultadoInsert->bindParam(':isAdmin', $isAdmin);
                $resultadoInsert->execute();
                return $response->withJson("Se ha registrado con exito", 201);
            }
        }
        $resultadoUsuario = null;
        $resultadoInsert = null;
        $db = null;
    } catch (PDOException $th) {
        return $response->withJson($th.getMessage(), 500);
    }
});
