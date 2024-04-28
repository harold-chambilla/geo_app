<?php

namespace App\Entity;

use App\Repository\PuestoRepository;
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

    #[ORM\ManyToOne(inversedBy: 'ara_puestos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Area $pst_area = null;

    #[ORM\ManyToOne(inversedBy: 'puestos')]
    private ?Colaborador $pst_colaborador = null;

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

    public function getPstArea(): ?Area
    {
        return $this->pst_area;
    }

    public function setPstArea(?Area $pst_area): static
    {
        $this->pst_area = $pst_area;

        return $this;
    }

    public function getPstColaborador(): ?Colaborador
    {
        return $this->pst_colaborador;
    }

    public function setPstColaborador(?Colaborador $pst_colaborador): static
    {
        $this->pst_colaborador = $pst_colaborador;

        return $this;
    }
}
