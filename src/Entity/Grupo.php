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

    #[ORM\Column(length: 255)]
    private ?string $grp_nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $grp_descripcion = null;

    #[ORM\Column]
    private ?bool $grp_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'grupos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Empresa $empresa = null;

    /**
     * @var Collection<int, ConfiguracionAsistencia>
     */
    #[ORM\OneToMany(targetEntity: ConfiguracionAsistencia::class, mappedBy: 'grupo')]
    private Collection $configuracionAsistencias;

    /**
     * @var Collection<int, Colaborador>
     */
    #[ORM\OneToMany(targetEntity: Colaborador::class, mappedBy: 'grupo')]
    private Collection $colaboradors;

    public function __construct()
    {
        $this->configuracionAsistencias = new ArrayCollection();
        $this->colaboradors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrpNombre(): ?string
    {
        return $this->grp_nombre;
    }

    public function setGrpNombre(string $grp_nombre): static
    {
        $this->grp_nombre = $grp_nombre;

        return $this;
    }

    public function getGrpDescripcion(): ?string
    {
        return $this->grp_descripcion;
    }

    public function setGrpDescripcion(string $grp_descripcion): static
    {
        $this->grp_descripcion = $grp_descripcion;

        return $this;
    }

    public function isGrpEliminado(): ?bool
    {
        return $this->grp_eliminado;
    }

    public function setGrpEliminado(bool $grp_eliminado): static
    {
        $this->grp_eliminado = $grp_eliminado;

        return $this;
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): static
    {
        $this->empresa = $empresa;

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
            $configuracionAsistencia->setGrupo($this);
        }

        return $this;
    }

    public function removeConfiguracionAsistencia(ConfiguracionAsistencia $configuracionAsistencia): static
    {
        if ($this->configuracionAsistencias->removeElement($configuracionAsistencia)) {
            // set the owning side to null (unless already changed)
            if ($configuracionAsistencia->getGrupo() === $this) {
                $configuracionAsistencia->setGrupo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Colaborador>
     */
    public function getColaboradors(): Collection
    {
        return $this->colaboradors;
    }

    public function addColaborador(Colaborador $colaborador): static
    {
        if (!$this->colaboradors->contains($colaborador)) {
            $this->colaboradors->add($colaborador);
            $colaborador->setGrupo($this);
        }

        return $this;
    }

    public function removeColaborador(Colaborador $colaborador): static
    {
        if ($this->colaboradors->removeElement($colaborador)) {
            // set the owning side to null (unless already changed)
            if ($colaborador->getGrupo() === $this) {
                $colaborador->setGrupo(null);
            }
        }

        return $this;
    }
}
