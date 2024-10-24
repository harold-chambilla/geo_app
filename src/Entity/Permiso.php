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

    #[ORM\ManyToOne(inversedBy: 'permisos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Colaborador $colaborador = null;

    #[ORM\ManyToOne(inversedBy: 'permisos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Motivo $motivo = null;

    public function __construct(){}

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

    public function getColaborador(): ?Colaborador
    {
        return $this->colaborador;
    }

    public function setColaborador(?Colaborador $colaborador): static
    {
        $this->colaborador = $colaborador;

        return $this;
    }

    public function getMotivo(): ?Motivo
    {
        return $this->motivo;
    }

    public function setMotivo(?Motivo $motivo): static
    {
        $this->motivo = $motivo;

        return $this;
    }
}
