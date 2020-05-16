<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterSubscriber implements EventSubscriberInterface
{
    private $encoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * RegisterSubscriber constructor.
     * @param $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder, TokenGenerator $tokenGenerator)
    {
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
        ];
    }

    public function userRegistered(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if(!$user instanceof User || $method !== Request::METHOD_POST) {
            return;
        }

        $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()));

        $user->setConfirmationToken($this->tokenGenerator->getRandomToken());
    }
}