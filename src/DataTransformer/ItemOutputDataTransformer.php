<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use App\Dto\ItemOutput;
use App\Entity\IItemOutputTransformable;

class ItemOutputDataTransformer implements DataTransformerInterface
{

    private $iriConverter;

    public function __construct(IriConverterInterface $iriConverter)
    {
        $this->iriConverter = $iriConverter;
    }

    public function transform($data, string $to, array $context = [])
    {
        if(!$data instanceof IItemOutputTransformable)
        {
            return null;
        }

        $output = new ItemOutput();
        $output->value = $this->iriConverter->getIriFromItem($data);
        $output->label = $data->getLabel();

        return $output;
    }

    //Permet de récupérer les données de TopicFramework et les retourne transformer dans la classe ItemOutput
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ItemOutput::class === $to && $data instanceof IItemOutputTransformable;
    }
}