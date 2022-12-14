<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService {

    public function entity(UserInterface|null $user): User
    {
        if (!$user instanceof User) {
            throw new \RuntimeException(sprintf('Expected App\\Entity\\User, got %s', $user === null ? 'null' : get_class($user)));
        }

        return $user;
    }

}