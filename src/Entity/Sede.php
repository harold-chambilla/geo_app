<?php

namespace App\Entity;

use App\Repository\SedeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SedeRepository::class)]
class Sede
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sed_nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $sed_pais = null;

    #[ORM\Column(length: 255)]
    private ?string $sed_direccion = null;

    #[ORM\Column(length: 255)]
    private ?string $sed_ubicacion = null;

    #[ORM\Column]
    private ?bool $sed_eliminado = null;

    /**
     * @var Collection<int, ConfiguracionAsistencia>
     */
    #[ORM\OneToMany(targetEntity: ConfiguracionAsistencia::class, mappedBy: 'sede')]
    private Collection $configuracionAsistencias;

    public function __construct()
    {
        $this->configuracionAsistencias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSedNombre(): ?string
    {
        return $this->sed_nombre;
    }

    public function setSedNombre(string $sed_nombre): static
    {
        $this->sed_nombre = $sed_nombre;

        return $this;
    }

    public function getSedPais(): ?string
    {
        return $this->sed_pais;
    }

    public function setSedPais(string $sed_pais): static
    {
        $this->sed_pais = $sed_pais;

        return $this;
    }

    public function getSedDireccion(): ?string
    {
        return $this->sed_direccion;
    }

    public function setSedDireccion(string $sed_direccion): static
    {
        $this->sed_direccion = $sed_direccion;

        return $this;
    }

    public function getSedUbicacion(): ?string
    {
        return $this->sed_ubicacion;
    }

    public function setSedUbicacion(string $sed_ubicacion): static
    {
        $this->sed_ubicacion = $sed_ubicacion;

        return $this;
    }

    public function isSedEliminado(): ?bool
    {
        return $this->sed_eliminado;
    }

    public function setSedEliminado(bool $sed_eliminado): static
    {
        $this->sed_eliminado = $sed_eliminado;

        return $this;
    }

    /**
     * @return Collection<int, ConfiguracionAsistencia>
     */
    public function getConfiguracionAsistencias(): Collection
    {
        return $this->configuracionAsistencias;
    }

    public function addConfiguracionAsistencia(ConfiguracionAsistencia $configuracionAsistencia): static
    {
        if (!$this->configuracionAsistencias->contains($configuracionAsistencia)) {
            $this->configuracionAsistencias->add($configuracionAsistencia);
            $configuracionAsistencia->setSede($this);
        }

        return $this;
    }

    public function removeConfiguracionAsistencia(ConfiguracionAsistencia $configuracionAsistencia): static
    {
        if ($this->configuracionAsistencias->removeElement($configuracionAsistencia)) {
            // set the owning side to null (unless already changed)
            if ($configuracionAsistencia->getSede() === $this) {
                $configuracionAsistencia->setSede(null);
            }
        }

        return $this;
    }
}
