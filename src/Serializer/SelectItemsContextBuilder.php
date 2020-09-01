<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\TopicProgrammingLanguage;
use App\Entity\Author;
use App\Entity\Level;
use App\Entity\TopicFramework;
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
        else if( TopicProgrammingLanguage::class === $resourceClass &&
        isset($context['groups']) &&
        $normalization === true) {
        $context['groups'][] = 'programLang:get-select-items';
        }
        else if( Author::class === $resourceClass &&
        isset($context['groups']) &&
        $normalization === true) {
        $context['groups'][] = 'auhtors:get-select-items';
        }
        else if( Level::class === $resourceClass &&
        isset($context['groups']) &&
        $normalization === true) {
        $context['groups'][] = 'levels:get-select-items';
        }

        return $context;
    }
}
