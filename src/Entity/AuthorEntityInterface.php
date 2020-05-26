<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface AuthorEntityInterface
{
    public function setUser(UserInterface $user): AuthorEntityInterface;
}