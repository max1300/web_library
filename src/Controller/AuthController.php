<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

class AuthController extends AbstractController
{
    /**
     * @Route("/api/login_check", name="login")
     * @return JsonResponse
     */
    public function login(): JsonResponse
    {
        $user = $this->getUser();
        return $this->json(array(
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles(),
        ));
    }
}
