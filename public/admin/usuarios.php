<?php session_start() ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/css/output.css" rel="stylesheet">
    <title>Listado de usuarios</title>
</head>

<body>
    <?php
    /* 1.3.B. Mostrar una lista de los usuarios en la URL /admin/usuarios.php, a la que sólo
              podrá acceder el usuario administrador. */

    require '../../vendor/autoload.php';

    //Comprobar si el usuario esta logueado y si es admin.
    if ($usuario = \App\Tablas\Usuario::logueado()) {
        if (!$usuario->es_admin()) {
            $_SESSION['error'] = 'Acceso no autorizado.';
            return volver();
        }
    } else {
        return redirigir_login();
    }

    $pdo = conectar();

    $id = obtener_post('id');

    if (isset($id)) {
        $sent = $pdo->prepare('UPDATE usuarios
                                  SET validado = NOT validado
                                WHERE id = :id');
        $sent->execute([':id' => $id]);
    }

    $sent = $pdo->query("SELECT * FROM usuarios ORDER BY usuario");
    ?>

    <!-- 1.3.C. Al lado de cada usuario en la lista del apartado b), mostrar un botón «Invalidar»
                o «Validar», dependiendo de si el usuario está o no validado, respectivamente. El botón
                «Validar» validará al usuario y el «Invalidar» lo invalidará. -->
                
    <div class="container mx-auto">
        <?php require '../../src/_menu.php' ?>
        <?php require '../../src/_alerts.php' ?>
        <div class="overflow-x-auto relative mt-4">
            <table class="mx-auto text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <th scope="col" class="py-3 px-6">Usuario</th>
                    <th scope="col" class="py-3 px-6 text-center">Acciones</th>
                </thead>
                <tbody>
                    <?php foreach ($sent as $fila) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <!-- Usuarios -->
                            <td class="py-4 px-6"><?= hh($fila['usuario']) ?></td>
                            <!-- Acciones -->
                            <td class="px-6 text-center">
                                <?php $fila_id = hh($fila['id']) ?>
                                <form action="" method="POST" class="inline">
                                    <input type="hidden" name="id" value="<?= $fila_id ?>">
                                    <!-- Botón de validar/invalidar: -->
                                    <?php if ($fila['validado']) : ?>
                                        <!-- Si validado == true -->
                                        <button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                                            Invalidar
                                        </button>
                                    <?php else : ?>
                                        <!-- Si validado == false -->
                                        <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                                            Validar
                                        </button>
                                    <?php endif ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="/js/flowbite/flowbite.js"></script>
</body>

</html>