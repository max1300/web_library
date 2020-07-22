<?php


namespace App\DataTransformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\FrameworkOutput;
use App\Entity\Framework;

class FrameworkOutputDataTransformer implements DataTransformerInterface
{

    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

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
        $this->validator->validate($data);

        $output = new FrameworkOutput();
        $output->frameworkName = $data->getName();
        $output->program = $data->getProgram();
        $output->docUrl = $data->getDocUrl();
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
        return FrameworkOutput::class === $to && $data instanceof Framework;
    }
}