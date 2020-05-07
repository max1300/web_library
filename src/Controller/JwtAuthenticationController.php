<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class JwtAuthenticationController extends AbstractController
{
    /**
     * @Route("/jwt/authentication", name="jwt_authentication")
     */
    public function login()
    {
        $user = $this->getUser();

        return $this->json(array(
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
        ));
    }
}
