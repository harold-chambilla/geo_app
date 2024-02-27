<?php

namespace App\Entity;

use App\Repository\AsistenciaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsistenciaRepository::class)]
class Asistencia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $asi_fechaentrada = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $asi_fechasalida = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $asi_horaentrada = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $asi_horasalida = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $asi_fotoentrada = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $asi_fotosalida = null;

    #[ORM\Column(nullable: true)]
    private ?array $asi_ubicacionentrada = null;

    #[ORM\Column(nullable: true)]
    private ?array $asi_ubicacionsalida = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $asi_estadoentrada = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $asi_estadosalida = null;

    #[ORM\Column(nullable: true)]
    private ?array $asi_notas = null;

    #[ORM\Column(nullable: true)]
    private ?bool $asi_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'col_asistencias')]
    private ?Colaborador $asi_colaborador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsiFechaentrada(): ?\DateTimeInterface
    {
        return $this->asi_fechaentrada;
    }

    public function setAsiFechaentrada(?\DateTimeInterface $asi_fechaentrada): static
    {
        $this->asi_fechaentrada = $asi_fechaentrada;

        return $this;
    }

    public function getAsiFechasalida(): ?\DateTimeInterface
    {
        return $this->asi_fechasalida;
    }

    public function setAsiFechasalida(?\DateTimeInterface $asi_fechasalida): static
    {
        $this->asi_fechasalida = $asi_fechasalida;

        return $this;
    }

    public function getAsiHoraentrada(): ?\DateTimeInterface
    {
        return $this->asi_horaentrada;
    }

    public function setAsiHoraentrada(?\DateTimeInterface $asi_horaentrada): static
    {
        $this->asi_horaentrada = $asi_horaentrada;

        return $this;
    }

    public function getAsiHorasalida(): ?\DateTimeInterface
    {
        return $this->asi_horasalida;
    }

    public function setAsiHorasalida(?\DateTimeInterface $asi_horasalida): static
    {
        $this->asi_horasalida = $asi_horasalida;

        return $this;
    }

    public function getAsiFotoentrada(): ?string
    {
        return $this->asi_fotoentrada;
    }

    public function setAsiFotoentrada(?string $asi_fotoentrada): static
    {
        $this->asi_fotoentrada = $asi_fotoentrada;

        return $this;
    }

    public function getAsiFotosalida(): ?string
    {
        return $this->asi_fotosalida;
    }

    public function setAsiFotosalida(?string $asi_fotosalida): static
    {
        $this->asi_fotosalida = $asi_fotosalida;

        return $this;
    }

    public function getAsiUbicacionentrada(): ?array
    {
        return $this->asi_ubicacionentrada;
    }

    public function setAsiUbicacionentrada(?array $asi_ubicacionentrada): static
    {
        $this->asi_ubicacionentrada = $asi_ubicacionentrada;

        return $this;
    }

    public function getAsiUbicacionsalida(): ?array
    {
        return $this->asi_ubicacionsalida;
    }

    public function setAsiUbicacionsalida(?array $asi_ubicacionsalida): static
    {
        $this->asi_ubicacionsalida = $asi_ubicacionsalida;

        return $this;
    }

    public function getAsiEstadoentrada(): ?string
    {
        return $this->asi_estadoentrada;
    }

    public function setAsiEstadoentrada(?string $asi_estadoentrada): static
    {
        $this->asi_estadoentrada = $asi_estadoentrada;

        return $this;
    }

    public function getAsiEstadosalida(): ?string
    {
        return $this->asi_estadosalida;
    }

    public function setAsiEstadosalida(?string $asi_estadosalida): static
    {
        $this->asi_estadosalida = $asi_estadosalida;

        return $this;
    }

    public function getAsiNotas(): ?array
    {
        return $this->asi_notas;
    }

    public function setAsiNotas(?array $asi_notas): static
    {
        $this->asi_notas = $asi_notas;

        return $this;
    }

    public function isAsiEliminado(): ?bool
    {
        return $this->asi_eliminado;
    }

    public function setAsiEliminado(?bool $asi_eliminado): static
    {
        $this->asi_eliminado = $asi_eliminado;

        return $this;
    }

    public function getAsiColaborador(): ?Colaborador
    {
        return $this->asi_colaborador;
    }

    public function setAsiColaborador(?Colaborador $asi_colaborador): static
    {
        $this->asi_colaborador = $asi_colaborador;

        return $this;
    }
}
