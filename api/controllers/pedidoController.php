<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/Producto.php';

class PedidoController extends Pedido implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = $parametros['idMesa'];
        $idProducto = $parametros['idProducto'];
        $cantidad = $parametros['cantidad'];
        $perfil = $parametros['perfil'];
        $cliente = $parametros['cliente'];

        $pedido = new Pedido();
        $pedido->idMesa = $idMesa;
        $pedido->idProducto = $idProducto;
        $pedido->cantidad = $cantidad;
        $pedido->perfil = $perfil;
        $pedido->cliente = $cliente;
        $pedido->rutaFoto = 'sin foto';
        

        $archivos = $request->getUploadedFiles();
        if($archivos['foto']->getError() === UPLOAD_ERR_OK){
          $destino="./fotos/";
          //var_dump($archivos);
          //var_dump($archivos['foto']);
          $nombreAnterior=$archivos['foto']->getClientFilename();
          //var_dump($nombreAnterior);
          $extension= explode(".", $nombreAnterior);
          $extension=array_reverse($extension);
          $destino=$destino.$pedido->idMesa.".".$extension[0];
          //var_dump($destino);
          $archivos['foto']->moveTo($destino);
          $pedido->rutaFoto = $destino;
        }

        $producto = Producto::obtenerProducto($idProducto);
        $pedido->monto = $producto->precio * $cantidad;
        $id = $pedido->crearPedido();

        $payload = json_encode(array("mensaje" => "Pedido creado con exito", "id" => $id));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $pedido = Pedido::obtenerPedido($id);
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodosPorEstado($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $estado = $parametros['estado'];

        //var_dump($parametros);

        $lista = Pedido::obtenerTodosPorEstado($estado);
        $payload = json_encode(array("listaPedidos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = $parametros['idMesa'];
        $idProducto = $parametros['idProducto'];
        $cantidad = $parametros['cantidad'];
        $perfil = $parametros['perfil'];
        $cliente = $parametros['cliente'];
        $id = $args['id'];
        $estado = $parametros['estado'];

        $pedido = new Pedido();
        $pedido->idMesa = $idMesa;
        $pedido->idProducto = $idProducto;
        $pedido->cantidad = $cantidad;
        $pedido->perfil = $perfil;
        $pedido->cliente = $cliente;
        $pedido->estado = $estado;
        $pedido->id = $id;
        $producto = Producto::obtenerProducto($idProducto);
        $pedido->monto = $producto->precio * $cantidad;
        $columnas = $pedido->modificarPedido();

        if($columnas != false){
          $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
        }else{
          $payload = json_encode(array("mensaje" => "No se pudo modificar"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];

        $pedido = Pedido::obtenerPedido($id);

        if($pedido != false){
          Pedido::borrarPedido($id);
          $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
        } else{
          $payload = json_encode(array("mensaje" => "No se encontro pedido"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function GenerarPDF($request, $response, $args)
    {
        ob_clean();
        ob_start();
        $lista = Pedido::obtenerTodos();
        $pdf = new PDF();
        $pdf->SetTitle("Lista de Pedidos");
        $pdf->AddPage();
        $pdf->Cell(150,10,'Lista Pedidos: ', 0, 1);
        foreach($lista as $pedido){
          $pdf->Cell(150,10, Pedido::toString($pedido));
          $pdf->Ln();
        }
        $pdf->Output('F', './archivo/PDFPEDIDOS.pdf',false);
        ob_end_flush();

        $payload = json_encode(array("message" => "pdf generado"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function VerificarHoraEntrega($request, $response, $args)
    {
        $id = $args['id'];
        $pedido = Pedido::obtenerTiempoEstimado($id);
     
        $payload = json_encode($pedido);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function MasUsada($request, $response, $args)
    {
        $pedido = Pedido::obtenerMesaMasUsada();
        $payload = json_encode(array($pedido));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}