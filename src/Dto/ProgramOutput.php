<?php

namespace App\Dto;

use App\Entity\Framework;
use Symfony\Component\Serializer\Annotation\Groups;

final class ProgramOutput
{

    /**
     * @var string
     * @Groups({"program:read", "program:write", "framework:read"})
     */
    public $programName;

    /**
     * @var Framework
     * @Groups({"program:read", "program:write"})
     */
    public $frameworks;

}