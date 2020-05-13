<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;


final class UserOutput
{

    /**
     * @var string
     * @Groups({"user:get", "comment:read", "resource:read"})
     */
    public $login;

    /**
     * @var string
     * @Groups({"user:get"})
     */
    public $picture;

    /**
     * @var string
     * @Groups({"user:get-admin"})
     */
    public $email;


}