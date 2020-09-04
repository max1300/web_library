<?php

namespace App\Dto;

<<<<<<< HEAD

=======
>>>>>>> 262260df5541035aa4241a3592748383905ba3a0
use Symfony\Component\Serializer\Annotation\Groups;

final class ItemOutput {

  /**
     * @var integer
     * @Groups({
     * "topicFram:get-select-items",
     * "programLang:get-select-items",
     * "levels:get-select-items",
     * "authors:get-select-items"
     * })
     */
    public $value;

    /**
     * @var string
     * @Groups({
     * "topicFram:get-select-items",
     * "programLang:get-select-items",
     * "levels:get-select-items",
     * "authors:get-select-items"
     * })
     */
    public $label;

  
}

