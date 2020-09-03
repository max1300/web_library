<?php

namespace App\Dto;


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

