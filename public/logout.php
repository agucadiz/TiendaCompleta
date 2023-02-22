<?php
session_start();

require '../src/auxiliar.php';

$_SESSION = [];
$params = session_get_cookie_params(); 
//Duda, ¿borrar cookies?
setcookie(
    session_name(),
    '',
    1,
    $params['path'],
    $params['domain'],
    $params['secure'],
    $params['httponly']
);
session_destroy();
volver();
