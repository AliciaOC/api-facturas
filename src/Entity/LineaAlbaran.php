<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class LineaAlbaran
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Type(type: 'integer')]
    #[Assert\Positive]
    private ?int $producto = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $nombreProducto = null;

    #[ORM\Column]
    #[Assert\Type(type: 'float')]
    #[Assert\Positive]
    private ?float $cantidad = null;

    #[ORM\Column]
    #[Assert\Type(type: 'float')]
    #[Assert\Positive]
    private ?float $precioUnitario = null;

    #[ORM\ManyToOne(inversedBy: 'lineas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Albaran $albaran = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaCreacion = null;

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

    public function getAlbaran(): ?Albaran
    {
        return $this->albaran;
    }

    public function setAlbaran(?Albaran $albaran): static
    {
        $this->albaran = $albaran;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    private function setFechaCreacion(\DateTimeInterface $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    #[ORM\PrePersist]
    public function establecerFechaCreacionNuevaLineaAlbaran(): void
    {
        $this->setFechaCreacion(new DateTimeImmutable());
    }
}
