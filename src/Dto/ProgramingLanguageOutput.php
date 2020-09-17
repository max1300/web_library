<?php


namespace App\Dto;


use App\Entity\Framework;
use App\Entity\Ressource;
use Symfony\Component\Serializer\Annotation\Groups;

class ProgramingLanguageOutput
{

    /**
     * @var int
     * @Groups({"programLang:read"})
     */
    public $idTopicProgramming;

    /**
     * @var int
     * @Groups({"user:get", "comment:read", "resource:read", "program:read", "framework:read", "programLang:read"})
     */
    public $id;

    /**
     * @var string
     * @Groups({"resource:read", "program:read", "framework:read", "programLang:read"})
     */
    public $programName;

    /**
     * @var Framework
     * @Groups({"program:read","programLang:read"})
     */
    public $frameworks;

    /**
     * @var Ressource
     * @Groups({"program:read","programLang:read", "framework:read"})
     */
    public $ressources;



}