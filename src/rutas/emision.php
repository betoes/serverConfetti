<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/emision', function (Request $request, Response $response) {
    $sql = "SELECT fecha, hora FROM emision ORDER BY idEmision DESC LIMIT 1";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultado = $db->query($sql);
        if ($resultado->rowCount() > 0) {
            $emisiones = $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($emisiones);
        } else {
            echo json_encode('No existen usuarios en la BBDD');
        }
        $resultado = null;
        $db = null;
    } catch (PDOException $th) {
        echo '{"error": {"text":' . $th->getMessage() . '}';
    }
});

$app->post('/api/emision/nuevo', function (Request $request, Response $response) {
    $idEmision = $request->getParam('idEmision');
    $fecha = $request->getParam('fecha');
    $hora = $request->getParam('hora');

    $sqlInsert = "INSERT INTO emision (idEmision, fecha, hora) VALUES
                    (:idEmision, :fecha, :hora)";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultado = $db->prepare($sqlInsert);
        $resultado->bindParam(':idEmision', $idEmision);
        $resultado->bindParam(':fecha', $fecha);
        $resultado->bindParam(':hora', $hora);
        $resultado->execute();
        echo json_encode('Nueva emisión añadida');
        $resultado = null;
        $db = null;
    } catch (PDOException $th) {
        echo '{"error": {"text":' . $th->getMessage() . '}';
    }
});

