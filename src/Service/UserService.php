<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService {

    /** @var User $user */
    public UserInterface $user;
    private ObjectManager $manager;

    function __construct(
        UserInterface $userInterface,
        private readonly ManagerRegistry $doctrine,
    ) {
        $this->manager = $this->doctrine->getManager();
        $this->user = $userInterface;
        $this->user->setLastSeen(new \DateTime('now'));
    }

    function __destruct(){
        if ($this->manager->isOpen()) {
            $this->manager->persist($this->user);
            $this->manager->flush();
        }
    }

}