<?php

namespace Caasi\Groomer\Components;

/**
 * Basic structure of a meta file
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Meta {
    private $name;
    private $property;
    private $content;

    public function __construct($name,$content) {
        $this->name = $name;
        $this->content = $content;
        $this->property = 'name';
    }
    public function setProperty(bool $enable){
        $this->property = $enable ? 'property' : 'name';
    }
    public function __toString(){
        return sprintf('<meta %s="%s" content="%s">',$this->property,$this->name,$this->content);
    }
}
