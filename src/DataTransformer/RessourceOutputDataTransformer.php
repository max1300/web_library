<?php


namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\RessourceOutput;
use App\Entity\Ressource;

class RessourceOutputDataTransformer implements DataTransformerInterface
{

    /**
     * Transforms the given object to something else, usually another object.
     * This must return the original object if no transformations have been done.
     *
     * @param $data
     * @param string $to
     * @param array $context
     * @return RessourceOutput
     */
    public function transform($data, string $to, array $context = []): RessourceOutput
    {
        $output = new RessourceOutput();
        $output->resourceName = $data->getName();
        $output->url = $data->getUrl();
        $output->author = $data->getAuthor();
        $output->level = $data->getLevel();
        $output->language = $data->getLanguage();
        $output->topic = $data->getTopic();
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
        return RessourceOutput::class === $to && $data instanceof Ressource;
    }
}