<?php

namespace App\Entity;

use App\Repository\ColaboradorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ColaboradorRepository::class)]
class Colaborador implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_nombres = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_apellidos = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_dninit = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $col_fechanacimiento = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_area = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_correoelectronico = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 180, unique: true)]
    private ?string $col_nombreusuario = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?bool $col_eliminado = null;

    #[ORM\OneToMany(targetEntity: Asistencia::class, mappedBy: 'asi_colaborador')]
    private Collection $col_asistencias;

    #[ORM\OneToMany(targetEntity: HorarioTrabajo::class, mappedBy: 'hot_colaborador')]
    private Collection $col_horariostrabajo;

    #[ORM\ManyToOne(inversedBy: 'emp_colaboradores')]
    private ?Empresa $col_empresa = null;

    #[ORM\ManyToOne(inversedBy: 'grp_colaboradores')]
    private ?Grupo $col_grupo = null;

    /**
     * @var Collection<int, Puesto>
     */
    #[ORM\OneToMany(targetEntity: Puesto::class, mappedBy: 'pst_colaborador')]
    private Collection $puestos;

    public function __construct()
    {
        $this->col_asistencias = new ArrayCollection();
        $this->col_horariostrabajo = new ArrayCollection();
        $this->puestos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColNombres(): ?string
    {
        return $this->col_nombres;
    }

    public function setColNombres(?string $col_nombres): static
    {
        $this->col_nombres = $col_nombres;

        return $this;
    }

    public function getColApellidos(): ?string
    {
        return $this->col_apellidos;
    }

    public function setColApellidos(?string $col_apellidos): static
    {
        $this->col_apellidos = $col_apellidos;

        return $this;
    }

    public function getColDninit(): ?string
    {
        return $this->col_dninit;
    }

    public function setColDninit(string $col_dninit): static
    {
        $this->col_dninit = $col_dninit;

        return $this;
    }

    public function getColFechanacimiento(): ?\DateTimeInterface
    {
        return $this->col_fechanacimiento;
    }

    public function setColFechanacimiento(?\DateTimeInterface $col_fechanacimiento): static
    {
        $this->col_fechanacimiento = $col_fechanacimiento;

        return $this;
    }

    public function getColArea(): ?string
    {
        return $this->col_area;
    }

    public function setColArea(?string $col_area): static
    {
        $this->col_area = $col_area;

        return $this;
    }

    public function getColCorreoelectronico(): ?string
    {
        return $this->col_correoelectronico;
    }

    public function setColCorreoelectronico(?string $col_correoelectronico): static
    {
        $this->col_correoelectronico = $col_correoelectronico;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getColNombreusuario(): ?string
    {
        return $this->col_nombreusuario;
    }

    public function setColNombreusuario(?string $col_nombreusuario): static
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
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

    public function isColEliminado(): ?bool
    {
        return $this->col_eliminado;
    }

    public function setColEliminado(?bool $col_eliminado): static
    {
        $this->col_eliminado = $col_eliminado;

        return $this;
    }

    /**
     * @return Collection<int, Asistencia>
     */
    public function getColAsistencias(): Collection
    {
        return $this->col_asistencias;
    }

    public function addColAsistencia(Asistencia $colAsistencia): static
    {
        if (!$this->col_asistencias->contains($colAsistencia)) {
            $this->col_asistencias->add($colAsistencia);
            $colAsistencia->setAsiColaborador($this);
        }

        return $this;
    }

    public function removeColAsistencia(Asistencia $colAsistencia): static
    {
        if ($this->col_asistencias->removeElement($colAsistencia)) {
            // set the owning side to null (unless already changed)
            if ($colAsistencia->getAsiColaborador() === $this) {
                $colAsistencia->setAsiColaborador(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HorarioTrabajo>
     */
    public function getColHorariostrabajo(): Collection
    {
        return $this->col_horariostrabajo;
    }

    public function addColHorariostrabajo(HorarioTrabajo $colHorariostrabajo): static
    {
        if (!$this->col_horariostrabajo->contains($colHorariostrabajo)) {
            $this->col_horariostrabajo->add($colHorariostrabajo);
            $colHorariostrabajo->setHotColaborador($this);
        }

        return $this;
    }

    public function removeColHorariostrabajo(HorarioTrabajo $colHorariostrabajo): static
    {
        if ($this->col_horariostrabajo->removeElement($colHorariostrabajo)) {
            // set the owning side to null (unless already changed)
            if ($colHorariostrabajo->getHotColaborador() === $this) {
                $colHorariostrabajo->setHotColaborador(null);
            }
        }

        return $this;
    }

    public function getColEmpresa(): ?Empresa
    {
        return $this->col_empresa;
    }

    public function setColEmpresa(?Empresa $col_empresa): static
    {
        $this->col_empresa = $col_empresa;

        return $this;
    }

    public function getColGrupo(): ?Grupo
    {
        return $this->col_grupo;
    }

    public function setColGrupo(?Grupo $col_grupo): static
    {
        $this->col_grupo = $col_grupo;

        return $this;
    }

    /**
     * @return Collection<int, Puesto>
     */
    public function getPuestos(): Collection
    {
        return $this->puestos;
    }

    public function addPuesto(Puesto $puesto): static
    {
        if (!$this->puestos->contains($puesto)) {
            $this->puestos->add($puesto);
            $puesto->setPstColaborador($this);
        }

        return $this;
    }

    public function removePuesto(Puesto $puesto): static
    {
        if ($this->puestos->removeElement($puesto)) {
            // set the owning side to null (unless already changed)
            if ($puesto->getPstColaborador() === $this) {
                $puesto->setPstColaborador(null);
            }
        }

        return $this;
    }
}
