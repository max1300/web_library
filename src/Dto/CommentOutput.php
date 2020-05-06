<?php

namespace App\Dto;

use App\Entity\Ressource;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

final class CommentOutput
{
    /**
     * @Groups("comment:read")
     */
    public $content;

    /**
     * @Groups("comment:read")
     */
    public $createdAt;

    /**
     * @var Ressource
     * @Groups("comment:read")
     */
    public $commentRessource;

    /**
     * @var User
     * @Groups("comment:read")
     */
    public $author;

}