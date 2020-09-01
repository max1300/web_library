<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserContextBuilder implements SerializerContextBuilderInterface
{
    private $builder;

    private $checker;
    /**
     * UserContextBuilder constructor.
     * @param SerializerContextBuilderInterface $builder
     * @param AuthorizationCheckerInterface $checker
     */
    public function __construct(
        SerializerContextBuilderInterface $builder,
        AuthorizationCheckerInterface $checker)
    {
        $this->builder = $builder;
        $this->checker = $checker;
    }


    /**
     * Creates a serialization context from a Request.
     * @param Request $request
     * @param bool $normalization
     * @param array|null $extractedAttributes
     * @return array
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->builder->createFromRequest(
            $request, $normalization, $extractedAttributes
        );

        $resourceClass = $context['resource_class'] ?? null;

        if( User::class === $resourceClass &&
            isset($context['groups']) &&
            $normalization === true &&
            $this->checker->isGranted(User::ROLE_ADMIN)) {
            $context['groups'][] = 'user:get-admin';
        }

        return $context;
    }
}