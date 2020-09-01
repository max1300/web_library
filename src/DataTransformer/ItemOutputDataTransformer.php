<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use ApiPlatform\Core\Api\IriConverterInterface;
use App\Dto\ItemOutput;
use App\Entity\TopicFramework;
use App\Entity\TopicProgrammingLanguage;
use App\Entity\Level;
use App\Entity\Author;

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

        if($data instanceof TopicFramework) 
        {
            $output->label = $data->getFramework()->getName();
        } 

        else if ($data instanceof TopicProgrammingLanguage)
        {
            $output->label = $data->getProgrammingLanguage()->getName();
        } 

        else if ($data instanceof Author)
        {
            $output->label = $data->getName();
        }

        else if ($data instanceof Level)
        {
            $output->label = $data->getName();

        }

        return $output;
    }

    //Permet de récupérer les données de TopicFramework et les retourne transformer dans la classe ItemOutput
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ItemOutput::class === $to && $data instanceof TopicFramework || $data instanceof TopicProgrammingLanguage || $data instanceof Author || $data instanceof Level;
    }
}