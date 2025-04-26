<?php

namespace App\Entity;

use App\Model\AlbaranEstadosEnum;
use App\Repository\AlbaranRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbaranRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Albaran
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     *AlbaranEstadosEnum es un enum que define los estados del albarán, como un diccionario. 
     *Elijo usar Enum en vez de un boolean porque es más realista pensar que los albaranes pueden tener más de 2 estados.
     */
    #[ORM\Column(enumType: AlbaranEstadosEnum::class)]
    private ?AlbaranEstadosEnum $estado = AlbaranEstadosEnum::Abierto;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cliente $cliente = null;

    /**
     * @var Collection<int, LineaAlbaran>
     */
    #[ORM\OneToMany(targetEntity: LineaAlbaran::class, mappedBy: 'albaran', orphanRemoval: true, cascade: ['persist', 'remove'])]
    private Collection $lineas;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaCreacion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fechaActualizacion = null;

    #[ORM\ManyToOne(inversedBy: 'albaranes')]
    private ?Factura $factura = null;

    public function __construct()
    {
        $this->lineas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstado(): ?AlbaranEstadosEnum
    {
        return $this->estado;
    }

    public function setEstado(AlbaranEstadosEnum $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): static
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * @return Collection<int, LineaAlbaran>
     */
    public function getLineas(): Collection
    {
        return $this->lineas;
    }

    public function addLinea(LineaAlbaran $linea): static
    {
        if (!$this->lineas->contains($linea)) {
            $this->lineas->add($linea);
            $linea->setAlbaran($this);
        }

        return $this;
    }

    public function removeLinea(LineaAlbaran $linea): static
    {
        if ($this->lineas->removeElement($linea)) {
            // set the owning side to null (unless already changed)
            if ($linea->getAlbaran() === $this) {
                $linea->setAlbaran(null);
            }
        }

        return $this;
    }

    public function reiniciarLineas(): static
    {
        foreach ($this->lineas as $linea) {
            $this->removeLinea($linea);
        }

        return $this;
    }

    public function getFactura(): ?Factura
    {
        return $this->factura;
    }

    public function setFactura(?Factura $factura): static
    {
        $this->factura = $factura;

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
    public function establecerFechaCreacionNuevoAlbaran(): void
    {
        $this->setFechaCreacion(new DateTimeImmutable());
    }

    public function getFechaActualizacion(): ?\DateTimeInterface
    {
        return $this->fechaActualizacion;
    }

    private function setFechaActualizacion(?\DateTimeInterface $fechaActualizacion): static
    {
        $this->fechaActualizacion = $fechaActualizacion;

        return $this;
    }

    /**
     * PreUpdate es para establecer la fecha de actualización automáticamente al actualizar el albarán.
     * Similar al funcionamiento de PrePersist.
     */
    #[ORM\PreUpdate]
    public function establecerFechaActualizacionAlbaran(): void
    {
        $this->setFechaActualizacion(new DateTimeImmutable());
    }
}
 