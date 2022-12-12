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
    ) {
        $this->manager = $this->doctrine->getManager();
    }

    public function findLonerByChance(int $x, int $y): void
    {
        $users = $this->userRepository->findBy([ 'x' => $x, 'y' => $y ]);
        if(count($users) <= 1 && rand(1,10) == 1){
            $this->generateLoner($x, $y);
        }
    }

    public function generateLoner(int $x, int $y): Loner
    {
        $locales = ['cs_CZ','en_US','en_GB','pl_PL','sk_SK','de_DE'];
        $faker = Faker\Factory::create($locales[array_rand($locales)]);

        $loner = new Loner();
        $loner->setName($faker->name());
        $loner->setX($x);
        $loner->setY($y);
        $loner->setRole(SoldierRoles::CITIZEN->value);
        $loner->setHealth(100);
        $loner->setExperience(0);
        $loner->setWeapon(0);

        $this->manager->persist($loner);
        $this->manager->flush();

        return $loner;
    }

    public function assignLonerToUser(Loner $loner, User $user): void
    {
        $soldier = new Soldier();
        $soldier->setUser($user);
        $soldier->setName($loner->getName());
        $soldier->setRole($loner->getRole());
        $soldier->setHealth($loner->getHealth());
        $soldier->setExperience($loner->getExperience());
        $soldier->setWeapon($loner->getWeapon());

        $this->manager->persist($soldier);
        $this->manager->remove($loner);
        $this->manager->flush();
    }

}