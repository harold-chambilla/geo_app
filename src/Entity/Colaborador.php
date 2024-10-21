<?php

namespace App\Entity;

use App\Repository\ColaboradorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ColaboradorRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_COL_NOMBREUSUARIO', fields: ['col_nombreusuario'])]
class Colaborador implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $col_nombreusuario = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * @var Collection<int, Asistencia>
     */
    #[ORM\OneToMany(targetEntity: Asistencia::class, mappedBy: 'colaborador')]
    private Collection $asistencias;

    /**
     * @var Collection<int, Permiso>
     */
    #[ORM\OneToMany(targetEntity: Permiso::class, mappedBy: 'colaborador')]
    private Collection $permisos;

    /**
     * @var Collection<int, HorarioTrabajo>
     */
    #[ORM\OneToMany(targetEntity: HorarioTrabajo::class, mappedBy: 'colaborador')]
    private Collection $horarioTrabajos;

    public function __construct()
    {
        $this->asistencias = new ArrayCollection();
        $this->permisos = new ArrayCollection();
        $this->horarioTrabajos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColNombreusuario(): ?string
    {
        return $this->col_nombreusuario;
    }

    public function setColNombreusuario(string $col_nombreusuario): static
    {
        $this->col_nombreusuario = $col_nombreusuario;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->col_nombreusuario;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Asistencia>
     */
    public function getAsistencias(): Collection
    {
        return $this->asistencias;
    }

    public function addAsistencia(Asistencia $asistencia): static
    {
        if (!$this->asistencias->contains($asistencia)) {
            $this->asistencias->add($asistencia);
            $asistencia->setColaborador($this);
        }

        return $this;
    }

    public function removeAsistencia(Asistencia $asistencia): static
    {
        if ($this->asistencias->removeElement($asistencia)) {
            // set the owning side to null (unless already changed)
            if ($asistencia->getColaborador() === $this) {
                $asistencia->setColaborador(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Permiso>
     */
    public function getPermisos(): Collection
    {
        return $this->permisos;
    }

    public function addPermiso(Permiso $permiso): static
    {
        if (!$this->permisos->contains($permiso)) {
            $this->permisos->add($permiso);
            $permiso->setColaborador($this);
        }

        return $this;
    }

    public function removePermiso(Permiso $permiso): static
    {
        if ($this->permisos->removeElement($permiso)) {
            // set the owning side to null (unless already changed)
            if ($permiso->getColaborador() === $this) {
                $permiso->setColaborador(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HorarioTrabajo>
     */
    public function getHorarioTrabajos(): Collection
    {
        return $this->horarioTrabajos;
    }

    public function addHorarioTrabajo(HorarioTrabajo $horarioTrabajo): static
    {
        if (!$this->horarioTrabajos->contains($horarioTrabajo)) {
            $this->horarioTrabajos->add($horarioTrabajo);
            $horarioTrabajo->setColaborador($this);
        }

        return $this;
    }

    public function removeHorarioTrabajo(HorarioTrabajo $horarioTrabajo): static
    {
        if ($this->horarioTrabajos->removeElement($horarioTrabajo)) {
            // set the owning side to null (unless already changed)
            if ($horarioTrabajo->getColaborador() === $this) {
                $horarioTrabajo->setColaborador(null);
            }
        }

        return $this;
    }
}
