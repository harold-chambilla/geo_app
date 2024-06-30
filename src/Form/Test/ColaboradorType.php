<?php

namespace App\Form\Test;

use App\Entity\Area;
use App\Entity\Colaborador;
use App\Entity\Empresa;
use App\Entity\Grupo;
use App\Entity\Puesto;
use App\Repository\AreaRepository;
use App\Repository\PuestoRepository;
use Doctrine\ORM\Query\Expr\Func;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Config\Security\ProviderConfig\EntityConfig;

class ColaboradorType extends AbstractType
{
	public function __construct(
		private AreaRepository $areaRepository,
		private Security $security
	){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('col_nombres', TextType::class)
            ->add('col_apellidos', TextType::class)
            ->add('col_dninit', NumberType::class)
            ->add('col_fechanacimiento', BirthdayType::class)
			->add('col_area', EntityType::class, [
				'class' => Area::class, 
				'choice_label' => 'ara_nombre',
				'query_builder' => function (AreaRepository $areaRepository) use ($options){
					return $areaRepository->buscarAreasPorIdEmpresa($options['empresa_id']);
				},
				'placeholder' => 'Seleccione un área.',
			])
			->add('col_puesto', EntityType::class, [
				'class' => Puesto::class,
				'choice_label' => 'pst_nombre',
				'placeholder' => 'Seleccionar un puesto.'
			])
            ->add('col_correoelectronico')
			/*
			->add('roles', ChoiceType::class, [
				'choices' => [
					'Usuario' => "ROLE_USER",
					'Supervisor' => "ROLE_SUPERVIDOR",
					'Super-Supervisor' => "ROLE_SUPERSUPERVISOR",
					'Administrador' => "ROLE_ADMIN",
					'Super-Administrador' => "ROLE_SUPERADMIN",
				] 
			])
            ->add('col_nombreusuario')
            ->add('password')
			
			->add('col_eliminado')
            ->add('col_empresa', EntityType::class, [
                'class' => Empresa::class,
				'choice_label' => 'id',
            ])
            ->add('col_grupo', EntityType::class, [
                'class' => Grupo::class,
				'choice_label' => 'id',
            ])
            ->add('col_puesto', EntityType::class, [
                'class' => Puesto::class,
				'choice_label' => 'id',
			])
			*/
		;	
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
			'data_class' => Colaborador::class,
			'empresa_id' => null,
        ]);
    }
}
