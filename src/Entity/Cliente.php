<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClienteRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Cliente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]//validaciones: no puede estar vacío.
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $direccion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaCreacion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fechaCreacion;
    }

    /**
     * Modifico la visibilidad a privada para que un cliente no pueda modificar la fecha
     */
    private function setFechaCreacion(DateTimeInterface $fechaCreacion): static
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * PrePersist es para establecer la fecha de creación automáticamente al insertar el cliente.
     * Tiene que ser público para que Doctrine lo reconozca, pero no admite parámetros.
     */
    #[ORM\PrePersist]
    public function establecerFechaCreacionNuevoCliente(): void
    {
        $this->setFechaCreacion(new DateTimeImmutable());
    }
}
