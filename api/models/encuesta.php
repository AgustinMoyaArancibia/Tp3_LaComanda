<?php

class Encuesta
{
    public $id;
    public $idPedido;
    public $puntajeMesa;
    public $puntajeRestaurant;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $promedio;
    public $comentarios;

    public function crearEncuesta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO encuesta (idPedido, puntajeMesa, puntajeRestaurant, puntajeMozo, puntajeCocinero, promedio, comentarios) VALUES (:idPedido, :puntajeMesa, :puntajeRestaurant, :puntajeMozo, :puntajeCocinero, :promedio, :comentarios)");
        $consulta->bindValue(':idPedido', $this->idPedido, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMesa', $this->puntajeMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeRestaurant', $this->puntajeRestaurant, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeMozo', $this->puntajeMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntajeCocinero', $this->puntajeCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':promedio', $this->promedio, PDO::PARAM_INT);
        $consulta->bindValue(':comentarios', $this->comentarios, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }


    //calculo promedio de los puntajes
    public function calcularPromedio(){

        $this->promedio = ($this->puntajeMesa+$this->puntajeRestaurant+$this->puntajeMozo+$this->puntajeCocinero)/4;
    }


    //obtengo todas las encuestas
    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }


    //traigo los mejores comentarios
    public static function traerMejoresComentarios()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta WHERE promedio > 6");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
    }


    //traigo encuenta por id
    public static function obtenerEncuesta($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuesta WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }


}