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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hot_diasemana = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hot_horaentrada = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hot_horasalida = null;

    #[ORM\Column(nullable: true)]
    private ?bool $hot_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'col_horariostrabajo')]
    private ?Colaborador $hot_colaborador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHotDiasemana(): ?string
    {
        return $this->hot_diasemana;
    }

    public function setHotDiasemana(?string $hot_diasemana): static
    {
        $this->hot_diasemana = $hot_diasemana;

        return $this;
    }

    public function getHotHoraentrada(): ?\DateTimeInterface
    {
        return $this->hot_horaentrada;
    }

    public function setHotHoraentrada(?\DateTimeInterface $hot_horaentrada): static
    {
        $this->hot_horaentrada = $hot_horaentrada;

        return $this;
    }

    public function getHotHorasalida(): ?\DateTimeInterface
    {
        return $this->hot_horasalida;
    }

    public function setHotHorasalida(?\DateTimeInterface $hot_horasalida): static
    {
        $this->hot_horasalida = $hot_horasalida;

        return $this;
    }

    public function isHotEliminado(): ?bool
    {
        return $this->hot_eliminado;
    }

    public function setHotEliminado(?bool $hot_eliminado): static
    {
        $this->hot_eliminado = $hot_eliminado;

        return $this;
    }

    public function getHotColaborador(): ?Colaborador
    {
        return $this->hot_colaborador;
    }

    public function setHotColaborador(?Colaborador $hot_colaborador): static
    {
        $this->hot_colaborador = $hot_colaborador;

        return $this;
    }
}
