<?php

namespace App\DataFixtures;

use App\Entity\Colaborador;
use App\Entity\ConfiguracionAsistencia;
use App\Entity\Empresa;
use App\Entity\Grupo;
use App\Entity\HorarioTrabajo;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $emp = new Empresa();
        $emp->setEmpRuc('34352');
        $emp->setEmpNombreempresa('Ripley');
        $manager->persist($emp);

        $col = new Colaborador();
        $col->setColNombres('easy');
        $col->setColApellidos('control');
        $col->setColDninit('79955451');
        $col->setRoles(["ROLE_ADMIN"]);
        $col->setColNombreusuario('admin');

        $hashPass = $this->userPasswordHasher->hashPassword($col, 'admin');
        $col->setPassword($hashPass);
        $col->setColEmpresa($emp);
        $manager->persist($col);

        $confAsis = new ConfiguracionAsistencia();
        $confAsis->setCasModalidad('REMOTO');
        $confAsis->setCasEmpresa($emp);
        $manager->persist($confAsis);
        
        $horaTra = new HorarioTrabajo();
        $horaTra->setHotHoraentrada(new DateTime('8:30:00'));
        $horaTra->setHotHorasalida(new DateTime('9:30:00'));
        $horaTra->setHotColaborador($col);
        $manager->persist($horaTra);

        // Para los nuevas empresas solo se requiere que configuren la empresa y el usuario admin principal.

        // Empresa (Los tumba-server)
        $empresa = new Empresa();
        $empresa->setEmpRuc('20503644968');
        $empresa->setEmpIndustria('Consultoria y fabrica de software.');
        $empresa->setEmpNombreempresa('Los tumba-server');
        $empresa->setEmpTelefono('978941234');
        $empresa->setEmpCantidadempleados(31);
        $empresa->setEmpEliminado(0);
        $manager->persist($empresa);

        $grupopredeterminado = new Grupo();
        $grupopredeterminado->setGrpNombre('Predeterminado');
        $grupopredeterminado->setGrpEmpresa($empresa);
        $grupopredeterminado->setGrpDescripcion('Grupo por defecto.');
        $grupopredeterminado->setGrpEliminado(0);
        $manager->persist($grupopredeterminado);

        $superadmin = new Colaborador();
        $superadmin->setColNombres('Juan');
        $superadmin->setColApellidos('Ornelas');
        $superadmin->setColDninit('78451236');
        $superadmin->setColFechanacimiento(DateTime::createFromFormat('d/m/Y', '09/12/1978'));
        $superadmin->setColPuesto('General manager');
        $superadmin->setColArea('Management');
        $superadmin->setColCorreoelectronico('juan.ornelas@tumbaserver.com');
        $superadmin->setRoles(["ROLE_SUPERADMIN"]);
        $superadmin->setColNombreusuario('juan.ornelas');
        $superadmin->setPassword($this->userPasswordHasher->hashPassword($col, '6d589f20'));
        $superadmin->setColEmpresa($empresa);
        $superadmin->setColGrupo($grupopredeterminado);
        $superadmin->setColEliminado(0);
        $manager->persist($superadmin);

        $manager->flush();
    }
}
