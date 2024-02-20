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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sed_nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sed_pais = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sed_direccion = null;

    #[ORM\Column(nullable: true)]
    private ?array $sed_ubicacion = null;

    #[ORM\Column(nullable: true)]
    private ?bool $sed_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'emp_sedes')]
    private ?Empresa $sed_empresa = null;

    #[ORM\OneToMany(targetEntity: ConfiguracionAsistencia::class, mappedBy: 'cas_sede')]
    private Collection $sed_configuracionesAsistencia;

    public function __construct()
    {
        $this->sed_configuracionesAsistencia = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSedNombre(): ?string
    {
        return $this->sed_nombre;
    }

    public function setSedNombre(?string $sed_nombre): static
    {
        $this->sed_nombre = $sed_nombre;

        return $this;
    }

    public function getSedPais(): ?string
    {
        return $this->sed_pais;
    }

    public function setSedPais(?string $sed_pais): static
    {
        $this->sed_pais = $sed_pais;

        return $this;
    }

    public function getSedDireccion(): ?string
    {
        return $this->sed_direccion;
    }

    public function setSedDireccion(?string $sed_direccion): static
    {
        $this->sed_direccion = $sed_direccion;

        return $this;
    }

    public function getSedUbicacion(): ?array
    {
        return $this->sed_ubicacion;
    }

    public function setSedUbicacion(?array $sed_ubicacion): static
    {
        $this->sed_ubicacion = $sed_ubicacion;

        return $this;
    }

    public function isSedEliminado(): ?bool
    {
        return $this->sed_eliminado;
    }

    public function setSedEliminado(?bool $sed_eliminado): static
    {
        $this->sed_eliminado = $sed_eliminado;

        return $this;
    }

    public function getSedEmpresa(): ?Empresa
    {
        return $this->sed_empresa;
    }

    public function setSedEmpresa(?Empresa $sed_empresa): static
    {
        $this->sed_empresa = $sed_empresa;

        return $this;
    }

    /**
     * @return Collection<int, ConfiguracionAsistencia>
     */
    public function getSedConfiguracionesAsistencia(): Collection
    {
        return $this->sed_configuracionesAsistencia;
    }

    public function addSedConfiguracionesAsistencium(ConfiguracionAsistencia $sedConfiguracionesAsistencium): static
    {
        if (!$this->sed_configuracionesAsistencia->contains($sedConfiguracionesAsistencium)) {
            $this->sed_configuracionesAsistencia->add($sedConfiguracionesAsistencium);
            $sedConfiguracionesAsistencium->setCasSede($this);
        }

        return $this;
    }

    public function removeSedConfiguracionesAsistencium(ConfiguracionAsistencia $sedConfiguracionesAsistencium): static
    {
        if ($this->sed_configuracionesAsistencia->removeElement($sedConfiguracionesAsistencium)) {
            // set the owning side to null (unless already changed)
            if ($sedConfiguracionesAsistencium->getCasSede() === $this) {
                $sedConfiguracionesAsistencium->setCasSede(null);
            }
        }

        return $this;
    }
}
