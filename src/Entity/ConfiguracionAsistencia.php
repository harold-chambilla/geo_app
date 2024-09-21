<?php

namespace App\Entity;

use App\Repository\ConfiguracionAsistenciaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfiguracionAsistenciaRepository::class)]
class ConfiguracionAsistencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cas_modalidad = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cas_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'emp_configuracionesAsistencia')]
    private ?Empresa $cas_empresa = null;

    #[ORM\ManyToOne(inversedBy: 'grp_configuracionesAsistencia')]
    private ?Grupo $cas_grupo = null;

    #[ORM\ManyToOne(inversedBy: 'sed_configuracionesAsistencia')]
    private ?Sede $cas_sede = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cas_area = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cas_puesto = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $cas_estado = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cas_predhorario = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCasModalidad(): ?string
    {
        return $this->cas_modalidad;
    }

    public function setCasModalidad(?string $cas_modalidad): static
    {
        $this->cas_modalidad = $cas_modalidad;

        return $this;
    }

    public function isCasEliminado(): ?bool
    {
        return $this->cas_eliminado;
    }

    public function setCasEliminado(?bool $cas_eliminado): static
    {
        $this->cas_eliminado = $cas_eliminado;

        return $this;
    }

    public function getCasEmpresa(): ?Empresa
    {
        return $this->cas_empresa;
    }

    public function setCasEmpresa(?Empresa $cas_empresa): static
    {
        $this->cas_empresa = $cas_empresa;

        return $this;
    }

    public function getCasGrupo(): ?Grupo
    {
        return $this->cas_grupo;
    }

    public function setCasGrupo(?Grupo $cas_grupo): static
    {
        $this->cas_grupo = $cas_grupo;

        return $this;
    }

    public function getCasSede(): ?Sede
    {
        return $this->cas_sede;
    }

    public function setCasSede(?Sede $cas_sede): static
    {
        $this->cas_sede = $cas_sede;

        return $this;
    }

    public function isCasArea(): ?bool
    {
        return $this->cas_area;
    }

    public function setCasArea(?bool $cas_area): static
    {
        $this->cas_area = $cas_area;

        return $this;
    }

    public function isCasPuesto(): ?bool
    {
        return $this->cas_puesto;
    }

    public function setCasPuesto(?bool $cas_puesto): static
    {
        $this->cas_puesto = $cas_puesto;

        return $this;
    }

    public function getCasEstado(): ?string
    {
        return $this->cas_estado;
    }

    public function setCasEstado(?string $cas_estado): static
    {
        $this->cas_estado = $cas_estado;

        return $this;
    }

    public function isCasPredhorario(): ?bool
    {
        return $this->cas_predhorario;
    }

    public function setCasPredhorario(?bool $cas_predhorario): static
    {
        $this->cas_predhorario = $cas_predhorario;

        return $this;
    }
}
