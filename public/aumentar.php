<?php
session_start();

use App\Tablas\Articulo;
use ValueError;

require '../vendor/autoload.php';

try {
    $id = obtener_get('id');

    if ($id === null) {
        return volver();
    }

    $articulo = Articulo::obtener($id);

    if ($articulo === null) {
        return volver();
    }


    $carrito = unserialize(carrito());
    $carrito->insertar($id);
    $_SESSION['carrito'] = serialize($carrito);

    header('Location: comprar.php');

} catch (ValueError $e) {
    // TODO: mostrar mensaje de error en un Alert
}

