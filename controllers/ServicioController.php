<?php

namespace Controllers;

use Models\Servicio;
use MVC\Router;

class ServicioController {

    public static function index(Router $router){
        session_start();
        isAdmin();

        $servicio = Servicio::all();
        
        $router->mostrar("servicios/index", [
            "nombre" => $_SESSION["nombre"],
            "servicios" => $servicio
        ]);
    }

    public static function crear(Router $router){
        session_start();
        isAdmin();

        $servicio = new Servicio;
        $alertas = [];
        
        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(empty($alertas)){
                $servicio->guardar();
                header("Location: /servicios");
            }
        }

        $router->mostrar("servicios/crear", [
            "nombre" => $_SESSION["nombre"],
            "servicio" => $servicio,
            "alertas" => $alertas
        ]);
    }

    public static function actualizar(Router $router){
        session_start();
        isAdmin();

        if(!is_numeric($_GET["id"])) 
            return;            

        $servicio = Servicio::buscarID($_GET["id"]);
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(empty($alertas)){
                $servicio->guardar();
                header("Location: /servicios");
            }
        }

        $router->mostrar("servicios/actualizar", [
            "nombre" => $_SESSION["nombre"],
            "servicio" => $servicio,
            "alertas" => $alertas
        ]);
    }

    public static function eliminar(){
        session_start();
        isAdmin();

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            if(!is_numeric($_POST["id"])) 
                return;

            $servicio = Servicio::buscarID($_POST["id"]);
            $servicio->eliminar();
            header("Location: /servicios");            
        }
    }
}