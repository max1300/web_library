<?php

namespace App\Controller;

use App\Mail\SymfonyMailer;
use App\Repository\UserRepository;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class LoginController extends AbstractController
{

    /**
     * @Route("/api/login_check", name="login")
     * @param Request $request
     * @param UserRepository $repository
     * @return JsonResponse
     */
    public function login(Request $request, UserRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $repository->findOneBy(['email' => $data["username"]]);
        if ($user === null){
            return $this->json('user not identified', 400);
        }
        return $this->json([
            'username'=>$user->getUsername(),
            'roles'=>$user->getRoles(),
            'login'=>$user->getLogin()
        ]);
    }

}
