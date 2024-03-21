<?php

namespace App\DataFixtures;

use App\Entity\Colaborador;
use App\Entity\ConfiguracionAsistencia;
use App\Entity\Empresa;
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
        $col->setRoles(["ADMIN"]);
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

        $manager->flush();
    }
}
