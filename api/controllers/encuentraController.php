<?php
require_once './models/Encuesta.php';

class EncuestaController extends Encuesta
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idPedido = $parametros['idPedido'];
        $puntajeMesa = $parametros['puntajeMesa'];
        $puntajeRestaurant = $parametros['puntajeRestaurant'];
        $puntajeMozo = $parametros['puntajeMozo'];
        $puntajeCocinero = $parametros['puntajeCocinero'];
        $comentarios = $parametros['comentarios'];
    
        $encuesta = new Encuesta();
        $encuesta->idPedido = $idPedido;
        $encuesta->puntajeMesa = $puntajeMesa;
        $encuesta->puntajeRestaurant = $puntajeRestaurant;
        $encuesta->puntajeMozo = $puntajeMozo;
        $encuesta->puntajeCocinero = $puntajeCocinero;
        $encuesta->comentarios = $comentarios;
        $encuesta->calcularPromedio();
        $encuesta->crearEncuesta();
    
        $payload = json_encode(array("mensaje" => "Encuesta cargada con exito"));
    
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Encuesta::obtenerTodos();
        $payload = json_encode(array("listaEncuestas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ListarMejoresComentarios($request, $response, $args)
    {
        $lista = Encuesta::traerMejoresComentarios();
        $payload = json_encode(array("mejoresComentarios" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }



}