<?php

namespace App\Dto;

use App\Entity\Comment;
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
     * @var Comment
     * @Groups({"user:get"})
     */
    public $userComments;


}