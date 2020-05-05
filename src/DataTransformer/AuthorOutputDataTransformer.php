<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\AuthorOutput;
use App\Entity\Author;

class AuthorOutputDataTransformer implements DataTransformerInterface
{

    /**
     * Transforms the given object to something else, usually another object.
     * This must return the original object if no transformations have been done.
     *
     * @param $data
     * @param string $to
     * @param array $context
     * @return AuthorOutput
     */
    public function transform($data, string $to, array $context = []): AuthorOutput
    {
        $output = new AuthorOutput();
        $output->authorName = $data->getName();
        $output->authorWebsite = $data->getWebsite();
        $output->authorRessources = $data->getRessources();
        return $output;
    }

    /**
     * Checks whether the transformation is supported for a given data and context.
     *
     * @param object|array $data object on normalize / array on denormalize
     * @param string $to
     * @param array $context
     * @return bool
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return AuthorOutput::class === $to && $data instanceof Author;
    }
}