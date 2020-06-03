<?php


namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTlistener
{
    private $requestStack;

    /**
     * JWTlistener constructor.
     * @param $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $successEvent)
    {
        $data = $successEvent->getData();
        $user = $successEvent->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $data['data'] = [
            'login' => $user->getLogin()
        ];

        $successEvent->setData($data);

    }


}