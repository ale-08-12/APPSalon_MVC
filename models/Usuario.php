<?php

namespace Models;

class Usuario extends ActiveRecord{

    // Base de Datos
    protected static $tabla = "usuarios";
    protected static $columnasDB = ["id", "nombre", "apellido", "email", "password", "telefono", "admin", "confirmado", "token"];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []){
        $this->id = $args["id"] ?? null;
        $this->nombre = $args["nombre"] ?? "";
        $this->apellido = $args["apellido"] ?? "";
        $this->email = $args["email"] ?? "";
        $this->password = $args["password"] ?? "";
        $this->telefono = $args["telefono"] ?? "";
        $this->admin = $args["admin"] ?? "0";
        $this->confirmado = $args["confirmado"] ?? "0";
        $this->token = $args["token"] ?? "";
    }

    // Revisa si el usuario ya existe
    public function existeUsuario(){
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1;";

        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas["error"][] = "El Usuario ya Existe";
        }
        return $resultado;
    }

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token = uniqid();
    }

    public function comprobarPasswordYVerificarlo($password) : bool{
        $resultado = password_verify($password, $this->password);

        if(!$resultado || !$this->confirmado){
            self::$alertas["error"][] = "Password Incorrecto o Cuenta no Confirmada";
            return false;
        }else{
            return true;
        }       
    }

    // Mensaje de validacion de Login
    public function validarLogin() : array{
        if(!$this->email){
            self::$alertas["error"][] = "El Email es Obligatorio";
        }

        if(!$this->password){
            self::$alertas["error"][] = "El Password es Obligatorio";
        }
        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas["error"][] = "El Email es Obligatorio";
        }
        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas["error"][] = "El Password es Obligatorio";
        }

        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "El Password Debe Contener al Menos 6 Caracteres";
        }
        return self::$alertas;
    }

    // Mensaje de validacion para un Nuevo Usuario
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas["error"][] = "El Nombre es Obligatorio";
        }

        if(!$this->apellido){
            self::$alertas["error"][] = "El Apellido es Obligatorio";
        }

        if(!$this->telefono){
            self::$alertas["error"][] = "El Telefono es Obligatorio";
        }

        if(!$this->email){
            self::$alertas["error"][] = "El Email es Obligatorio";
        }

        if(!$this->password){
            self::$alertas["error"][] = "El Password es Obligatorio";
        }

        if(strlen($this->password) < 6){
            self::$alertas["error"][] = "El Password Debe Contener al Menos 6 Caracteres";
        }
        return self::$alertas;
    }
}