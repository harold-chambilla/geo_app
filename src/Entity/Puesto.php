<?php

namespace App\Entity;

use App\Repository\PuestoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuestoRepository::class)]
class Puesto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $pst_nombre = null;

    #[ORM\Column]
    private ?bool $pst_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'ara_puestos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Area $pst_area = null;

    #[ORM\OneToMany(targetEntity: Colaborador::class, mappedBy: 'col_puesto')]
    private Collection $pst_colaboradores;

    public function __construct()
    {
        $this->pst_colaboradores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPstNombre(): ?string
    {
        return $this->pst_nombre;
    }

    public function setPstNombre(string $pst_nombre): static
    {
        $this->pst_nombre = $pst_nombre;

        return $this;
    }

    public function isPstEliminado(): ?bool
    {
        return $this->pst_eliminado;
    }

    public function setPstEliminado(bool $pst_eliminado): static
    {
        $this->pst_eliminado = $pst_eliminado;

        return $this;
    }

    public function getPstArea(): ?Area
    {
        return $this->pst_area;
    }

    public function setPstArea(?Area $pst_area): static
    {
        $this->pst_area = $pst_area;

        return $this;
    }

    /**
     * @return Collection<int, Colaborador>
     */
    public function getPstColaboradores(): Collection
    {
        return $this->pst_colaboradores;
    }

    public function addPstColaboradore(Colaborador $pstColaboradore): static
    {
        if (!$this->pst_colaboradores->contains($pstColaboradore)) {
            $this->pst_colaboradores->add($pstColaboradore);
            $pstColaboradore->setColPuesto($this);
        }

        return $this;
    }

    public function removePstColaboradore(Colaborador $pstColaboradore): static
    {
        if ($this->pst_colaboradores->removeElement($pstColaboradore)) {
            // set the owning side to null (unless already changed)
            if ($pstColaboradore->getColPuesto() === $this) {
                $pstColaboradore->setColPuesto(null);
            }
        }

        return $this;
    }
}
