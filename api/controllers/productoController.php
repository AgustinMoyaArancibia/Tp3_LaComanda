<?php
require_once './models/producto.php';
require_once './interfaces/iApiUsable.php';
require_once './models/pdf.php';

class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $producto = $parametros['producto'];
        $precio = $parametros['precio'];

        $prod = new Producto();
        $prod->producto = $producto;
        $prod->precio = $precio;
        $prod->crearProducto();

        $payload = json_encode(array("mensaje" => "Producto creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $mesa = Producto::obtenerProducto($id);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        //var_dump($lista);
        $payload = json_encode(array("listaProductos" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $producto = $parametros['producto'];
        $precio = $parametros['precio'];

        $prod = new Producto();
        $prod->producto = $producto;
        $prod->precio = $precio;
        $prod->id = $id;
        $columnas = $prod->modificarProducto();

        if($columnas != false){
          $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
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

        $prod = Producto::obtenerProducto($id);

        if($prod != false){
          Producto::borrarProducto($id);
          $payload = json_encode(array("mensaje" => "Producto borrado con exito"));
        } else{
          $payload = json_encode(array("mensaje" => "No se encontro producto"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CargarCSV($request, $response, $args)
    {

      $lista = Producto::obtenerTodos();

      $retorno = false;
      $archivo = fopen("./archivo/productos.csv", "a");

      if($archivo != false)
      {
        foreach ($lista as $prod) {
          
          $auxProd = array($prod->id, $prod->producto, $prod->precio);
          $comma_separated = implode(",", $auxProd) . "\n";
          fwrite($archivo, $comma_separated);
        }
  
          fclose($archivo);
          $payload = json_encode(array("mensaje" => "Productos cargados en archivo CSV"));

      } else{
          $payload = json_encode(array("mensaje" => "No se pudo cargar el archivo"));
      }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function DescargarCSV($request, $response, $args)
    {


      $archivo = fopen("./archivo/cargarproductos.csv", "r");

      if($archivo != false){

        while(($datos = fgetcsv($archivo)) !== false){

          $prod = new Producto();
          $prod->producto = $datos[0];
          $prod->precio = $datos[1];
          $prod->crearProducto();
        }
  
        fclose($archivo);
        $payload = json_encode(array("mensaje" => "Productos cargados con exito"));
      }else{
        $payload = json_encode(array("mensaje" => "No se pudo cargar el archivo"));
      }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function GenerarPDF($request, $response, $args)
    {
        ob_clean();
        ob_start();
        $lista = Producto::obtenerTodos();
        $pdf = new PDF();
        $pdf->SetTitle("Lista de Productos");
        $pdf->AddPage();
        $pdf->Cell(150,10,'Lista Productos: ', 0, 1);
        foreach($lista as $producto){
          $pdf->Cell(150,10, Producto::toString($producto));
          $pdf->Ln();
        }
        $pdf->Output('F', './archivo/PDFPRODUCTOS.pdf',false);
        ob_end_flush();

        $payload = json_encode(array("message" => "pdf generado"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}