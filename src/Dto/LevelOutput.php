<?php

namespace App\Dto;

use App\Entity\Ressource;
use Symfony\Component\Serializer\Annotation\Groups;

final class LevelOutput
{

    /**
     * @var string
     * @Groups({"resource:read", "level:read", "level:write"})
     */
    public $levelName;

    /**
     * @var Ressource
     * @Groups("level:read")
     */
    public $levelRessources;
}