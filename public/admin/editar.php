<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/output.css" rel="stylesheet">
    <title>Modificar</title>
</head>

<body>

    <?php
    require '../../vendor/autoload.php';

    $pdo = conectar();

    //Recoge los datos del panel de administrador.
    $id = obtener_get('id');
    $codigo = obtener_get('codigo');
    $descripcion = obtener_get('descripcion');
    $precio = obtener_get('precio');
    $stock = obtener_get('stock');
    $categoria_id = obtener_get('categoria_id'); //Capturo el id de categoría.

    //Recoge los nuevos datos del panel de editar.
    $codigo2 = obtener_post('codigo2');
    $descripcion2 = obtener_post('descripcion2');
    $precio2 = obtener_post('precio2');
    $stock2 = obtener_post('stock2');
    $categoria_id2 = obtener_post('categoria_id2');

    if (
        isset($codigo2) && $codigo2 != ''
        && ($descripcion2) && $descripcion2 != ''
        && isset($precio2) && $precio2 != ''
        && isset($categoria_id2) && $categoria_id2 != ''
        && isset($stock2) && $stock2 != ''
    ) {
        \App\Tablas\Articulo::modificar($id, $codigo2, $descripcion2, $precio2, $categoria_id2, $stock2, $pdo);
        $_SESSION['exito'] = "El articulo se ha modificado correctamente.";
        return volver_admin();
    }
    ?>

    <div class="container mx-auto">
        <?php require '../../src/_menu.php' ?>

        <form class="mt-5 mr-96 ml-96" action="" method="POST">
            <div class="mb-6">
                <label for="codigo2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Código
                </label>
                <input type="text" name="codigo2" value="<?= $codigo ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">

                <label for="descripcion2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Descripción
                </label>
                <input type="text" name="descripcion2" value="<?= $descripcion ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">

                <label for="precio2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Precio
                </label>
                <input type="text" name="precio2" value="<?= $precio ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">

                <label for="stock2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Stock
                </label>
                <input type="text" name="stock2" value="<?= $stock ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                <label for="categoria_id2" class="block mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Categoría
                </label>

                <!-- Guarda la categoría del panel de administración -->
                <?php
                $categories = array(
                    "" => "Elegir",
                    "1" => "Informática",
                    "2" => "Alimentación",
                    "3" => "Otros"
                );

                foreach ($categories as $cat_id => $cat_nombre) {
                    if ($cat_id == $categoria_id) {
                        $menu .= '<option value="' . $cat_id . '" selected>' . $cat_nombre . '</option>';
                    } else {
                        $menu .= '<option value="' . $cat_id . '">' . $cat_nombre . '</option>';
                    }
                }
                ?>
                <select name="categoria_id2" class="block mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300" required>
                    <?php
                    print_r($menu);
                    ?>
                </select>
            </div>

            <button type="submit" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 mr-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900">
                Editar
            </button>
        </form>
    </div>

    <script src="../js/flowbite/flowbite.js"></script>

</body>

</html>