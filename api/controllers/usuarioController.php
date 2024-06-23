<?php
require_once './models/usuario.php';
require_once './interfaces/iApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $perfil = $parametros['perfil'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->perfil = $perfil;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['id'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $perfil = $parametros['perfil'];
        $estado = $parametros['estado'];

        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->perfil = $perfil;
        $usr->estado = $estado;
        $usr->id = $args['id'];
        $columnas = $usr->modificarUsuario();

        if($columnas != false){
          $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        }else{
          $payload = json_encode(array("mensaje" => "No se pudo modificar"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {

        $usuarioId = $args['id'];

        $usuario = Usuario::obtenerUsuario($usuarioId);

        if($usuario != false){
          Usuario::borrarUsuario($usuarioId);
          $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
        } else{
          $payload = json_encode(array("mensaje" => "No se encontro usuario"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ValidarUsuario($request, $response, $args){

      $parametros = $request->getParsedBody();

      var_dump("usuario pasado" ,$parametros);
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $payload = json_encode(array("mensaje" => "Error de autenticacion"));

      $auxUser = Usuario::loginUsuario($usuario);
      var_dump("usuario base de datos" ,$auxUser);
      if($auxUser != false && $auxUser->estado == 'disponible'){

        $validar = password_verify($clave, $auxUser->clave);
        
        var_dump($clave == $auxUser->clave);
        if($clave == $auxUser->clave){

          $datos = array('usuario' => $usuario, 'clave' => $auxUser->clave, 'perfil' => $auxUser->perfil);
          $token = AutentificadorJWT::CrearToken($datos);
          $payload = json_encode(array("jwt" => $token, "response" => "ok", "perfil de usuario" => $auxUser->perfil));

          //registro fecha y hora de login al sistema
          $auxUser->registroLogin();

        }

      };


      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');


    }

    public function GenerarPDF($request, $response, $args)
    {
        ob_clean();
        ob_start();
        $lista = Usuario::obtenerTodos();
        $pdf = new PDF();
        $pdf->SetTitle("Lista de Usuarios");
        $pdf->AddPage();
        $pdf->Cell(150,10,'Lista Usuarios: ', 0, 1);
        foreach($lista as $usuario){
          $pdf->Cell(150,10, Usuario::toString($usuario));
          $pdf->Ln();
        }
        $pdf->Output('F', './archivo/PDFUSUARIOS.pdf',false);
        ob_end_flush();

        $payload = json_encode(array("message" => "pdf generado"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

}