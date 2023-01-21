<?php

namespace Caasi\Groomer\Cms;

if (!defined('WP_SHORTEN_ASSETS_URL')) {
    define('WP_SHORTEN_ASSETS_URL', false);
}

class WordPress {

    public function addStyles(){}
    public function shortenAssetsUrl(){
        return WP_SHORTEN_ASSETS_URL;
    }
}
