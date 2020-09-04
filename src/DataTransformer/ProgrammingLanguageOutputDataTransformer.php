<?php


namespace App\DataTransformer;


use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\ProgramingLanguageOutput;
use App\Entity\TopicProgrammingLanguage;

class ProgrammingLanguageOutputDataTransformer implements DataTransformerInterface
{


    public function transform($data, string $to, array $context = [])
    {
        $output = new ProgramingLanguageOutput();
        $output->idTopicProgramming = $data->getId();
        $output->id = $data->getProgrammingLanguage()->getId();
        $output->programName = $data->getProgrammingLanguage()->getName();
        $output->frameworks = $data->getProgrammingLanguage()->getFrameworks();
        $output->ressources = $data->getRessources();
        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return ProgramingLanguageOutput::class === $to && $data instanceof TopicProgrammingLanguage;
    }
}