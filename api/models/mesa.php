<?php

class Mesa
{
    public $id;
    public $idMesa;
    public $estado;

    //creo mesa y le agreso su estado
    public function crearMesa()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (idMesa, estado) VALUES (:idMesa, :estado)");
        $estado = 'abierta';
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    //obtengo todas mesas
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idMesa, estado FROM mesas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
    }


    //obtengo mesa en particular
    public static function obtenerMesa($idMesa)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, idMesa, estado FROM mesas WHERE idMesa = :idMesa");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }


    //modifico la mesa
    public function modificarMesa()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado, idMesa = :idMesa WHERE id = :id");

        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_STR);
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

    //cierro la mesa
    public function modificarMesaCerrada()
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado WHERE idMesa = :idMesa");
    
        $estado = 'cerrada';
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':idMesa', $this->idMesa, PDO::PARAM_INT);
        
        $consulta->execute();
        return $consulta->rowCount();
    }


    //borro mesa por id
    public static function borrarMesa($idMesa)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM mesas WHERE idMesa = :idMesa");
        $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_STR);
        $consulta->execute();
    }

    //imprimo la mesa
    public static function toString($mesa)
    {

        return 'ID:' . $mesa->id . ' | MESA: ' . $mesa->idMesa . ' | ESTADO: ' . $mesa->estado;
    }

    public static function obtenerMesasOrdenadasFactura()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("
            SELECT idMesa, SUM(monto) AS totalFactura
            FROM pedidos
            GROUP BY idMesa
            ORDER BY totalFactura ASC
        ");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_OBJ);
    }

    public static function obtenerFacturacionMesaEntreDosFechas($idMesa, $fechaInicio, $fechaFin)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("
        SELECT idMesa, SUM(monto) AS totalFacturado
        FROM pedidos
        WHERE idMesa = :idMesa AND horaEntrega BETWEEN :fechaInicio AND :fechaFin
        GROUP BY idMesa
    ");
    $consulta->bindValue(':idMesa', $idMesa, PDO::PARAM_INT);
    $consulta->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
    $consulta->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
    $consulta->execute();
    return $consulta->fetch(PDO::FETCH_OBJ);
}
}
