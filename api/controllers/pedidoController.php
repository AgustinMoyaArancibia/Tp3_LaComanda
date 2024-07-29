<?php
require_once './models/pedido.php';
require_once './interfaces/iApiUsable.php';
require_once './models/producto.php';

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
      if ($archivos['foto']->getError() === UPLOAD_ERR_OK) {
          $destino = "./fotos/";
  
          $nombreAnterior = $archivos['foto']->getClientFilename();
          //var_dump($nombreAnterior);
          $extension = pathinfo($nombreAnterior, PATHINFO_EXTENSION);
          $nombreArchivo = pathinfo($nombreAnterior, PATHINFO_FILENAME);
          $destino = $destino . $nombreArchivo . "_" . $cliente . "_" . $pedido->idMesa . "." . $extension;
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

    if ($columnas != false) {
      $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));
    } else {
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

    if ($pedido != false) {
      Pedido::borrarPedido($id);
      $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));
    } else {
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

    $pdf = new FPDF();
    $pdf->SetTitle("Lista de Pedidos");
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(150, 10, 'Lista de Pedidos: ', 0, 1);
    foreach ($lista as $pedido) {
      $pdf->SetFont('Arial', '', 12);
      $pdf->Cell(150, 10, Pedido::toString($pedido));
      $pdf->Ln();
    }
    $pdf->Output('F', './archivo/PDFPEDIDOS.pdf', false);
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

  public function TraerPendientesPerfil($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $perfil = $parametros['perfil'];
    //var_dump($parametros);

    $lista = Pedido::listarPendientes($perfil);
    $payload = json_encode(array("listaPedidos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerEnPreparacionPerfil($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $perfil = $parametros['perfil'];
   

    $lista = Pedido::listarEnPreparacion($perfil);
    $payload = json_encode(array("listaPedidos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }
  public function TraerlistosPerfil($request, $response, $args)
  {
    $parametros = $request->getParsedBody();

    $perfil = $parametros['perfil'];
   

    $lista = Pedido::listarListos($perfil);
    $payload = json_encode(array("listaPedidos" => $lista));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPedidosNoEntregadosATiempo($request, $response, $args)
  {
    $pedidos = Pedido::obtenerPedidosNoEntregadosATiempo();
    $payload = json_encode(array("listaPedidosNoEntregadosATiempo" => $pedidos));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function DescargarLogoEmpresaPDF($request, $response, $args)
  {
    // Limpiar el buffer de salida
    ob_clean();
    ob_start();

    // Crear una instancia de FPDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Logo de la Empresa');
    $pdf->Image('./archivo/logo.jpg', 30, 30, 50); // Cambia la ruta a la ubicación real de tu logo.

    // Generar el PDF y guardarlo en un archivo temporal
    $filePath = './archivo/logo_empresa.pdf';
    $pdf->Output('F', $filePath);

    // Enviar el archivo al cliente para su descarga
    if (file_exists($filePath)) {
      $response->getBody()->write(file_get_contents($filePath));
      return $response
        ->withHeader('Content-Type', 'application/pdf')
        ->withHeader('Content-Disposition', 'attachment; filename="logo_empresa.pdf"');
    } else {
      $payload = json_encode(array("message" => "El archivo no se encontró."));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
  }


  public function ObtenerCantidadOperacionesPorSector($request, $response, $args)
  {
    $resultados = Pedido::cantidadOperacionesPorSector();
    $payload = json_encode($resultados);

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }


  public function GenerarPDFPedidoMesaCliente($request, $response, $args)
  {
      $parametros = $request->getParsedBody();
      $idMesa = $parametros['idMesa'];
      $cliente = $parametros['cliente'];

      // Obtener los pedidos filtrados por idMesa y cliente
      $lista = Pedido::obtenerPedidosPorMesaYCliente($idMesa, $cliente);

      // Limpiar el buffer de salida
      ob_clean();
      ob_start();

      // Crear una instancia de FPDF
      $pdf = new FPDF();
      $pdf->SetTitle("Pedidos de la Mesa " . $idMesa . " del Cliente " . $cliente);
      $pdf->AddPage();
      $pdf->SetFont('Arial', 'B', 12);
      $pdf->Cell(150, 10, 'Pedidos de la Mesa ' . $idMesa . ' del Cliente ' . $cliente . ': ', 0, 1);
      
      foreach ($lista as $pedido) {
          $pdf->SetFont('Arial', '', 12);
          $pdf->Cell(150, 10, Pedido::toString($pedido));
          $pdf->Ln();
      }
      
      // Guardar el PDF en un archivo
      $filePath = './archivo/PedidoMesa' . $idMesa . '_Cliente' . $cliente . '.pdf';
      $pdf->Output('F', $filePath);

      // Enviar el archivo al cliente para su descarga
      if (file_exists($filePath)) {
          $response->getBody()->write(file_get_contents($filePath));
          return $response
              ->withHeader('Content-Type', 'application/pdf')
              ->withHeader('Content-Disposition', 'attachment; filename="PedidoMesa' . $idMesa . '_Cliente' . $cliente . '.pdf"');
      } else {
          $payload = json_encode(array("message" => "El archivo no se encontró."));
          $response->getBody()->write($payload);
          return $response
              ->withHeader('Content-Type', 'application/json');
      }
  }


  public function TraerTodosPorMesa($request, $response, $args)
  {
      $idMesa = $args['idMesa'];
      $resultados = Pedido::where('idMesa', $idMesa);
      $payload = json_encode($resultados);
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
  }

}
