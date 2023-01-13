<?php

namespace Controllers;

use MVC\Router;

class CitaController{

    public static function index(Router $router){

        session_start();

        isAuth();

        $router->mostrar("cita/index", [
            "id" => $_SESSION["id"],
            "nombre" => $_SESSION["nombre"]            
        ]);
    }
}