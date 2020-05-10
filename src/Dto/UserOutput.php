<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;


final class UserOutput
{

    /**
     * @var string
     * @Groups({"user:read", "comment:read"})
     */
    public $login;

    /**
     * @var string
     * @Groups({"user:read"})
     */
    public $picture;

}