<?php


namespace App\DataTransformer;


use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\UserOutput;
use App\Entity\User;

class UserOutputDataTransformer implements DataTransformerInterface
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
     * @return UserOutput
     */
    public function transform($data, string $to, array $context = []): UserOutput
    {
            $this->validator->validate($data);

            $output = new UserOutput();
            $output->login = $data->getLogin();
            $output->picture = $data->getProfilePic();
            $output->email = $data->getEmail();
            $output->role = $data->getRoles();
            $output->username = $data->getUsername();
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
        return UserOutput::class === $to && $data instanceof User;
    }
}