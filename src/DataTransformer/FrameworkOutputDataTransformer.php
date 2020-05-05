<?php


namespace App\DataTransformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\FrameworkOutput;
use App\Entity\Framework;

class FrameworkOutputDataTransformer implements DataTransformerInterface
{

    /**
     * Transforms the given object to something else, usually another object.
     * This must return the original object if no transformations have been done.
     *
     * @param $data
     * @param string $to
     * @param array $context
     * @return FrameworkOutput
     */
    public function transform($data, string $to, array $context = []): FrameworkOutput
    {
        $output = new FrameworkOutput();
        $output->frameworkName = $data->getName();
        $output->program = $data->getProgram();
        $output->docUrl = $data->getDocUrl();
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
        return FrameworkOutput::class === $to && $data instanceof Framework;
    }
}