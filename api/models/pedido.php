<?php

class Pedido
{
    public $id;
    public $idMesa;
    public $idProducto;
    public $cantidad;
    public $cliente;
    public $perfil;
    public $estado;
    public $horaIngreso;
    public $tiempoEstimado;
    public $rutaFoto;
    public $horaEntrega;
    public $monto;

    public function crearPedido()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (idMesa, idProducto, cantidad, cliente, perfil, estado, rutaFoto, monto) VALUES (:idMesa, :idProducto, :cantidad, :cliente, :perfil, :estado, :rutaFoto, :monto)");
        $estado = 'pendiente';
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':rutaFoto', $this->rutaFoto, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_INT);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerTodosPorEstado($estado)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos WHERE estado = :estado");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
    }

    public static function obtenerPedido($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idMesa, idProducto, cantidad, cliente, perfil, estado, horaIngreso, tiempoEstimado FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public function modificarPedido()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET idMesa = :idMesa, idProducto = :idProducto, cantidad = :cantidad, cliente = :cliente, estado = :estado, perfil = :perfil monto = :monto WHERE id = :id");

        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        $consulta->bindValue(':idProducto', $this->idProducto, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':cliente', $this->cliente, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $this->perfil, PDO::PARAM_STR);
        $consulta->bindValue(':monto', $this->monto, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function modificarEnPreparacion($id, $tiempoEstimado)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado, horaIngreso = :horaIngreso, tiempoEstimado = :tiempoEstimado WHERE id = :id");

        $fecha = new DateTime();
        $horaIngreso = $fecha->format('Y-m-d H:i:s');
        $tiempoEstimado = $fecha->modify('+' . $tiempoEstimado . 'minute')->format('Y-m-d H:i:s');
        $estado = 'en preparacion';
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':horaIngreso', $horaIngreso, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoEstimado', $tiempoEstimado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function modificarPedidoListo($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado, horaEntrega = :horaEntrega WHERE id = :id");

        $fecha = new DateTime();
        $horaEntrega = $fecha->format('Y-m-d H:i:s');
        $estado = 'listo para servir';
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':horaEntrega', $horaEntrega, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function modificarPedidoEntregado($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE id = :id");

        $estado = 'entregado';
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    public static function modificarPedidoCobrado($idMesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET estado = :estado WHERE idMesa = :idMesa AND estado = :auxEstado");

        $estado = 'cobrado';
        $auxEstado = 'entregado';
        $consulta->bindValue(':auxEstado', $auxEstado, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }


    public static function borrarPedido($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function listarPendientes($perfil){
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT pedidos.id, pedidos.idMesa, pedidos.idProducto, productos.producto, pedidos.cantidad, pedidos.cliente, pedidos.perfil, pedidos.estado FROM pedidos, productos WHERE perfil = :perfil AND estado = :estado AND pedidos.idProducto = productos.id");
        $estado = 'pendiente';
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':perfil', $perfil, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function toString($pedido){


        return 'ID:'.$pedido->id.' | MESA: '.$pedido->idMesa.' | CLIENTE: '.$pedido->cliente.' | ESTADO: '.$pedido->estado.' | MONTO: '.$pedido->monto;
    }

    public static function obtenerTiempoEstimado($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idMesa, tiempoEstimado AS horaEstimada FROM pedidos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerMesaMasUsada()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idMesa, COUNT(*) AS cantidadUsos FROM pedidos GROUP BY idMesa HAVING COUNT(1)>1");
        $consulta->execute();

        return $consulta->fetchObject();
    }

}