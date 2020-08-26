<?php


namespace App\Dto;


use App\Entity\Author;
use App\Entity\Level;
use App\Entity\Program;
use App\Entity\Topic;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

final class RessourceOutput
{

    /**
     * @var string
     * @Groups({"program:read", "resource:read", "level:read", "framework:read"})
     */
    public $resourceName;

    /**
     * @var string
     * @Groups({"resource:read", "program:read", "framework:read"})
     */
    public $url;

    /**
     * @var Author
     * @Groups({"resource:read", "level:read", "program:read", "framework:read"})
     */
    public $author;

    /**
     * @var Level
     * @Groups({"resource:read", "program:read", "framework:read"})
     */
    public $level;

    /**
     * @var string
     * @Groups({"resource:read", "level:read", "program:read", "framework:read"})
     */
    public $language;

    /**
     * @var Topic
     * @Groups({"resource:read", "level:read", "program:read", "framework:read"})
     */
    public $topic;

    /**
     * @var User
     * @Groups({"resource:read", "program:read", "framework:read"})
     */
    public $publisher;


}