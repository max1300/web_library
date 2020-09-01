<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SelectItemsContextBuilder implements SerializerContextBuilderInterface
{
  private $builder;

    /**
     * UserContextBuilder constructor.
     * @param SerializerContextBuilderInterface $builder
     */
    public function __construct(SerializerContextBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->builder->createFromRequest(
            $request, $normalization, $extractedAttributes
        );

        $resourceClass = $context['resource_class'] ?? null;

        if( TopicFramework::class === $resourceClass &&
            isset($context['groups']) &&
            $normalization === true) {
            $context['groups'][] = 'topicFram:get-select-items';
        }

        return $context;
    }
}
