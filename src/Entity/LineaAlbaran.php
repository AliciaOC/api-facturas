<?php

namespace App\Entity;

use App\Repository\LineaAlbaranRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LineaAlbaranRepository::class)]
class LineaAlbaran
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $producto = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreProducto = null;

    #[ORM\Column]
    private ?float $cantidad = null;

    #[ORM\Column]
    private ?float $precioUnitario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProducto(): ?int
    {
        return $this->producto;
    }

    public function setProducto(int $producto): static
    {
        $this->producto = $producto;

        return $this;
    }

    public function getNombreProducto(): ?string
    {
        return $this->nombreProducto;
    }

    public function setNombreProducto(string $nombreProducto): static
    {
        $this->nombreProducto = $nombreProducto;

        return $this;
    }

    public function getCantidad(): ?float
    {
        return $this->cantidad;
    }

    public function setCantidad(float $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecioUnitario(): ?float
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(float $precioUnitario): static
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }
}
