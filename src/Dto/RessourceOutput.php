<?php


namespace App\Dto;


use App\Entity\Author;
use App\Entity\Level;
use App\Entity\Topic;
use Symfony\Component\Serializer\Annotation\Groups;

final class RessourceOutput
{

    /**
     * @var string
     * @Groups({"resource:read", "level:read", "comment:read"})
     */
    public $resourceName;

    /**
     * @var string
     * @Groups({"resource:read"})
     */
    public $url;

    /**
     * @var Author
     * @Groups({"resource:read", "level:read"})
     */
    public $author;

    /**
     * @var Level
     * @Groups({"resource:read"})
     */
    public $level;

    /**
     * @var string
     * @Groups({"resource:read", "level:read"})
     */
    public $language;

    /**
     * @var Topic
     * @Groups({"resource:read", "level:read"})
     */
    public $topic;
}