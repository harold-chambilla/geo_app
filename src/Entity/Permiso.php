<?php

namespace App\Entity;

use App\Repository\PermisoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermisoRepository::class)]
class Permiso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pms_estado = null;

    #[ORM\Column]
    private ?bool $pms_eliminado = null;

    /**
     * @var Collection<int, Motivo>
     */
    #[ORM\OneToMany(targetEntity: Motivo::class, mappedBy: 'permiso')]
    private Collection $motivo;

    #[ORM\ManyToOne(inversedBy: 'permisos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Colaborador $colaborador = null;

    public function __construct()
    {
        $this->motivo = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPmsEstado(): ?string
    {
        return $this->pms_estado;
    }

    public function setPmsEstado(string $pms_estado): static
    {
        $this->pms_estado = $pms_estado;

        return $this;
    }

    public function isPmsEliminado(): ?bool
    {
        return $this->pms_eliminado;
    }

    public function setPmsEliminado(bool $pms_eliminado): static
    {
        $this->pms_eliminado = $pms_eliminado;

        return $this;
    }

    /**
     * @return Collection<int, Motivo>
     */
    public function getMotivo(): Collection
    {
        return $this->motivo;
    }

    public function addMotivo(Motivo $motivo): static
    {
        if (!$this->motivo->contains($motivo)) {
            $this->motivo->add($motivo);
            $motivo->setPermiso($this);
        }

        return $this;
    }

    public function removeMotivo(Motivo $motivo): static
    {
        if ($this->motivo->removeElement($motivo)) {
            // set the owning side to null (unless already changed)
            if ($motivo->getPermiso() === $this) {
                $motivo->setPermiso(null);
            }
        }

        return $this;
    }

    public function getColaborador(): ?Colaborador
    {
        return $this->colaborador;
    }

    public function setColaborador(?Colaborador $colaborador): static
    {
        $this->colaborador = $colaborador;

        return $this;
    }
}
