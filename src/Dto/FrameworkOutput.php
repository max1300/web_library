<?php

namespace App\Dto;

use App\Entity\Program;
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
     * @Groups({"resource:read", "author:read", "level:read", "framework:read"})
     */
    public $program;

    /**
     * @var string
     * @Groups({"resource:read", "author:read", "framework:write", "framework:read"})
     */
    public $docUrl;

}