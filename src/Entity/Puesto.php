<?php

namespace App\Entity;

use App\Repository\PuestoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuestoRepository::class)]
class Puesto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pst_nombre = null;

    #[ORM\Column]
    private ?bool $pst_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'puestos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Area $area = null;

    /**
     * @var Collection<int, ConfiguracionAsistencia>
     */
    #[ORM\OneToMany(targetEntity: ConfiguracionAsistencia::class, mappedBy: 'puesto')]
    private Collection $configuracionAsistencias;

    public function __construct()
    {
        $this->configuracionAsistencias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPstNombre(): ?string
    {
        return $this->pst_nombre;
    }

    public function setPstNombre(string $pst_nombre): static
    {
        $this->pst_nombre = $pst_nombre;

        return $this;
    }

    public function isPstEliminado(): ?bool
    {
        return $this->pst_eliminado;
    }

    public function setPstEliminado(bool $pst_eliminado): static
    {
        $this->pst_eliminado = $pst_eliminado;

        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): static
    {
        $this->area = $area;

        return $this;
    }

    /**
     * @return Collection<int, ConfiguracionAsistencia>
     */
    public function getConfiguracionAsistencias(): Collection
    {
        return $this->configuracionAsistencias;
    }

    public function addConfiguracionAsistencia(ConfiguracionAsistencia $configuracionAsistencia): static
    {
        if (!$this->configuracionAsistencias->contains($configuracionAsistencia)) {
            $this->configuracionAsistencias->add($configuracionAsistencia);
            $configuracionAsistencia->setPuesto($this);
        }

        return $this;
    }

    public function removeConfiguracionAsistencia(ConfiguracionAsistencia $configuracionAsistencia): static
    {
        if ($this->configuracionAsistencias->removeElement($configuracionAsistencia)) {
            // set the owning side to null (unless already changed)
            if ($configuracionAsistencia->getPuesto() === $this) {
                $configuracionAsistencia->setPuesto(null);
            }
        }

        return $this;
    }
}
