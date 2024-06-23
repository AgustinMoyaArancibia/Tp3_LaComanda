<?php
require_once './models/Pedido.php';

class EmpleadoController extends Pedido
{
  public static function ListarPedidos($request, $response, $args){

        $perfil = $args['perfil'];
        $empleado = Logger::PerfilEmpleado($request);

        if($empleado == $perfil){

          $lista = Pedido::listarPendientes($perfil);
          $payload = json_encode(array("listaPendientes" => $lista));

        }else{
          $payload = json_encode(array("Error" => "No tiene permisos para ver este listado"));
        }

        $response->getBody()->write($payload);
        return $response
      ->withHeader('Content-Type', 'application/json');
    }

  public static function PedidoEnPreparacion($request, $response, $args){

    $parametros = $request->getParsedBody();

    $id = $parametros['id'];
    $perfil = $parametros['perfil'];
    $tiempoEstimado = $parametros['tiempoEstimado'];

    $empleado = Logger::PerfilEmpleado($request);
    //var_dump($empleado);
   //var_dump($perfil);

    if($empleado == $perfil){

      $row = Pedido::modificarEnPreparacion($id, $tiempoEstimado);
      if($row>0){
        $payload = json_encode(array("Message" => "Pedido modificado con exito"));
      }else{
        json_encode(array("Error" => "No se pudo modificar pedido"));
      }

    }else{
      $payload = json_encode(array("Error" => "No tiene permisos para ver este listado"));
    }

    $response->getBody()->write($payload);
    return $response
  ->withHeader('Content-Type', 'application/json');
}

public static function EntregarPedido($request, $response, $args){

  $parametros = $request->getParsedBody();

  $id = $parametros['id'];

  $row = Pedido::modificarPedidoEntregado($id);
  if($row>0){
    $payload = json_encode(array("Message" => "Pedido modificado con exito"));
  }else{
    json_encode(array("Error" => "No se pudo modificar pedido"));
  }

  $response->getBody()->write($payload);
  return $response
->withHeader('Content-Type', 'application/json');
}

public static function CobrarCuenta($request, $response, $args){

  $parametros = $request->getParsedBody();

  $idMesa = $parametros['idMesa'];

  $row = Pedido::modificarPedidoCobrado($idMesa);
  if($row>0){
    $payload = json_encode(array("Message" => "Mesa cobrada con exito"));
  }else{
    json_encode(array("Error" => "No se pudo cobrar mesa"));
  }

  $response->getBody()->write($payload);
  return $response
->withHeader('Content-Type', 'application/json');
}

public static function PedidoListo($request, $response, $args){

  $parametros = $request->getParsedBody();

  $id = $parametros['id'];
  $perfil = $parametros['perfil'];

  $empleado = Logger::PerfilEmpleado($request);

  if($empleado == $perfil){

    $row = Pedido::modificarPedidoListo($id);
    if($row>0){
      $payload = json_encode(array("Message" => "Pedido modificado con exito"));
    }else{
      json_encode(array("Error" => "No se pudo modificar pedido"));
    }

  }else{
    $payload = json_encode(array("Error" => "No tiene permisos para ver este listado"));
  }

  $response->getBody()->write($payload);
  return $response
->withHeader('Content-Type', 'application/json');
}

}