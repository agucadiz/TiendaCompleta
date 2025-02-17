<?php
// Cuando se pulsa el botón insertar en carrito te dirige aquí.

use App\Tablas\Articulo;

session_start();

require '../vendor/autoload.php';

$categoria = obtener_get('categoria');
$etiqueta = obtener_get('etiqueta');


try {
    $id = obtener_get('id');

    if ($id === null) {
        return volver();
    }

    //Duda.
    $articulo = Articulo::obtener($id);

    if ($articulo === null) {
        return volver();
    }

    //Control de las existencias pero no se llega a utilizar porque realiza se realiza antes en comprar.php y en el index.php
    if ($articulo->getStock() <= 0) {
        $_SESSION['error'] = 'No hay existencias suficientes.';
        return volver();
    }

    //Deserializar el carrito, insertar el id del artículo y volver a serializarlo.
    $carrito = unserialize(carrito());
    $carrito->insertar($id);
    $_SESSION['carrito'] = serialize($carrito);

    //Si se buscó con categorías, se guardad en la url.
    //Si se buscó con etiquetas, se guarda en la url.
    if ($categoria !== null && $categoria !== '') {
        header('Location: index.php?nombre=&precio_min=&precio_max=&categoria=' . $categoria);
    } else if ($etiqueta !== null && $etiqueta !== '') {
        header('Location: index.php?etiqueta=' . $etiqueta);
    } else {
        volver();
    }

} catch (ValueError $e) {
    // TODO: mostrar mensaje de error en un Alert
}

