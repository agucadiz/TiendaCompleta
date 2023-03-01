<?php
session_start();

require '../../src/auxiliar.php';

$id = obtener_post('id');

if (!isset($id)) {
    return header("Location: /admin/categorias.php");
}

$pdo = conectar();
$sent = $pdo->prepare("DELETE FROM categorias WHERE id = :id");
$sent->execute([':id' => $id]);

$_SESSION['exito'] = 'La categoría se ha borrado correctamente.';

volver_categorias();
