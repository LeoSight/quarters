<?php

namespace App\Service;

use App\Entity\Loner;
use App\Entity\Soldier;
use App\Entity\User;
use App\Enum\SoldierRoles;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Faker;

class LonerService {

    private ObjectManager $manager;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ManagerRegistry $doctrine,
        private readonly UtilsService $utilsService
    ) {
        $this->manager = $this->doctrine->getManager();
    }

    public function findLonerByChance(int $x, int $y): void
    {
        $users = $this->userRepository->findBy([ 'x' => $x, 'y' => $y ]);
        if(count($users) <= 1 && rand(1,20) == 1){
            $this->generateLoner($x, $y);
        }
    }

    public function generateLoner(int $x, int $y): Loner
    {
        $locales = ['cs_CZ','en_US','en_GB','pl_PL','sk_SK','de_DE'];
        $faker = Faker\Factory::create($locales[array_rand($locales)]);
        $gender = rand(1,3) == 1 ? 'female' : 'male';

        $loner = new Loner();
        $loner->setName($faker->firstName($gender) . ' ' . $faker->lastName($gender));
        $loner->setX($x);
        $loner->setY($y);

        $loner->setRole(
            SoldierRoles::from(
                $this->utilsService->getRandomWeightedElement([
                    SoldierRoles::CITIZEN->value => 5,
                    SoldierRoles::RIFLEMAN->value => 2,
                    SoldierRoles::MEDIC->value => 1,
                ])
            )
        );

        $loner->setHealth(100);
        $loner->setMorale(rand(70, 100));

        $loner->setExperience(0);
        if($loner->getRole() != SoldierRoles::CITIZEN){
            $loner->setExperience(rand(0,70));
        }

        $loner->setWeapon(0);

        $this->manager->persist($loner);
        $this->manager->flush();

        return $loner;
    }

    public function soldierDataTransfer(Loner|Soldier $from, Loner|Soldier $to): void
    {
        $to->setName($from->getName());
        $to->setRole($from->getRole());
        $to->setHealth($from->getHealth());
        $to->setExperience($from->getExperience());
        $to->setWeapon($from->getWeapon());
        $to->setInjuries($from->getInjuries());
        $to->setMorale($from->getMorale());
    }

    public function assignLonerToUser(Loner $loner, User $user): void
    {
        $soldier = new Soldier();
        $soldier->setUser($user);
        $this->soldierDataTransfer($loner, $soldier);

        $this->manager->persist($soldier);
        $this->manager->remove($loner);
        $this->manager->flush();
    }

    public function freeSoldierFromUser(Soldier $soldier, User $user): void
    {
        $loner = new Loner();
        $loner->setX($user->getX());
        $loner->setY($user->getY());
        $this->soldierDataTransfer($soldier, $loner);
        $loner->setMorale(max(0, $loner->getMorale() - 10));

        $this->manager->persist($loner);
        $this->manager->remove($soldier);
        $this->manager->flush();
    }

}