<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService {

    public User $user;
    private ObjectManager $manager;

    function __construct(?UserInterface $user, ManagerRegistry $doctrine){
        /** @var User $user */
        $this->user = $user;
        $this->manager = $doctrine->getManager();

        $this->user->setLastSeen(new \DateTime('now'));
    }

    function __destruct(){
        $this->manager->persist($this->user);
        $this->manager->flush();
    }

}