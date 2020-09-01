<?php

namespace App\Controller;

use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// Recoit le token transmis par l'utilisateur quand celui ci clique sur le lien dans l'email qu'on a envoyé et active le compte de l'utilisateur puis redirige sur "home"
class AccountActivatorController extends AbstractController
{
    /**
     * @Route("/", name="accountActivator_index")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/confirm-user/{token}", name="accountActivator_confirm_token")
     * @param string $token
     * @param UserConfirmationService $confirmationService
     * @return Response
     */
    public function confirmUser(string $token, UserConfirmationService $confirmationService)
    {
        $confirmationService->confirmUser($token);
        return new Response('ok compte activé');
    }
}
