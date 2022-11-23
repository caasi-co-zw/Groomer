<?php

include __DIR__ . '/../manuload.php';

class Website extends Caasi\Groomer
{
    public $variable1;

    public function __construct($config = [], $cb = null)
    {
        // your code
        $this->addStyles(
            array(
                'src'=>'/css/style.min.css'
            )
        );
        $this->addStyles(
            array(
                'src'=>'/js/app.min.js'
            )
        );
        $this->setSeo(false);
        parent::__construct($config, $cb); // must be included last
        return $this;
    }
}