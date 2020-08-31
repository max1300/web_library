<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\ItemOutput;
use App\Entity\TopicFramework;

class ItemOutputDataTransformer implements DataTransformerInterface
{
  private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function transform($data, string $to, array $context = [])
    {
        $this->validator->validate($data);

        $output = new ItemOutput();
        $output->value = $data->getId();
        $output->label = $data->getFramework()->getName();
        return $output;
    }

    //Permet de récupérer les données de TopicFramework et les retourne transformer dans la classe ItemOutput
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ItemOutput::class === $to && $data instanceof TopicFramework;
    }
}