<?php

namespace App\Dto;

use App\Entity\Ressource;
use Symfony\Component\Serializer\Annotation\Groups;

final class AuthorOutput {

    /**
     * @var string
     * @Groups({"author:read"})
     */
    public $authorName;

    /**
     * @var string
     * @Groups({"author:read"})
     */
    public $authorWebsite;

    /**
     * @var Ressource
     * @Groups("author:read")
     */
    public $authorRessources;


}