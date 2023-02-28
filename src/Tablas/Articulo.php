<?php

namespace App\Tablas;

use App\Tablas\Categoria;
use PDO;

class Articulo extends Modelo
{
    protected static string $tabla = 'articulos';

    public $id; //Duda, ¿id publico?
    private $codigo;
    private $descripcion;
    private $precio;
    private $stock;
    private $categoria_id;

    public function __construct(array $campos)
    {
        $this->id = $campos['id'];
        $this->codigo = $campos['codigo'];
        $this->descripcion = $campos['descripcion'];
        $this->precio = $campos['precio'];
        $this->stock = $campos['stock'];
        $this->categoria_id = $campos['categoria_id'];
    }

    //Nos dice si el articulo existe.
    public static function existe(int $id, ?PDO $pdo = null): bool
    {
        return static::obtener($id, $pdo) !== null;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function getID()
    {
        return $this->id;
    }

    //Mostrar categoría en carrito.
    public function getCategoria(?PDO $pdo = null)
    {
        return Categoria::obtener($this->categoria_id, $pdo)->categoria;
    }

    //Insertar artículo.
    public static function insertar($codigo, $descripcion, $precio, $descuento, $categoria_id, $stock, ?PDO $pdo = null)
    {
        $pdo = $pdo ?? conectar();

        $sent = $pdo->prepare('INSERT INTO articulos (codigo, descripcion, precio, descuento, categoria_id, stock)
                                    VALUES (:codigo, :descripcion, :precio, :descuento, :categoria_id, :stock)');
        $sent->execute([':codigo' => $codigo, ':descripcion' => $descripcion, ':precio' => $precio, ':descuento' => $descuento, ':categoria_id' => $categoria_id, ':stock' => $stock]);
    }

    //Modificar artículo.
    public static function modificar($id, $codigo, $descripcion, $precio, $descuento, $categoria_id, $stock, ?PDO $pdo = null)
    {
        $pdo = $pdo ?? conectar();

        $sent = $pdo->prepare("UPDATE articulos
                                  SET codigo = :codigo, descripcion = :descripcion, precio = :precio, descuento = :descuento, categoria_id = :categoria_id, stock = :stock
                                WHERE id = :id");
        $sent->execute([':id' => $id, ':codigo' => $codigo, ':descripcion' => $descripcion, ':precio' => $precio, ':descuento' => $descuento, ':categoria_id' => $categoria_id, ':stock' => $stock]);
    }
}
