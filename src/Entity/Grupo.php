<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GrupoRepository::class)]
class Grupo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $grp_nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $grp_descripcion = null;

    #[ORM\Column(nullable: true)]
    private ?bool $grp_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'emp_grupos')]
    private ?Empresa $grp_empresa = null;

    #[ORM\OneToMany(targetEntity: ConfiguracionAsistencia::class, mappedBy: 'cas_grupo')]
    private Collection $grp_configuracionesAsistencia;

    #[ORM\OneToMany(targetEntity: Colaborador::class, mappedBy: 'col_grupo')]
    private Collection $grp_colaboradores;

    public function __construct()
    {
        $this->grp_configuracionesAsistencia = new ArrayCollection();
        $this->grp_colaboradores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrpNombre(): ?string
    {
        return $this->grp_nombre;
    }

    public function setGrpNombre(?string $grp_nombre): static
    {
        $this->grp_nombre = $grp_nombre;

        return $this;
    }

    public function getGrpDescripcion(): ?string
    {
        return $this->grp_descripcion;
    }

    public function setGrpDescripcion(?string $grp_descripcion): static
    {
        $this->grp_descripcion = $grp_descripcion;

        return $this;
    }

    public function isGrpEliminado(): ?bool
    {
        return $this->grp_eliminado;
    }

    public function setGrpEliminado(?bool $grp_eliminado): static
    {
        $this->grp_eliminado = $grp_eliminado;

        return $this;
    }

    public function getGrpEmpresa(): ?Empresa
    {
        return $this->grp_empresa;
    }

    public function setGrpEmpresa(?Empresa $grp_empresa): static
    {
        $this->grp_empresa = $grp_empresa;

        return $this;
    }

    /**
     * @return Collection<int, ConfiguracionAsistencia>
     */
    public function getGrpConfiguracionesAsistencia(): Collection
    {
        return $this->grp_configuracionesAsistencia;
    }

    public function addGrpConfiguracionesAsistencium(ConfiguracionAsistencia $grpConfiguracionesAsistencium): static
    {
        if (!$this->grp_configuracionesAsistencia->contains($grpConfiguracionesAsistencium)) {
            $this->grp_configuracionesAsistencia->add($grpConfiguracionesAsistencium);
            $grpConfiguracionesAsistencium->setCasGrupo($this);
        }

        return $this;
    }

    public function removeGrpConfiguracionesAsistencium(ConfiguracionAsistencia $grpConfiguracionesAsistencium): static
    {
        if ($this->grp_configuracionesAsistencia->removeElement($grpConfiguracionesAsistencium)) {
            // set the owning side to null (unless already changed)
            if ($grpConfiguracionesAsistencium->getCasGrupo() === $this) {
                $grpConfiguracionesAsistencium->setCasGrupo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Colaborador>
     */
    public function getGrpColaboradores(): Collection
    {
        return $this->grp_colaboradores;
    }

    public function addGrpColaboradore(Colaborador $grpColaboradore): static
    {
        if (!$this->grp_colaboradores->contains($grpColaboradore)) {
            $this->grp_colaboradores->add($grpColaboradore);
            $grpColaboradore->setColGrupo($this);
        }

        return $this;
    }

    public function removeGrpColaboradore(Colaborador $grpColaboradore): static
    {
        if ($this->grp_colaboradores->removeElement($grpColaboradore)) {
            // set the owning side to null (unless already changed)
            if ($grpColaboradore->getColGrupo() === $this) {
                $grpColaboradore->setColGrupo(null);
            }
        }

        return $this;
    }
}
