<?php


namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserConfirmation
 * @package App\Entity
 * @ApiResource(
 *     collectionOperations={
 *          "post"={"path"="/users/confirm"}
 *     },
 *     itemOperations={}
 * )
 */
class UserConfirmation
{
    /**
     * @Assert\NotBlank()
     * @Assert\length(min=30, max=30)
     */
    public $confirmationToken;

}