<?php

//Conectar con la BBDD
function conectar()
{
    return new \PDO('pgsql:host=localhost,dbname=tienda', 'tienda', 'tienda');
}

function hh($x)
{
    return htmlspecialchars($x ?? '', ENT_QUOTES | ENT_SUBSTITUTE);
}

function dinero($s)
{
    return number_format($s, 2, ',', ' ') . ' €';
}

function obtener_get($par)
{
    return obtener_parametro($par, $_GET);
}

function obtener_post($par)
{
    return obtener_parametro($par, $_POST);
}

function obtener_parametro($par, $array)
{
    return isset($array[$par]) ? trim($array[$par]) : null;
}

function volver()
{
    header('Location: /index.php');
}

//Serializa el carrito.
function carrito()
{
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = serialize(new \App\Generico\Carrito());
    }

    return $_SESSION['carrito'];
}

function carrito_vacio()
{
    $carrito = unserialize(carrito());

    return $carrito->vacio();
}

function volver_admin()
{
    header("Location: /admin/");
}

function volver_categorias()
{
    header("Location: /admin/categorias.php");
}

function redirigir_login()
{
    header('Location: /login.php');
}

function volver_a($location)
{
    header("Location: " . $location);
}
