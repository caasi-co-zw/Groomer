<?php

namespace Groomer\Components;

/**
 * Basic structure of a meta file
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Meta extends BaseComponent
{
    const CONTENT = 'content';
    const PROPERTY = 'property';
    const HTTP_EQUIV = 'http-equiv';
    const FACEBOOK_TITLE = 'og:title';
    const FACEBOOK_TYPE = 'og:type';
    const FACEBOOK_LOCALE = 'og:local';
    const FACEBOOK_URL = 'og:url';
    const FACEBOOK_IMAGE = 'og:image';
    const FACEBOOK_SITENAME = 'og:site_name';
    const FACEBOOK_IMAGE_ALT = 'og:image_alt';
    const FACEBOOK_DESCRIPTION = 'og:description';
    const PREFER_COLOR = 'name="prefer-color-scheme"';
    const PREFER_COLOR_DARK = 'dark';
    const PREFER_COLOR_LIGHT = 'light';
    const PREFER_COLOR_LIGHT_THEME = 'name="prefer-color-scheme" content="light"';
    const PREFER_COLOR_DARK_THEME = 'name="prefer-color-scheme" content="dark"';
    protected $closeTag = false;
}
