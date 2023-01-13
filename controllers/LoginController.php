<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Models\Usuario;

class LoginController{

    public static function login(Router $router){
        
        $alertas = [];
        $auth = new Usuario;

        if($_SERVER["REQUEST_METHOD"] === "POST"){

            $auth->sincronizar($_POST);
            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                // Comprobamos que exista el Usuario
                $usuario = Usuario::buscar("email", $auth->email);

                if($usuario){
                    // Comprobamos el Password
                    if($usuario->comprobarPasswordYVerificarlo($auth->password)){
                        // Autenticar el Usuario
                        session_start();

                        $_SESSION["id"] = $usuario->id;
                        $_SESSION["nombre"] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION["email"] = $usuario->email;
                        $_SESSION["login"] = true;

                        if($usuario->admin){
                            $_SESSION["admin"] = $usuario->admin;
                            header("Location: /admin");
                        }else{
                            header("Location: /cita");
                        }                        
                    }
                }else{
                    Usuario::setAlerta("error", "Usuario no Registrado");
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->mostrar("auth/login",[
            "alertas" => $alertas,
            "auth" => $auth
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];
        header("Location: /");
    }

    public static function olvide(Router $router){
        
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::buscar("email", $auth->email);

                if($usuario && $usuario->confirmado){
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar email de confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    Usuario::setAlerta("exito", "Revisa tu Email");
                }else{
                    Usuario::setAlerta("error","El Usuario no Existe o No esta Confirmado ");
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->mostrar("auth/olvide-password", [
            "alertas" => $alertas
        ]);
    }

    public static function recuperar(Router $router){
        
        $alertas = [];
        $error = false;
        $token = sanitizar($_GET["token"]);

        // Buscar Usuario por su toquen
        $usuario = Usuario::buscar("token", $token);

        if(empty($usuario) || $usuario->token === ""){
            Usuario::setAlerta("error", "Token No Válido");
            $error = true;
        }

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            // Leer el nuevo Password
            $password = new Usuario($_POST);
            $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = "";
                $resultado = $usuario->guardar();
                
                if($resultado){
                    header("Location: /");
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->mostrar("auth/recuperar-password", [
            "alertas" => $alertas,
            "error" => $error
        ]);
    }

    public static function crear(Router $router){
       
        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER["REQUEST_METHOD"] === "POST"){
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alertas este vacio
            if(empty($alertas)){
                // Verificar que el usuario no este registrado
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                } else{
                    $usuario->hashPassword();
                    $usuario->crearToken();

                    // Enviar email de confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();

                    if($resultado){
                        header("Location: /mensaje");
                    }
                }
            }
        }

        $router->mostrar("auth/crear-cuenta", [
            "usuario" => $usuario,
            "alertas" => $alertas
        ]);
    }

    public static function confirmar(Router $router){

        $alertas = [];

        $token = sanitizar($_GET["token"]);
        $usuario = Usuario::buscar("token", $token);

        if(empty($usuario) || $usuario->token === ""){
            // Error cuando no se encuentra el usuario
            Usuario::setAlerta("error", "Token No Válido");
        }else{
            $usuario->confirmado = "1";
            $usuario->token = "";
            $usuario->guardar();
            Usuario::setAlerta("exito", "Cuenta Comprobada Correctamente");
        }
        $alertas = Usuario::getAlertas();
        
        $router->mostrar("auth/confirmar-cuenta", [
            "alertas" => $alertas
        ]);
    }

    public static function mensaje(Router $router){

        $router->mostrar("auth/mensaje");
    }
}