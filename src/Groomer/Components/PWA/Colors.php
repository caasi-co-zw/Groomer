<?php

namespace Caasi\Groomer\Components\PWA;

/**
 * Basic font structure
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Colors {
    private $lightColor;
    private $darkColor;
    
    public function setColor(string $color) {
        $this->color = $color;
    }
}
