<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
class Empresa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emp_ruc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emp_nombreempresa = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emp_industria = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emp_telefono = null;

    #[ORM\Column(nullable: true)]
    private ?int $emp_cantidadempleados = null;

    #[ORM\Column(nullable: true)]
    private ?bool $emp_eliminado = null;

    #[ORM\OneToMany(targetEntity: Sede::class, mappedBy: 'sed_empresa')]
    private Collection $emp_sedes;

    #[ORM\OneToMany(targetEntity: Grupo::class, mappedBy: 'grp_empresa')]
    private Collection $emp_grupos;

    #[ORM\OneToMany(targetEntity: ConfiguracionAsistencia::class, mappedBy: 'cas_empresa')]
    private Collection $emp_configuracionesAsistencia;

    #[ORM\OneToMany(targetEntity: Colaborador::class, mappedBy: 'col_empresa')]
    private Collection $emp_colaboradores;

    #[ORM\OneToMany(targetEntity: Area::class, mappedBy: 'ara_empresa')]
    private Collection $emp_areas;

    public function __construct()
    {
        $this->emp_sedes = new ArrayCollection();
        $this->emp_grupos = new ArrayCollection();
        $this->emp_configuracionesAsistencia = new ArrayCollection();
        $this->emp_colaboradores = new ArrayCollection();
        $this->emp_areas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmpRuc(): ?string
    {
        return $this->emp_ruc;
    }

    public function setEmpRuc(?string $emp_ruc): static
    {
        $this->emp_ruc = $emp_ruc;

        return $this;
    }

    public function getEmpNombreempresa(): ?string
    {
        return $this->emp_nombreempresa;
    }

    public function setEmpNombreempresa(?string $emp_nombreempresa): static
    {
        $this->emp_nombreempresa = $emp_nombreempresa;

        return $this;
    }

    public function getEmpIndustria(): ?string
    {
        return $this->emp_industria;
    }

    public function setEmpIndustria(?string $emp_industria): static
    {
        $this->emp_industria = $emp_industria;

        return $this;
    }

    public function getEmpTelefono(): ?string
    {
        return $this->emp_telefono;
    }

    public function setEmpTelefono(?string $emp_telefono): static
    {
        $this->emp_telefono = $emp_telefono;

        return $this;
    }

    public function getEmpCantidadempleados(): ?int
    {
        return $this->emp_cantidadempleados;
    }

    public function setEmpCantidadempleados(?int $emp_cantidadempleados): static
    {
        $this->emp_cantidadempleados = $emp_cantidadempleados;

        return $this;
    }

    public function isEmpEliminado(): ?bool
    {
        return $this->emp_eliminado;
    }

    public function setEmpEliminado(?bool $emp_eliminado): static
    {
        $this->emp_eliminado = $emp_eliminado;

        return $this;
    }

    /**
     * @return Collection<int, Sede>
     */
    public function getEmpSedes(): Collection
    {
        return $this->emp_sedes;
    }

    public function addEmpSede(Sede $empSede): static
    {
        if (!$this->emp_sedes->contains($empSede)) {
            $this->emp_sedes->add($empSede);
            $empSede->setSedEmpresa($this);
        }

        return $this;
    }

    public function removeEmpSede(Sede $empSede): static
    {
        if ($this->emp_sedes->removeElement($empSede)) {
            // set the owning side to null (unless already changed)
            if ($empSede->getSedEmpresa() === $this) {
                $empSede->setSedEmpresa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Grupo>
     */
    public function getEmpGrupos(): Collection
    {
        return $this->emp_grupos;
    }

    public function addEmpGrupo(Grupo $empGrupo): static
    {
        if (!$this->emp_grupos->contains($empGrupo)) {
            $this->emp_grupos->add($empGrupo);
            $empGrupo->setGrpEmpresa($this);
        }

        return $this;
    }

    public function removeEmpGrupo(Grupo $empGrupo): static
    {
        if ($this->emp_grupos->removeElement($empGrupo)) {
            // set the owning side to null (unless already changed)
            if ($empGrupo->getGrpEmpresa() === $this) {
                $empGrupo->setGrpEmpresa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ConfiguracionAsistencia>
     */
    public function getEmpConfiguracionesAsistencia(): Collection
    {
        return $this->emp_configuracionesAsistencia;
    }

    public function addEmpConfiguracionesAsistencium(ConfiguracionAsistencia $empConfiguracionesAsistencium): static
    {
        if (!$this->emp_configuracionesAsistencia->contains($empConfiguracionesAsistencium)) {
            $this->emp_configuracionesAsistencia->add($empConfiguracionesAsistencium);
            $empConfiguracionesAsistencium->setCasEmpresa($this);
        }

        return $this;
    }

    public function removeEmpConfiguracionesAsistencium(ConfiguracionAsistencia $empConfiguracionesAsistencium): static
    {
        if ($this->emp_configuracionesAsistencia->removeElement($empConfiguracionesAsistencium)) {
            // set the owning side to null (unless already changed)
            if ($empConfiguracionesAsistencium->getCasEmpresa() === $this) {
                $empConfiguracionesAsistencium->setCasEmpresa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Colaborador>
     */
    public function getEmpColaboradores(): Collection
    {
        return $this->emp_colaboradores;
    }

    public function addEmpColaboradore(Colaborador $empColaboradore): static
    {
        if (!$this->emp_colaboradores->contains($empColaboradore)) {
            $this->emp_colaboradores->add($empColaboradore);
            $empColaboradore->setColEmpresa($this);
        }

        return $this;
    }

    public function removeEmpColaboradore(Colaborador $empColaboradore): static
    {
        if ($this->emp_colaboradores->removeElement($empColaboradore)) {
            // set the owning side to null (unless already changed)
            if ($empColaboradore->getColEmpresa() === $this) {
                $empColaboradore->setColEmpresa(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Area>
     */
    public function getEmpAreas(): Collection
    {
        return $this->emp_areas;
    }

    public function addEmpArea(Area $empArea): static
    {
        if (!$this->emp_areas->contains($empArea)) {
            $this->emp_areas->add($empArea);
            $empArea->setAraEmpresa($this);
        }

        return $this;
    }

    public function removeEmpArea(Area $empArea): static
    {
        if ($this->emp_areas->removeElement($empArea)) {
            // set the owning side to null (unless already changed)
            if ($empArea->getAraEmpresa() === $this) {
                $empArea->setAraEmpresa(null);
            }
        }

        return $this;
    }
}
