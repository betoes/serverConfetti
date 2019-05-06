<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

function enviarSMS($telefono, $pin)
{
    $apiKey = '6ZbxSPSwh0t0HlSR6vkSpg0Dh';

    $messagebird = new MessageBird\Client($apiKey);
    $message = new MessageBird\Objects\Message;
    $message->originator = '+522282729094';
    $message->recipients = ['+52' . $telefono];
    $message->body = 'Proyecto Confetti: José Alberto, Jethran Enrique, Angel Sanchez, Paola Marai: PIN: ' . $pin;
    $response = $messagebird->messages->create($message);
}


$app->get('/api/usuario', function (Request $request, Response $response, array $args) {
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
        return $response->withJson($th . getMessage(), 500);
    }
});

$app->post('/api/usuario/login', function (Request $request, Response $response, array $args) {
    $usuario = $request->getParam('usuario');
    $clave = $request->getParam('clave');

    $sql = "SELECT autenticado FROM usuario WHERE usuario = :usuario AND clave = :clave LIMIT 1";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':usuario', $usuario);
        $resultado->bindParam(':clave', $clave);
        $resultado->execute();
        if ($resultado->rowCount() > 0) {

            $us = $resultado->fetch(PDO::FETCH_ASSOC)['autenticado'];
            return $response->withJson($us, 200);
            
            

        } else {
            return $response->withJson("El usuario o contraseña son incorrectos", 404);
        }

    } catch (PDOException $th) {
        return $response->withJson("Error para recuperar los datos de Inicio de Sesion", 500);
    }

});

$app->post('/api/usuario/auth', function (Request $request, Response $response, array $args) {
    $usuario = $request->getParam('usuario');
    $pin = intval($request->getParam('pin'));
    $autenticar = 1;
    

    $sql = "SELECT autenticado FROM usuario WHERE usuario = :usuario AND pin = :pin";
    $sqlPin = "UPDATE usuario SET autenticado = :auth WHERE usuario = :usuario";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultado = $db->prepare($sql);
        $resultado->bindParam(':usuario', $usuario);
        $resultado->bindValue(':pin', $pin);
        $resultado->execute();
        if ($resultado->rowCount() > 0) {
            
            $actualizar = $db->prepare($sqlPin);
            $actualizar->bindParam(':auth', $autenticar);
            $actualizar->bindParam(':usuario', $usuario);
            $actualizar->execute();
            return $response->withStatus(200);
        } else {
            return $response->withStatus(404);
        }

        $db = null;
        $resultado = null;
    } catch (PDOException $th) {
        $errorEnServidor = 'Ocurrio un error al acceder al Servidor';
    } finally {
        $resultadoUsuario = null;
        $resultadoCorreo = null;
        $resultadoTel = null;
        $resultadoInsert = null;
        $db = null;
    }

    return $response->withJson($errorEnServidor, 500);
});

$app->get('/api/usuario/{parametro}', function (Request $request, Response $response, array $args) {

    $tipoBusqueda = $request->getParam('tipo');

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
                    return $response->withStatus(404);
                }
                $resultado = null;
                $db = null;
            } catch (PDOException $th) {
                return $response->withJson($th . getMessage(), 500);
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
                    return $response->withStatus(404);
                }
                $resultado = null;
                $db = null;
            } catch (PDOException $th) {
                return $response->withJson($th . getMessage(), 500);
            }
            break;
    }

});

$app->post('/api/usuario/nuevo', function (Request $request, Response $response, array $args) {
    /*$random_number = mt_rand(1, 9);
    for ($i = 0; $i < 2; $i++) {
        $random_number .= mt_rand(0, 9);
    }*/
    
    
    $pin = 123;
    $nombre = $request->getParam('nombre');
    $apellido = $request->getParam('apellido');
    $clave = $request->getParam('clave');
    $correo = $request->getParam('correo');
    $usuario = $request->getParam('usuario');
    $telefono = $request->getParam('telefono');

    

    $sqlSelectUsuario = "SELECT usuario FROM usuario WHERE usuario = :usuario";
    $sqlSelectCorreo = "SELECT usuario FROM usuario WHERE correo = :correo";
    $sqlSelectTel = "SELECT usuario FROM usuario WHERE telefono = :telefono";

    

    $sqlInsert = "INSERT INTO usuario (nombre, apellido, clave, correo, usuario, telefono, pin) VALUES
                    (:nombre, :apellido, :clave, :correo, :usuario, :telefono, :pin)";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultadoUsuario = $db->prepare($sqlSelectUsuario);
        $resultadoUsuario->bindParam(':usuario', $usuario);
        $resultadoUsuario->execute();
        //Se revisa si el usuario ya está registrado en la BD
        if ($resultadoUsuario->rowCount() > 0) {

            return $response->withJson('El usuario ya está registrado', 404);
        } else {
            $resultadoCorreo = $db->prepare($sqlSelectCorreo);
            $resultadoCorreo->bindParam(':correo', $correo);
            $resultadoCorreo->execute();
            
            //Se revisa que el correo no este en uso
            if ($resultadoCorreo->rowCount() > 0) {
                return $response->withJson('El correo ya está registrado', 404);
            } else {
                $resultadoTel = $db->prepare($sqlSelectTel);
                $resultadoTel->bindParam(':telefono', $telefono);
                $resultadoTel->execute();
                if ($resultadoTel->rowCount() > 0) {
                    return $response->withJson('El telefono ya está registrado', 404);
                } else {
                    //Se termina de preparar la consulta y se ejecuta
                    
                    //enviarSMS($telefono, $pin);
                    $resultadoInsert = $db->prepare($sqlInsert);
                    $resultadoInsert->bindParam(':nombre', $nombre);
                    $resultadoInsert->bindParam(':apellido', $apellido);
                    $resultadoInsert->bindParam(':clave', $clave);
                    $resultadoInsert->bindParam(':correo', $correo);
                    $resultadoInsert->bindParam(':usuario', $usuario);
                    $resultadoInsert->bindParam(':telefono', $telefono);
                    $resultadoInsert->bindParam(':pin', $pin);
                    
                    $resultadoInsert->execute();
                    return $response->withStatus(201);

                }

            }

        }
        
    } catch (PDOException $th) {
        $errorEnServidor = 'Ocurrio un error al acceder al Servidor';
    } finally {
        $resultadoUsuario = null;
        $resultadoCorreo = null;
        $resultadoTel = null;
        $resultadoInsert = null;
        $db = null;
    }

    return $response->withJson($errorEnServidor, 500);
});
