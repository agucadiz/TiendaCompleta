<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/output.css" rel="stylesheet">
    <title>Insertar categoría</title>
</head>

<body>

    <?php
    require '../../vendor/autoload.php';

    $pdo = conectar();

    $categoria = obtener_post('categoria');

    if (isset($categoria) && $categoria != '') {
        $pdo = conectar();

        $sent = $pdo->prepare("INSERT INTO categorias (categoria) VALUES (:categoria)");

        $sent->execute([':categoria' => $categoria]);

        $_SESSION['exito'] = "La categoria se ha añadido correctamente.";

        return header("Location: /admin/categorias.php");
    }

    ?>

    <div class="container mx-auto">
        <?php require '../../src/_menu.php' ?>

        <form class="mt-6 mr-96 ml-96" action="" method="POST">
            <div class="mb-6">
                <label for="categoria" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                    Categoría
                </label>
                <input type="text" name="categoria" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
            </div>

            <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                Insertar
            </button>
        </form>
    </div>

    <script src="../js/flowbite/flowbite.js"></script>

</body>

</html>