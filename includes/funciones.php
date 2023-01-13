<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function sanitizar($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Funcion que revisa que el usuario este autenticado
function isAuth() : void {
    if(!isset($_SESSION["login"]))
        header("Location: /");
}

// Funcion que devuelve verdadero si se lee otra cita
function esUltimo(string $actual, string $proximo) : bool {
    if ($actual != $proximo)
        return true;
    return false;
}

// Funcion que verifica que sea Admin
function isAdmin(): void {
    if(!isset($_SESSION["admin"])){
        header("Location: /");
    }
}