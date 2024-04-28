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

    #[ORM\ManyToOne(inversedBy: 'emp_areas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Empresa $ara_empresa = null;

    #[ORM\OneToMany(targetEntity: Puesto::class, mappedBy: 'pst_area')]
    private Collection $ara_puestos;

    public function __construct()
    {
        $this->ara_puestos = new ArrayCollection();
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

    public function getAraEmpresa(): ?Empresa
    {
        return $this->ara_empresa;
    }

    public function setAraEmpresa(?Empresa $ara_empresa): static
    {
        $this->ara_empresa = $ara_empresa;

        return $this;
    }

    /**
     * @return Collection<int, Puesto>
     */
    public function getAraPuestos(): Collection
    {
        return $this->ara_puestos;
    }

    public function addAraPuesto(Puesto $araPuesto): static
    {
        if (!$this->ara_puestos->contains($araPuesto)) {
            $this->ara_puestos->add($araPuesto);
            $araPuesto->setPstArea($this);
        }

        return $this;
    }

    public function removeAraPuesto(Puesto $araPuesto): static
    {
        if ($this->ara_puestos->removeElement($araPuesto)) {
            // set the owning side to null (unless already changed)
            if ($araPuesto->getPstArea() === $this) {
                $araPuesto->setPstArea(null);
            }
        }

        return $this;
    }
}
