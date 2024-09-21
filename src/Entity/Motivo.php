<?php

namespace App\Entity;

use App\Repository\MotivoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotivoRepository::class)]
class Motivo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $mtv_nombre = null;

    #[ORM\Column]
    private ?bool $mtv_eliminado = null;

    /**
     * @var Collection<int, Permiso>
     */
    #[ORM\OneToMany(targetEntity: Permiso::class, mappedBy: 'pms_motivo')]
    private Collection $mtv_permisos;

    public function __construct()
    {
        $this->mtv_permisos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMtvNombre(): ?string
    {
        return $this->mtv_nombre;
    }

    public function setMtvNombre(string $mtv_nombre): static
    {
        $this->mtv_nombre = $mtv_nombre;

        return $this;
    }

    public function isMtvEliminado(): ?bool
    {
        return $this->mtv_eliminado;
    }

    public function setMtvEliminado(bool $mtv_eliminado): static
    {
        $this->mtv_eliminado = $mtv_eliminado;

        return $this;
    }

    /**
     * @return Collection<int, Permiso>
     */
    public function getMtvPermisos(): Collection
    {
        return $this->mtv_permisos;
    }

    public function addMtvPermiso(Permiso $mtvPermiso): static
    {
        if (!$this->mtv_permisos->contains($mtvPermiso)) {
            $this->mtv_permisos->add($mtvPermiso);
            $mtvPermiso->setPmsMotivo($this);
        }

        return $this;
    }

    public function removeMtvPermiso(Permiso $mtvPermiso): static
    {
        if ($this->mtv_permisos->removeElement($mtvPermiso)) {
            // set the owning side to null (unless already changed)
            if ($mtvPermiso->getPmsMotivo() === $this) {
                $mtvPermiso->setPmsMotivo(null);
            }
        }

        return $this;
    }
}
