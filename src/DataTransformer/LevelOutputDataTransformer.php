<?php


namespace App\DataTransformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\LevelOutput;
use App\Entity\Level;

class LevelOutputDataTransformer implements DataTransformerInterface
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
     * @return void
     */
    public function transform($data, string $to, array $context = []): LevelOutput
    {
        $this->validator->validate($data);

        $levelOutput = new LevelOutput();
        $levelOutput->levelName = $data->getName();
        $levelOutput->levelRessources = $data->getRessources();

        return $levelOutput;
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
        return LevelOutput::class === $to && $data instanceof Level;
    }
}