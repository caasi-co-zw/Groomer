<?php

namespace Caasi\Groomer\Components;

/**
 * Overrides core functions depending on the CMS it is being used in.
 */
class Cms
{
    const WORDPRESS = 'WordPress';
    const CODEIGNITER = 'CodeIgniter';
    private $cms_name;
    public function __construct()
    {
        $this->_guessCms();
    }
    public function isCms()
    {
        return $this->cms_name !== null;
    }
    private function _guessCms()
    {
        if (class_exists('WP')) {
            return $this->cms_name = self::WORDPRESS;
        }
        if (class_exists('CodeIgniter')) {
            return $this->cms_name = self::CODEIGNITER;
        }
        return $this->cms_name = null;
    }
}
