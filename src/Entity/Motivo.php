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

    #[ORM\Column(length: 255)]
    private ?string $mtv_nombre = null;

    #[ORM\Column]
    private ?bool $mtv_eliminado = null;

    /**
     * @var Collection<int, Permiso>
     */
    #[ORM\OneToMany(targetEntity: Permiso::class, mappedBy: 'motivo')]
    private Collection $permisos;

    public function __construct()
    {
        $this->permisos = new ArrayCollection();
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
    public function getPermisos(): Collection
    {
        return $this->permisos;
    }

    public function addPermiso(Permiso $permiso): static
    {
        if (!$this->permisos->contains($permiso)) {
            $this->permisos->add($permiso);
            $permiso->setMotivo($this);
        }

        return $this;
    }

    public function removePermiso(Permiso $permiso): static
    {
        if ($this->permisos->removeElement($permiso)) {
            // set the owning side to null (unless already changed)
            if ($permiso->getMotivo() === $this) {
                $permiso->setMotivo(null);
            }
        }

        return $this;
    }
}
