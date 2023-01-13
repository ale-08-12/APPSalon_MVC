<?php

namespace Models;

class Servicio extends ActiveRecord{
    // Base de Datos
    protected static $tabla = "servicios";
    protected static $columnasDB = ["id", "nombre", "precio"];

    public $id;
    public $nombre;
    public $precio;

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->precio = $args["precio"] ?? null;
    }

    public function validar() : array{
        if(!$this->nombre){
            self::$alertas["error"][] = "El Nombre del Servicio es Obligatorio";
        }

        if(!$this->precio){
            self::$alertas["error"][] = "El Precio del Servicio es Obligatorio";
        }

        if(!is_numeric($this->precio)){
            self::$alertas["error"][] = "El Precio No es Valido";
        }

        return self::$alertas;
    }
}