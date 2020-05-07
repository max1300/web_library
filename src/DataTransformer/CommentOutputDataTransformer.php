<?php


namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\CommentOutput;
use App\Entity\Comment;

class CommentOutputDataTransformer implements DataTransformerInterface
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
    public function transform($data, string $to, array $context = []): CommentOutput
    {
        $this->validator->validate($data);

        $output = new CommentOutput();
        $output->content = $data->getContent();
        $output->createdAt = $data->getCreatedAt();
        $output->commentRessource = $data->getRessource();
        $output->author = $data->getUser();
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
        return CommentOutput::class === $to && $data instanceof Comment;
    }
}