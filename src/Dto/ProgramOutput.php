<?php

namespace App\Dto;

use App\Entity\Framework;
use App\Entity\Topic;
use Symfony\Component\Serializer\Annotation\Groups;

final class ProgramOutput
{

    /**
     * @var string
     * @Groups({"resource:read", "program:read", "framework:read"})
     */
    public $programName;

    /**
     * @var Framework
     * @Groups({"resource:read", "program:read"})
     */
    public $frameworks;

    /**
     * @var Topic
     * @Groups({"program:read"})
     */
    public $topic;

}