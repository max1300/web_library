<?php

namespace App\Dto;

use App\Entity\Program;
use App\Entity\Topic;
use Symfony\Component\Serializer\Annotation\Groups;

final class FrameworkOutput
{

    /**
     * @var string
     * @Groups({"resource:read", "author:read", "level:read", "framework:write", "program:read", "framework:read"})
     */
    public $frameworkName;

    /**
     * @var Program
     * @Groups({"author:read", "level:read", "framework:read"})
     */
    public $program;

    /**
     * @var string
     * @Groups({"resource:read", "author:read", "framework:write", "framework:read"})
     */
    public $docUrl;

    /**
     * @var Topic
     * @Groups({"resource:read"})
     */
    public $topic;

}