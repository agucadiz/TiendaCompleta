<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/output.css" rel="stylesheet">
    <title>Comprar</title>
</head>

<body>
    <?php require '../vendor/autoload.php';

    if (!\App\Tablas\Usuario::esta_logueado()) {
        return redirigir_login();
    }

    $carrito = unserialize(carrito());

    if (obtener_post('_testigo') !== null) { //_testigo para saber que se ha pulsado el botón "Realizar pedido".
        $pdo = conectar();

        //Arreglo del fallo de comprar varios articulos diferentes.
        $ids = implode(', ', $carrito->getIds());
        $where = "WHERE id IN ($ids)";

        //1.2.D. Impedir la creación de factura si al hacerlo se quedara algún artículo con existencias negativas.
        $sent = $pdo->query("SELECT *
                                 FROM articulos
                                 $where");
        foreach ($sent->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            //Si el stock es menor que la cantidad que se va a comprar, error.
            if ($fila['stock'] < $carrito->getLinea($fila['id'])->getCantidad()) {
                $_SESSION['error'] = 'No hay existencias suficientes para crear la factura.';
                return volver();
            }
        }

        // Crear factura si la compra ha sido exitosa.
        $usuario = \App\Tablas\Usuario::logueado();
        $usuario_id = $usuario->id;
        $pdo->beginTransaction();
        $sent = $pdo->prepare('INSERT INTO facturas (usuario_id)
                               VALUES (:usuario_id)
                               RETURNING id');  //Retorna el id de la facturas que se ha insertado.
        $sent->execute([':usuario_id' => $usuario_id]);
        $factura_id = $sent->fetchColumn();
        $lineas = $carrito->getLineas();
        $values = [];
        $execute = [':f' => $factura_id];
        $i = 1;

        //Nose que hace este foreach.
        foreach ($lineas as $id => $linea) { //sin $id parece que funciona tb.
            // articulos_facturas (articulo_id, factura_id, cantidad)
            $values[] = "(:a$i, :f, :c$i)";
            $execute[":a$i"] = $id;
            $execute[":c$i"] = $linea->getCantidad();
            $i++;
        }

        //1.2.D. Reducir las existencias del artículo al crear una factura.
        $values = implode(', ', $values); //Duda.
        $sent = $pdo->prepare("INSERT INTO articulos_facturas (articulo_id, factura_id, cantidad)
                               VALUES $values");
        $sent->execute($execute); //Duda
        foreach ($lineas as $id => $linea) {
            $cantidad = $linea->getCantidad();
            $sent = $pdo->prepare('UPDATE articulos
                                      SET stock = stock - :cantidad
                                    WHERE id = :id');
            $sent->execute([':id' => $id, ':cantidad' => $cantidad]);
        }
        $pdo->commit();
        $_SESSION['exito'] = 'La factura se ha creado correctamente.';
        unset($_SESSION['carrito']);
        return volver();
    }
    ?>

    <div class="container mx-auto">
        <?php require '../src/_menu.php' ?>
        <div class="overflow-y-auto py-4 px-3 bg-gray-50 rounded dark:bg-gray-800">
            <table class="mx-auto text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <th scope="col" class="py-3 px-6">Código</th>
                    <th scope="col" class="py-3 px-6">Descripción</th>
                    <th scope="col" class="py-3 px-6">Cantidad</th>
                    <th scope="col" class="py-3 px-6">Precio</th>
                    <th scope="col" class="py-3 px-6">IVA</th>
                    <th scope="col" class="py-3 px-6">Importe</th>
                    <th scope="col" class="py-3 px-6 text-center"></th>
                </thead>
                <tbody>
                    <?php $total = 0 ?>
                    <?php foreach ($carrito->getLineas() as $id => $linea) : ?>
                        <?php
                        $articulo = $linea->getArticulo();
                        $articulo_id = $articulo->getId();
                        $codigo = $articulo->getCodigo();
                        $descripcion = $articulo->getDescripcion();
                        $cantidad = $linea->getCantidad();
                        $precio = $articulo->getPrecio();
                        $descuento = ($precio * $articulo->getDescuento()) / 100;
                        $precio_final = $precio - $descuento;
                        $total_linea = $precio_final * $cantidad;
                        $iva = $total_linea * 0.21;
                        $importe = $total_linea + $iva;
                        $total += $importe;
                        ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="py-4 px-6"><?= $codigo ?></td>
                            <td class="py-4 px-6"><?= $descripcion ?></td>
                            <td class="py-4 px-6 text-center"><?= $cantidad ?></td>
                            <td class="py-4 px-6 text-center">
                                <?= dinero($total_linea) ?>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <?= dinero($iva) ?>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <?= dinero($importe) ?>
                            </td>
                            <!-- 2.3.A. Implementar botones para aumentar o disminuir la cantidad de los articulos a comprar. -->
                            <td class="py-4 px-6 text-center">
                                <a href="/aumentar.php?id=<?= $articulo_id ?>" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                                    +
                                </a>
                                <a href="/disminuir.php?id=<?= $articulo_id ?>" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                                    -
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
                <tfoot>
                    <td colspan="4"></td>
                    <td class="text-center font-semibold">TOTAL:</td>
                    <td class="text-center font-semibold"><?= dinero($total) ?></td>
                </tfoot>
            </table>
            <form action="" method="POST" class="mx-auto flex mt-4">
                <!-- _testigo -->
                <input type="hidden" name="_testigo" value="1">
                <button type="submit" href="" class="mx-auto focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                    Realizar pedido
                </button>
            </form>
        </div>
    </div>
    <script src="/js/flowbite/flowbite.js"></script>
</body>

</html>