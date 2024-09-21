<?php

namespace App\Entity;

use App\Repository\PermisoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermisoRepository::class)]
class Permiso
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mtv_permisos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Motivo $pms_motivo = null;

    #[ORM\ManyToOne(inversedBy: 'col_permisos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Colaborador $pms_colaborador = null;

    #[ORM\Column]
    private ?bool $pms_eliminado = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $pms_estado = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPmsMotivo(): ?Motivo
    {
        return $this->pms_motivo;
    }

    public function setPmsMotivo(?Motivo $pms_motivo): static
    {
        $this->pms_motivo = $pms_motivo;

        return $this;
    }

    public function getPmsColaborador(): ?Colaborador
    {
        return $this->pms_colaborador;
    }

    public function setPmsColaborador(?Colaborador $pms_colaborador): static
    {
        $this->pms_colaborador = $pms_colaborador;

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

    public function getPmsEstado(): ?string
    {
        return $this->pms_estado;
    }

    public function setPmsEstado(?string $pms_estado): static
    {
        $this->pms_estado = $pms_estado;

        return $this;
    }
}
