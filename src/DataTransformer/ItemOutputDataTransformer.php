<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use App\Dto\ItemOutput;
use App\Entity\TopicFramework;

class ItemOutputDataTransformer implements DataTransformerInterface
{
  private $validator;

  private $iriConverter;

    public function __construct(ValidatorInterface $validator, IriConverterInterface $iriConverter)
    {
        $this->validator = $validator;
        $this->iriConverter = $iriConverter;
    }

    public function transform($data, string $to, array $context = [])
    {
        $this->validator->validate($data);

        $output = new ItemOutput();
        $output->value = $this->iriConverter->getIriFromItem($data);
        $output->label = $data->getFramework()->getName();
        return $output;
    }

    //Permet de récupérer les données de TopicFramework et les retourne transformer dans la classe ItemOutput
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ItemOutput::class === $to && $data instanceof TopicFramework;
    }
}