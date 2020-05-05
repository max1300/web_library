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
     * @return object
     */
    public function transform($data, string $to, array $context = []): RessourceOutput
    {
        $output = new RessourceOutput();
        $output->name = $data->name;
        $output->url = $data->url;
        $output->author = $data->author;
        $output->level = $data->level;
        $output->language = $data->language;
        $output->topic = $data->topic;
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