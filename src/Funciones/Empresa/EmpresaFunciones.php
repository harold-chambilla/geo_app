<?php 
namespace App\Funciones\Empresa;

use App\Entity\Empresa;
use App\Repository\ColaboradorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Func;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;

class EmpresaFunciones
{
	public function __construct(
		private ColaboradorRepository $colaboradorRepository, 
		private Security $security,
		private EntityManagerInterface $entityManagerInterface
	){}

	public function obtenerEmpresa(): ?Empresa
	{
		$empresa = null;
		$colaborador = $this->colaboradorRepository->findOneBy([
			'col_nombreusuario' => $this->security->getUser()->getUserIdentifier(),
		]);

		if($colaborador){
			$empresa = $colaborador->getColEmpresa();	
		}

		return $empresa;
	}

	public function actualizarRUC(Empresa $entity, string $nuevoValor): void
	{
		# Actualizar el ruc en empresa
		$entity->setEmpRuc($nuevoValor);
		$this->entityManagerInterface->persist($entity);

		# Actualizar el nombre de usuario
		$usuarios = $this->colaboradorRepository->findBy([
			'col_empresa' => $entity
		]);
		foreach($usuarios as $usuario){
			$nuevo_usuario = explode(' ', $usuario->getColNombres())[0] . '.' . explode(' ', $usuario->getColApellidos())[0];
			$nuevo_usuario = strtolower($nuevo_usuario);
			$usuario->setColNombreusuario($nuevoValor . '|' . $nuevo_usuario);
		}
		$this->entityManagerInterface->flush();
	}

	public function actualizarRazonSocial(Empresa $entity, string $nuevoValor): void
	{
		$entity->setEmpNombreempresa('$nuevoValor');
		$this->entityManagerInterface->persist($entity);
		$this->entityManagerInterface->flush();
	}
}

