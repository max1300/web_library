<?php

namespace App\Dto;

use App\Entity\TopicFramework;
use Symfony\Component\Serializer\Annotation\Groups;

final class ItemOutput {

  /**
     * @var integer
     * @Groups({"topicFram:get-select-items"})
     */
    public $value;

    /**
     * @var string
     * @Groups({"topicFram:get-select-items"})
     */
    public $label;

  
}

