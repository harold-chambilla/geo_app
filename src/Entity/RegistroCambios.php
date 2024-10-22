<?php

namespace App\Entity;

use App\Repository\RegistroCambiosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistroCambiosRepository::class)]
class RegistroCambios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reg_tablaafectada = null;

    #[ORM\Column(length: 255)]
    private ?string $reg_campoafectado = null;

    #[ORM\Column(length: 255)]
    private ?string $reg_valoranterior = null;

    #[ORM\Column(length: 255)]
    private ?string $reg_valornuevo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $reg_fecha = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegTablaafectada(): ?string
    {
        return $this->reg_tablaafectada;
    }

    public function setRegTablaafectada(string $reg_tablaafectada): static
    {
        $this->reg_tablaafectada = $reg_tablaafectada;

        return $this;
    }

    public function getRegCampoafectado(): ?string
    {
        return $this->reg_campoafectado;
    }

    public function setRegCampoafectado(string $reg_campoafectado): static
    {
        $this->reg_campoafectado = $reg_campoafectado;

        return $this;
    }

    public function getRegValoranterior(): ?string
    {
        return $this->reg_valoranterior;
    }

    public function setRegValoranterior(string $reg_valoranterior): static
    {
        $this->reg_valoranterior = $reg_valoranterior;

        return $this;
    }

    public function getRegValornuevo(): ?string
    {
        return $this->reg_valornuevo;
    }

    public function setRegValornuevo(string $reg_valornuevo): static
    {
        $this->reg_valornuevo = $reg_valornuevo;

        return $this;
    }

    public function getRegFecha(): ?\DateTimeInterface
    {
        return $this->reg_fecha;
    }

    public function setRegFecha(\DateTimeInterface $reg_fecha): static
    {
        $this->reg_fecha = $reg_fecha;

        return $this;
    }
}
