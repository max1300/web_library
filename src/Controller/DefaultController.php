<?php

namespace App\Controller;

use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

//Recoit le token transmis par l'utilisateur quand celui ci clique sur le lien dans l'email qu'on a envoyé et confirm le compte de l'utilisateur puis redirige sur "home"
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default_index")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/confirm-user/{token}", name="default_confirm_token")
     * @param string $token
     * @param UserConfirmationService $confirmationService
     * @return RedirectResponse
     */
    public function confirmUser(string $token, UserConfirmationService $confirmationService)
    {
        $confirmationService->confirmUser($token);

        return $this->redirectToRoute('default_index');
    }
}
