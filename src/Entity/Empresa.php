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

    #[ORM\Column(length: 255)]
    private ?string $emp_ruc = null;

    #[ORM\Column(length: 255)]
    private ?string $emp_nombreempresa = null;

    #[ORM\Column(length: 255)]
    private ?string $emp_industria = null;

    #[ORM\Column(length: 255)]
    private ?string $emp_telefono = null;

    #[ORM\Column]
    private ?int $emp_cantidadempleados = null;

    #[ORM\Column]
    private ?bool $emp_eliminado = null;

    /**
     * @var Collection<int, Grupo>
     */
    #[ORM\OneToMany(targetEntity: Grupo::class, mappedBy: 'empresa')]
    private Collection $grupos;

    public function __construct()
    {
        $this->grupos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmpRuc(): ?string
    {
        return $this->emp_ruc;
    }

    public function setEmpRuc(string $emp_ruc): static
    {
        $this->emp_ruc = $emp_ruc;

        return $this;
    }

    public function getEmpNombreempresa(): ?string
    {
        return $this->emp_nombreempresa;
    }

    public function setEmpNombreempresa(string $emp_nombreempresa): static
    {
        $this->emp_nombreempresa = $emp_nombreempresa;

        return $this;
    }

    public function getEmpIndustria(): ?string
    {
        return $this->emp_industria;
    }

    public function setEmpIndustria(string $emp_industria): static
    {
        $this->emp_industria = $emp_industria;

        return $this;
    }

    public function getEmpTelefono(): ?string
    {
        return $this->emp_telefono;
    }

    public function setEmpTelefono(string $emp_telefono): static
    {
        $this->emp_telefono = $emp_telefono;

        return $this;
    }

    public function getEmpCantidadempleados(): ?int
    {
        return $this->emp_cantidadempleados;
    }

    public function setEmpCantidadempleados(int $emp_cantidadempleados): static
    {
        $this->emp_cantidadempleados = $emp_cantidadempleados;

        return $this;
    }

    public function isEmpEliminado(): ?bool
    {
        return $this->emp_eliminado;
    }

    public function setEmpEliminado(bool $emp_eliminado): static
    {
        $this->emp_eliminado = $emp_eliminado;

        return $this;
    }

    /**
     * @return Collection<int, Grupo>
     */
    public function getGrupos(): Collection
    {
        return $this->grupos;
    }

    public function addGrupo(Grupo $grupo): static
    {
        if (!$this->grupos->contains($grupo)) {
            $this->grupos->add($grupo);
            $grupo->setEmpresa($this);
        }

        return $this;
    }

    public function removeGrupo(Grupo $grupo): static
    {
        if ($this->grupos->removeElement($grupo)) {
            // set the owning side to null (unless already changed)
            if ($grupo->getEmpresa() === $this) {
                $grupo->setEmpresa(null);
            }
        }

        return $this;
    }
}
