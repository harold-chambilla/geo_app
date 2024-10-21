<?php

namespace App\Entity;

use App\Repository\AreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AreaRepository::class)]
class Area
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ara_nombre = null;

    #[ORM\Column]
    private ?bool $ara_eliminado = null;

    /**
     * @var Collection<int, Puesto>
     */
    #[ORM\OneToMany(targetEntity: Puesto::class, mappedBy: 'area')]
    private Collection $puestos;

    public function __construct()
    {
        $this->puestos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAraNombre(): ?string
    {
        return $this->ara_nombre;
    }

    public function setAraNombre(string $ara_nombre): static
    {
        $this->ara_nombre = $ara_nombre;

        return $this;
    }

    public function isAraEliminado(): ?bool
    {
        return $this->ara_eliminado;
    }

    public function setAraEliminado(bool $ara_eliminado): static
    {
        $this->ara_eliminado = $ara_eliminado;

        return $this;
    }

    /**
     * @return Collection<int, Puesto>
     */
    public function getPuestos(): Collection
    {
        return $this->puestos;
    }

    public function addPuesto(Puesto $puesto): static
    {
        if (!$this->puestos->contains($puesto)) {
            $this->puestos->add($puesto);
            $puesto->setArea($this);
        }

        return $this;
    }

    public function removePuesto(Puesto $puesto): static
    {
        if ($this->puestos->removeElement($puesto)) {
            // set the owning side to null (unless already changed)
            if ($puesto->getArea() === $this) {
                $puesto->setArea(null);
            }
        }

        return $this;
    }
}
