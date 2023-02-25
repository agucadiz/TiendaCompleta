<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/output.css" rel="stylesheet">
    <title>Portal</title>
</head>

<body>
    <?php
    require '../vendor/autoload.php';

    $carrito = unserialize(carrito());

    //Conectar a base de datos y consulta:
    $pdo = conectar();
    $sent = $pdo->query("SELECT a.*, categoria, c.id AS cat_id
                           FROM articulos a 
                           JOIN categorias c 
                             ON a.categoria_id = c.id 
                       ORDER BY codigo");


    //Lógica buscador:
    $nombre = obtener_get('nombre');
    $precio_min = obtener_get('precio_min');
    $precio_max = obtener_get('precio_max');
    $categoria = obtener_get('categoria');
    $where = [];
    $execute = [];

    if (isset($nombre) && $nombre != '') {
        $where[] = 'lower(descripcion) LIKE lower(:nombre)';
        $execute[':nombre'] = "%$nombre%";
    }
    if (isset($precio_min) && $precio_min != '') {
        $where[] = 'precio >= :precio_min';
        $execute[':precio_min'] = $precio_min;
    }
    if (isset($precio_max) && $precio_max != '') {
        $where[] = 'precio <= :precio_max ';
        $execute[':precio_max'] = $precio_max;
    }
    if (isset($categoria) && $categoria != '') {
        $where[] = 'categoria_id = :categoria';
        $execute[':categoria'] = $categoria;
    }

    $where = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
    $sent = $pdo->prepare("SELECT a.*, categoria, c.id AS cat_id
                             FROM articulos a 
                             JOIN categorias c ON a.categoria_id = c.id 
                             $where
                             ORDER BY codigo");
    $sent->execute($execute);
    ?>

    <div class="container mx-4">

        <?php require '../src/_menu.php' ?>
        <?php require '../src/_alerts.php' ?>

        <!-- Buscador -->
        <div>
            <form action="" method="get">
                <fieldset>
                    <legend> <b>Búsqueda</b> </legend>
                    <div class="mb-3 font-normal text-gray-700 dark:text-gray-400">
                        <label class="block mb-2 text-sm font-medium w-1/4 pr-4">
                            Nombre del artículo:
                            <input type="text" name="nombre" value="<?= $nombre ?>" class="border text-sm rounded-lg w-full p-1.5">
                        </label>

                        <label class="block mb-2 text-sm font-medium w-1/4 pr-4">
                            Precio mínimo:
                            <input type="text" name="precio_min" value="<?= $precio_min ?>" class="border text-sm rounded-lg w-full p-1.5">
                        </label>

                        <label class="block mb-2 text-sm font-medium w-1/4 pr-4">
                            Precio máximo:
                            <input type="text" name="precio_max" value="<?= $precio_max ?>" class="border text-sm rounded-lg w-full p-1.5">
                        </label>

                        <label class="block mb-2 text-sm font-medium w-1/4 pr-4">
                            Categoría:
                            <select name="categoria" class="block mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-gray-300 border rounded-lg w-full p-1.5">

                                <?php $category = $pdo->query("SELECT * FROM categorias"); ?>

                                <option value=""> Elegir categoría </option>
                                <?php foreach ($category as $categories) : ?>
                                    <option value=<?= hh($categories['id']) ?> <?= ($categories['id'] == $categoria) ? 'selected' : '' ?>>
                                        <?= hh($categories['categoria']) ?>
                                    </option>
                                <?php endforeach ?>

                            </select>
                        </label>
                    </div>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 mr-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        Buscar
                    </button>
                </fieldset>
            </form>
        </div>

        <br>

        <!-- Tarjetas articulos -->
        <div class="container mx-auto">
            <div class="flex">
                <main class="flex-1 grid grid-cols-3 gap-4 justify-center justify-items-center">
                    <?php foreach ($sent as $fila) : ?>
                        <div class="p-6 max-w-xs min-w-full bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
                            <!-- Mostrar descripción de cada artículo -->
                            <a href="#">
                                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><?= hh($fila['descripcion']) ?></h5>
                            </a>
                            <!-- Mostrar precio de cada artículo -->
                            <p class="mb-3 font-medium text-gray-700 dark:text-gray-400"><?= hh(dinero($fila['precio'])) ?></p>
                            <!-- Mostrar categoría de cada artículo -->
                            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Categoría: <?= hh($fila['categoria']) ?></p>
                            <!-- Mostrar existencias de cada artículo -->
                            <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Existencias: <?= hh($fila['stock']) ?></p>
                            <!-- Botón carrito o sin existencias -->
                            <?php if ($fila['stock'] > 0) : ?>
                                <a href="/insertar_en_carrito.php?id=<?= $fila['id'] ?>&categoria=<?= hh($categoria) ?>" class="inline-flex items-center py-2 px-3.5 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                    Añadir al carrito
                                    <svg aria-hidden="true" class="ml-3 -mr-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                            <?php else : ?>
                                <a class="inline-flex items-center py-2 px-3.5 text-sm font-medium text-center text-white bg-gray-700 rounded-lg hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                    Sin existencias
                                </a>
                            <?php endif ?>
                        </div>
                    <?php endforeach ?>
                </main>

                <!-- Carrito de la compra -->
                <?php if (!$carrito->vacio()) : ?>
                    <aside class="flex flex-col items-center w-1/4" aria-label="Sidebar">
                        <div class="overflow-y-auto py-4 px-3 bg-gray-50 rounded dark:bg-gray-800">
                            <table class="mx-auto text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <th scope="col" class="py-3 px-6">Descripción</th>
                                    <th scope="col" class="py-3 px-6">Cantidad</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($carrito->getLineas() as $id => $linea) : ?>
                                        <?php
                                        $articulo = $linea->getArticulo();
                                        $cantidad = $linea->getCantidad();
                                        ?>
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="py-4 px-6">
                                                <b><?= $articulo->getDescripcion() ?></b>
                                                <br>
                                                <?= $articulo->getCategoria() ?>
                                            </td>
                                            <td class="py-4 px-6 text-center"><?= $cantidad ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        <div>
                            <a href="/vaciar_carrito.php" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                                Vaciar carrito</a>
                            <a href="/comprar.php" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                                Comprar</a>
                        </div>
                    </aside>
                <?php endif ?>
            </div>
        </div>
        <script src="/js/flowbite/flowbite.js"></script>
</body>

</html>