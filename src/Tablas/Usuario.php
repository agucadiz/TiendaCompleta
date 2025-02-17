<?php
namespace App\Tablas;

use PDO;

class Usuario extends Modelo
{
    protected static string $tabla = 'usuarios';

    public $id;
    public $usuario;
    public $validado;

    public function __construct(array $campos)
    {
        $this->id = $campos['id'];
        $this->usuario = $campos['usuario'];
        $this->validado = $campos['validado'];
    }

    public function es_admin(): bool
    {
        return $this->usuario == 'admin';
    }

    public static function esta_logueado(): bool
    {
        return isset($_SESSION['login']);
    }

    public static function logueado(): ?static
    {
        return isset($_SESSION['login']) ? unserialize($_SESSION['login']) : null; //Devuelve el objeto usuario.
    }

    public static function comprobar($login, $password, ?PDO $pdo = null)
    {
        $pdo = $pdo ?? conectar();

        $sent = $pdo->prepare('SELECT *
                                 FROM usuarios
                                WHERE usuario = :login');
        $sent->execute([':login' => $login]);
        $fila = $sent->fetch(PDO::FETCH_ASSOC);

        if ($fila === false) {
            return false;
        }

        return password_verify($password, $fila['password']) //Duda.
            ? new static($fila)
            : false;
    }

    public static function existe($login, ?PDO $pdo = null): bool //Duda.
    {
        return $login == '' ? false :
            !empty(static::todos(
                ['usuario = :usuario'],
                [':usuario' => $login],
                $pdo
            ));
    }

    public static function registrar($login, $password, ?PDO $pdo = null)
    {
        $sent = $pdo->prepare('INSERT INTO usuarios (usuario, password, validado)
                               VALUES (:login, :password, false)');
        $sent->execute([
            ':login' => $login,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    //Método para cambiar el password de un usuario.
    public function cambiar_password($user, $password, ?PDO $pdo = null)
    {
        $sent = $pdo->prepare("UPDATE usuarios
                                SET password = :password
                                WHERE id = :id");
        $sent->execute([
            ':id' => $user->obtenerId(),
            ':password' => password_hash($password, PASSWORD_DEFAULT),
        ]);
    }

    public function obtenerId()
    {
        return $this->id;
    }
}
