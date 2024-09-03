<?php

namespace App\Form\Empresa;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;

class CargadorArchivoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('archivo', FileType::class, [
                'label' => 'Subir Excel',
                'constraints' => [
                    new File([
                        'maxSize' => '20480k',
                        'mimeTypes' => [
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'text/csv',
                        ],
                        'mimeTypesMessage' => 'Por favor suba un archivo de datos correcto.',
                    ])
                ],
            ])
            ->add('terms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Acepto los términos y condiciones',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Debe aceptar los términos y condiciones para continuar.',
                    ]),
                ],
            ])
            ->add('cargar', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
