<?php


namespace App\Dto;


use App\Entity\Author;
use App\Entity\Level;
use App\Entity\Topic;

class RessourceOutput
{

    public $name;

    public $url;

    /**
     * @var Author
     */
    public $author;

    /**
     * @var Level
     */
    public $level;

    public $language;

    /**
     * @var Topic
     */
    public $topic;
}