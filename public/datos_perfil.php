<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/output.css" rel="stylesheet">
    <title>Perfil de usuario</title>
</head>

<body>
    <?php
    require '../vendor/autoload.php';
    require '../src/_menu.php';
    require '../src/_alerts.php';


    if (!($usuario = \App\Tablas\Usuario::esta_logueado())) {
        return redirigir_login();
    }

    $pdo = conectar();

    $id = \App\Tablas\Usuario::logueado()->id;

    $sent = $pdo->query("SELECT * FROM usuarios WHERE  id = $id");

    //Mantiene los valores antiguos.
    foreach ($sent as $fila) :
        $usuario = $fila['usuario'];
        $nombre = $fila['nombre'];
        $apellidos = $fila['apellidos'];
        $email = $fila['email'];
        $telefono = $fila['telefono'];
    endforeach
    ?>

    <div class="container mx-auto">
        <form action="insertar_perfil.php" method="POST" class="mt-5 mr-96 ml-96">
            <div class="mb-6">
                <label for="usuario" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Usuario
                </label>
                <input type="text" name="usuario" id="usuario" value="<?= $usuario ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600  dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div class="mb-6">
                <label for="nombre" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Nombre
                </label>
                <input type="text" name="nombre" id="nombre" value="<?= $nombre ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600  dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div class="mb-6">
                <label for="apellidos" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Apellidos
                </label>
                <input type="text" name="apellidos" id="apellidos" value="<?= $apellidos ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600  dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div class="mb-6">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Email
                </label>
                <input type="text" name="email" id="email" value="<?= $email ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600  dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div class="mb-6">
                <label for="telefono" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                    Teléfono
                </label>
                <input type="text" name="telefono" id="telefono" value="<?= $telefono ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600  dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Enviar
            </button>
        </form>
    </div>
    <script src="/js/flowbite/flowbite.js"></script>
</body>

</html>