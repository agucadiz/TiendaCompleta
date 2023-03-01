<?php
//Eliminar linea del carrito.
session_start();

require '../vendor/autoload.php';

$carrito = unserialize(carrito());

$id = obtener_get('id');

$carrito->eliminar($id);

$_SESSION['carrito'] = serialize($carrito);

volver();
