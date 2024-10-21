<?php

namespace App\Entity;

use App\Repository\HorarioTrabajoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HorarioTrabajoRepository::class)]
class HorarioTrabajo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $hot_diasemana = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $hot_fecha = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $hot_horaentrada = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $hot_horasalida = null;

    #[ORM\Column(length: 255)]
    private ?string $hot_tipojornada = null;

    #[ORM\Column]
    private ?bool $hot_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'horarioTrabajos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Colaborador $colaborador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHotDiasemana(): ?string
    {
        return $this->hot_diasemana;
    }

    public function setHotDiasemana(string $hot_diasemana): static
    {
        $this->hot_diasemana = $hot_diasemana;

        return $this;
    }

    public function getHotFecha(): ?\DateTimeInterface
    {
        return $this->hot_fecha;
    }

    public function setHotFecha(\DateTimeInterface $hot_fecha): static
    {
        $this->hot_fecha = $hot_fecha;

        return $this;
    }

    public function getHotHoraentrada(): ?\DateTimeInterface
    {
        return $this->hot_horaentrada;
    }

    public function setHotHoraentrada(\DateTimeInterface $hot_horaentrada): static
    {
        $this->hot_horaentrada = $hot_horaentrada;

        return $this;
    }

    public function getHotHorasalida(): ?\DateTimeInterface
    {
        return $this->hot_horasalida;
    }

    public function setHotHorasalida(\DateTimeInterface $hot_horasalida): static
    {
        $this->hot_horasalida = $hot_horasalida;

        return $this;
    }

    public function getHotTipojornada(): ?string
    {
        return $this->hot_tipojornada;
    }

    public function setHotTipojornada(string $hot_tipojornada): static
    {
        $this->hot_tipojornada = $hot_tipojornada;

        return $this;
    }

    public function isHotEliminado(): ?bool
    {
        return $this->hot_eliminado;
    }

    public function setHotEliminado(bool $hot_eliminado): static
    {
        $this->hot_eliminado = $hot_eliminado;

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
