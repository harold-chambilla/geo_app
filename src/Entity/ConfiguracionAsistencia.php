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

    #[ORM\Column]
    private ?int $cas_tiempo_falta_horas = null;

    #[ORM\Column]
    private ?int $cas_tolerancia_ingreso_minutos = null;

    #[ORM\Column]
    private ?bool $cas_permitir_foto = null;

    #[ORM\Column]
    private ?bool $cas_faltas_tardanzas = null;

    #[ORM\Column]
    private ?bool $cas_permisos = null;

    #[ORM\Column]
    private ?bool $cas_vacaciones = null;

    #[ORM\Column]
    private ?bool $cas_marcacion = null;

    #[ORM\Column(length: 255)]
    private ?string $cas_modalidad = null;

    #[ORM\Column]
    private ?bool $cas_area = null;

    #[ORM\Column]
    private ?bool $cas_puesto = null;

    #[ORM\Column]
    private ?bool $cas_predhorario = null;

    #[ORM\Column]
    private ?bool $cas_eliminado = null;

    #[ORM\Column(length: 255)]
    private ?string $cas_estado = null;

    #[ORM\ManyToOne(inversedBy: 'configuracionAsistencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Grupo $grupo = null;

    #[ORM\ManyToOne(inversedBy: 'configuracionAsistencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sede $sede = null;

    #[ORM\ManyToOne(inversedBy: 'configuracionAsistencias')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Puesto $puesto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCasTiempoFaltaHoras(): ?int
    {
        return $this->cas_tiempo_falta_horas;
    }

    public function setCasTiempoFaltaHoras(int $cas_tiempo_falta_horas): static
    {
        $this->cas_tiempo_falta_horas = $cas_tiempo_falta_horas;

        return $this;
    }

    public function getCasToleranciaIngresoMinutos(): ?int
    {
        return $this->cas_tolerancia_ingreso_minutos;
    }

    public function setCasToleranciaIngresoMinutos(int $cas_tolerancia_ingreso_minutos): static
    {
        $this->cas_tolerancia_ingreso_minutos = $cas_tolerancia_ingreso_minutos;

        return $this;
    }

    public function isCasPermitirFoto(): ?bool
    {
        return $this->cas_permitir_foto;
    }

    public function setCasPermitirFoto(bool $cas_permitir_foto): static
    {
        $this->cas_permitir_foto = $cas_permitir_foto;

        return $this;
    }

    public function isCasFaltasTardanzas(): ?bool
    {
        return $this->cas_faltas_tardanzas;
    }

    public function setCasFaltasTardanzas(bool $cas_faltas_tardanzas): static
    {
        $this->cas_faltas_tardanzas = $cas_faltas_tardanzas;

        return $this;
    }

    public function isCasPermisos(): ?bool
    {
        return $this->cas_permisos;
    }

    public function setCasPermisos(bool $cas_permisos): static
    {
        $this->cas_permisos = $cas_permisos;

        return $this;
    }

    public function isCasVacaciones(): ?bool
    {
        return $this->cas_vacaciones;
    }

    public function setCasVacaciones(bool $cas_vacaciones): static
    {
        $this->cas_vacaciones = $cas_vacaciones;

        return $this;
    }

    public function isCasMarcacion(): ?bool
    {
        return $this->cas_marcacion;
    }

    public function setCasMarcacion(bool $cas_marcacion): static
    {
        $this->cas_marcacion = $cas_marcacion;

        return $this;
    }

    public function getCasModalidad(): ?string
    {
        return $this->cas_modalidad;
    }

    public function setCasModalidad(string $cas_modalidad): static
    {
        $this->cas_modalidad = $cas_modalidad;

        return $this;
    }

    public function isCasArea(): ?bool
    {
        return $this->cas_area;
    }

    public function setCasArea(bool $cas_area): static
    {
        $this->cas_area = $cas_area;

        return $this;
    }

    public function isCasPuesto(): ?bool
    {
        return $this->cas_puesto;
    }

    public function setCasPuesto(bool $cas_puesto): static
    {
        $this->cas_puesto = $cas_puesto;

        return $this;
    }

    public function isCasPredhorario(): ?bool
    {
        return $this->cas_predhorario;
    }

    public function setCasPredhorario(bool $cas_predhorario): static
    {
        $this->cas_predhorario = $cas_predhorario;

        return $this;
    }

    public function isCasEliminado(): ?bool
    {
        return $this->cas_eliminado;
    }

    public function setCasEliminado(bool $cas_eliminado): static
    {
        $this->cas_eliminado = $cas_eliminado;

        return $this;
    }

    public function getCasEstado(): ?string
    {
        return $this->cas_estado;
    }

    public function setCasEstado(string $cas_estado): static
    {
        $this->cas_estado = $cas_estado;

        return $this;
    }

    public function getGrupo(): ?Grupo
    {
        return $this->grupo;
    }

    public function setGrupo(?Grupo $grupo): static
    {
        $this->grupo = $grupo;

        return $this;
    }

    public function getSede(): ?Sede
    {
        return $this->sede;
    }

    public function setSede(?Sede $sede): static
    {
        $this->sede = $sede;

        return $this;
    }

    public function getPuesto(): ?Puesto
    {
        return $this->puesto;
    }

    public function setPuesto(?Puesto $puesto): static
    {
        $this->puesto = $puesto;

        return $this;
    }
}
