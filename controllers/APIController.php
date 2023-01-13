<?php

namespace Controllers;

use Models\Cita;
use Models\CitaServicio;
use Models\Servicio;

class APIController{

    public static function index(){
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function guardar(){
        
        // Almacena la cita y devuelve el ID
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        $idCita = $resultado["id"];

        // Almacena la Cita y el Servicio
        $idServicios = explode(",", $_POST["servicios"]);

        foreach($idServicios as $idServicio){
            $args = [
                "citaId" => $idCita,
                "servicioId" => $idServicio
            ];
            $citaServicio = new CitaServicio($args);
            $citaServicio->guardar();
        }

        // Retornamos una respuesta
        $respuesta = [
            "resultado" => $resultado
        ];
        echo json_encode($respuesta);
    }

    public static function eliminar(){
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            $id = $_POST["id"];
            $cita = Cita::buscarID($id);
            $cita->eliminar();
            header("location:" . $_SERVER["HTTP_REFERER"]);
        }
    }
}