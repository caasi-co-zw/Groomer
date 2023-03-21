<?php

namespace Components;

/**
 * Basic font structure
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Fonts
{
    private $name;

    const WEIGHT_NORMAL = 400;
    const WEIGHT_BOLD = 600;

    public function __construct()
    {
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }
}
