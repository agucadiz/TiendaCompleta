<?php

namespace App\Generico;

use App\Tablas\Articulo;
use ValueError;

class Carrito extends Modelo
{
    private array $lineas;

    public function __construct()
    {
        $this->lineas = [];
    }

    //Incrementa o crea una nueva linea en el carrito.
    public function insertar($id)
    {
        if (!($articulo = Articulo::obtener($id))) {
            throw new ValueError('El artículo no existe.');
        }

        if (isset($this->lineas[$id])) {
            $this->lineas[$id]->incrCantidad();
        } else {
            $this->lineas[$id] = new Linea($articulo);
        }
    }

    //Decrementa o elimina una linea en el carrito.
    public function eliminar($id)
    {
        if (isset($this->lineas[$id])) {
            $this->lineas[$id]->decrCantidad();
            if ($this->lineas[$id]->getCantidad() == 0) {
                unset($this->lineas[$id]);
            }
        } else {
            throw new ValueError('Artículo inexistente en el carrito');
        }
    }

    //Información si el carrito esta vacio o no.
    public function vacio(): bool
    {
        return empty($this->lineas);
    }

    //Muestra las lineas del carrito.
    public function getLineas(): array
    {
        return $this->lineas;
    }

    //muesta los ids de las lineas del carrito.
    public function getIds(): array
    {
        return array_keys($this->lineas);
    }

    //Muestra la linea indicada por su id.
    public function getLinea($id): Linea
    {
        return $this->lineas[$id];
    }
}
