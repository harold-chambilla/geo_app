<?php 
namespace App\Funciones\Empresa;

use App\Entity\Empresa;
use App\Repository\ColaboradorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class EmpresaFunciones
{
    public function __construct(
        private ColaboradorRepository $colaboradorRepository, 
        private Security $security,
        private EntityManagerInterface $entityManagerInterface
    ) {}

    public function obtenerEmpresa(): ?Empresa
    {
        $colaborador = $this->colaboradorRepository->findOneBy([
            'col_nombreusuario' => $this->security->getUser()->getUserIdentifier(),
        ]);

        return $colaborador ? $colaborador->getColEmpresa() : null;
    }
}
