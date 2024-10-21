<?php

namespace App\Entity;

use App\Repository\MotivoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotivoRepository::class)]
class Motivo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $mtv_nombre = null;

    #[ORM\Column]
    private ?bool $mtv_eliminado = null;

    #[ORM\ManyToOne(inversedBy: 'motivo')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Permiso $permiso = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMtvNombre(): ?string
    {
        return $this->mtv_nombre;
    }

    public function setMtvNombre(string $mtv_nombre): static
    {
        $this->mtv_nombre = $mtv_nombre;

        return $this;
    }

    public function isMtvEliminado(): ?bool
    {
        return $this->mtv_eliminado;
    }

    public function setMtvEliminado(bool $mtv_eliminado): static
    {
        $this->mtv_eliminado = $mtv_eliminado;

        return $this;
    }

    public function getPermiso(): ?Permiso
    {
        return $this->permiso;
    }

    public function setPermiso(?Permiso $permiso): static
    {
        $this->permiso = $permiso;

        return $this;
    }
}
