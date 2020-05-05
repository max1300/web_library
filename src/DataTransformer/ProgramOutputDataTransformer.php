<?php


namespace App\DataTransformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\ProgramOutput;
use App\Entity\Program;

class ProgramOutputDataTransformer implements DataTransformerInterface
{

    /**
     * Transforms the given object to something else, usually another object.
     * This must return the original object if no transformations have been done.
     *
     * @param $data
     * @param string $to
     * @param array $context
     * @return ProgramOutput
     */
    public function transform($data, string $to, array $context = []): ProgramOutput
    {
        $output = new ProgramOutput();
        $output->programName = $data->getName();
        $output->frameworks = $data->getFrameworks();
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
        return ProgramOutput::class === $to && $data instanceof Program;
    }
}