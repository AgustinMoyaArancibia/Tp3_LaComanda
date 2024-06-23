<?php
require_once './models/Mesa.php';
require_once './interfaces/IApiUsable.php';

class MesaController extends Mesa implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = $parametros['idMesa'];
        $estado = $parametros['estado'];

        $usr = new Mesa();
        $usr->idMesa = $idMesa;
        $usr->estado = $estado;
        $usr->crearMesa();

        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['idMesa'];
        $mesa = Mesa::obtenerMesa($id);
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = $parametros['idMesa'];
        $estado = $parametros['estado'];
        $id = $parametros['id'];

        if($estado != 'cerrada'){
          $mesa = new Mesa();
          $mesa->idMesa = $idMesa;
          $mesa->estado = $estado;
          $mesa->id = $id;
          $columnas = $mesa->modificarMesa();
  
          if($columnas != false){
            $payload = json_encode(array("mensaje" => "Mesa modificada con exito"));
          }else{
            $payload = json_encode(array("mensaje" => "No se pudo modificar"));
          }

        }else{
          $payload = json_encode(array("mensaje" => "No tiene permisos para realizar este cambio"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function CerrarMesa($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = $parametros['idMesa'];
        $estado = $parametros['estado'];
        $id = $parametros['id'];

        $mesa = new Mesa();
        $mesa->idMesa = $idMesa;
        $mesa->estado = $estado;
        $mesa->id = $id;
        $columnas = $mesa->modificarMesaCerrada();

        if($columnas != false){
          $payload = json_encode(array("mensaje" => "Mesa cerrada con exito"));
        }else{
          $payload = json_encode(array("mensaje" => "No se pudo modificar"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $idMesa = $args['id'];

        $mesa = Mesa::obtenerMesa($idMesa);

        if($mesa != false){
          Mesa::borrarMesa($idMesa);
          $payload = json_encode(array("mensaje" => "Mesa borrada con exito"));
        } else{
          $payload = json_encode(array("mensaje" => "No se encontro mesa"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function GenerarPDF($request, $response, $args)
    {
        ob_clean();
        ob_start();
        $lista = Mesa::obtenerTodos();
        $pdf = new PDF();
        $pdf->SetTitle("Lista de Mesas");
        $pdf->AddPage();
        $pdf->Cell(150,10,'Lista Mesas: ', 0, 1);
        foreach($lista as $Mesa){
          $pdf->Cell(150,10, Mesa::toString($Mesa));
          $pdf->Ln();
        }
        $pdf->Output('F', './archivo/PDFMESAS.pdf',false);
        ob_end_flush();

        $payload = json_encode(array("message" => "pdf generado"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


}