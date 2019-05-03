<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/api/pregunta', function(Request $request, Response $response, array $args){
    $sqlSelectPregunta = "SELECT * FROM pregunta";
    try {
        $db = new db();
        $db = $db->conDB();
        $resultadoPreguntas = $db->query($sqlSelectPregunta);
        $preguntasWithRespuestas = array();
        $resultadoPreguntas->execute();

        if($resultadoPreguntas->rowCount() > 0){
          while ($row = $resultadoPreguntas->fetch(PDO::FETCH_ASSOC)) {
            $preguntasWithRespuestas['Preguntas'][] = $row;
          }
          echo json_encode($preguntasWithRespuestas);
        }else{
            echo json_encode('No existen preguntas en la base de datos');
        }
        $resultadoPreguntas = null;
        $db = null;
    } catch (PDOException $th) {
        echo '{"error": {"text":' .$th->getMessage(). '}';
        return $response->withJson($errorEnServidor, 500);
    }
});

$app->post('/api/pregunta/nueva', function(Requeste $request, Response $reponse, array $args) {

    $idPregunta = $request->getParam('idPregunta');
    $pregunta = $request->getParam('pregunta');
    $respuestaUno = $request->getParam('respuesta1');
    $respuestaDos = $request->getParam('respuesta2');
    $respuestaTres = $request->getParam('respuesta3');
    $respuestaCuatro = $request->getParam('respuesta4');

    $sqlSelectPregunta = "SELECT pregunta FROM pregunta WHERE pregunta = :pregunta";
    $sqlInsertPregunta = "INSERT INTO pregunta (idPregunta ,pregunta, respuestaFalsaUno, respuestaFalsa2, respuestaFalsa3, 
    RespustaCorrecta) VALUES (:idPregunta ,:pregunta, :respuestaUno, :respuestaDos, :respuestaTres, :respuestaCuatro)";

  try {

    $db = new db();
    $db = $db->conDB();
    $resultado = $db->prepare($sqlSelectPregunta);
    $resultado->bindParam(':pregunta', $pregunta);
    $resultado->execute();

    //Se revisa si existe una pregunta igual
    if ($resultado->rowCount() > 0) {
        return $response->withJson('Ya existe una pregunta igual', 404);
    } else {
        //Insertar pregunta;
        $resultadoInsert = $db->prepare($sqlInsertPregunta);
                  
        $resultadoInsert->bindParam(':idPregunta', $idPregunta);
        $resultadoInsert->bindParam(':pregunta', $pregunta);
        $resultadoInsert->bindParam(':respuestaUno', $respuestaUno);
        $resultadoInsert->bindParam(':respuestaDos', $respuestaDos);
        $resultadoInsert->bindParam(':respuestaTres', $respuestaTres);
        $resultadoInsert->bindParam(':respuestaCuatro', $respuestaCuatro);

        $resultadoInsert->execute();
        return $response->withStatus(201);

    }
  } catch (PDOException $th) {
      $errorEnServidor = 'Ocurrio un error al acceder al Servidor';
      return $response->withJson($errorEnServidor, 500);
  } finally {
      $resultado = null;
      $resultadoInsert = null;
      $db = null;
  }
 
});
?>