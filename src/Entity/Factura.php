<?php

namespace App\Entity;

use App\Repository\FacturaRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FacturaRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Factura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Type(type: 'float')]
    #[Assert\Positive]
    private ?float $importeTotal = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cliente $cliente = null;

    /**
     * @var Collection<int, Albaran>
     */
    #[ORM\OneToMany(targetEntity: Albaran::class, mappedBy: 'factura')]
    private Collection $albaranes;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaCreacion = null;

    public function __construct()
    {
        $this->albaranes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImporteTotal(): ?float
    {
        return $this->importeTotal;
    }

    public function setImporteTotal(float $importeTotal): static
    {
        $this->importeTotal = $importeTotal;

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
     * @return Collection<int, Albaran>
     */
    public function getAlbaranes(): Collection
    {
        return $this->albaranes;
    }

    public function addAlbaran(Albaran $albaran): static
    {
        if (!$this->albaranes->contains($albaran)) {
            $this->albaranes->add($albaran);
            $albaran->setFactura($this);
        }

        return $this;
    }

    public function removeAlbaran(Albaran $albaran): static
    {
        if ($this->albaranes->removeElement($albaran)) {
            // set the owning side to null (unless already changed)
            if ($albaran->getFactura() === $this) {
                $albaran->setFactura(null);
            }
        }

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
    public function establecerFechaCreacionNuevaFactura(): void
    {
        $this->setFechaCreacion(new DateTimeImmutable());
    }
}
