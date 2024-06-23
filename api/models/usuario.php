<?php

class Usuario
{
    public $id;
    public $usuario;
    public $clave;
    public $perfil;
    public $estado;
    public $fechaBaja;

    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, perfil, estado) VALUES (:usuario, :clave, :perfil, :estado)");
        $estado = 'disponible';
        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, perfil, estado FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerUsuario($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, perfil, estado FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function loginUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, perfil, estado FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public function modificarUsuario()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, perfil = :perfil, estado = :estado WHERE id = :id");

        $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);

        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $claveHash, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function borrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET estado = :estado, fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime();
        $fechaBaja = $fecha->format('Y-m-d H:i:s');
        $estado = 'dado de baja';
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $fechaBaja);
        $consulta->execute();
    }

    public function Equals($user){

        $retorno = -1;

        if($this->usuario == $user->usuario){
            $retorno = 0;
            
            if(password_verify($this->clave, $user->clave)){
                $retorno = 1;
            }
        }

        return $retorno;
    }

    public function registroLogin()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO registro (idUsuario, usuario, fecha) VALUES (:idUsuario, :usuario, :fecha)");
        
        $fecha = new DateTime();
        $fechaLogin = $fecha->format('Y-m-d H:i:s');
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':idUsuario', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $fechaLogin, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function toString($usuario){


        return 'ID:'.$usuario->id.' | USUARIO: '.$usuario->usuario.' | PERFIL: '.$usuario->perfil;
    }
}