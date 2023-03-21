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
    const FACEBOOK_LOCALE = 'og:type';
    const FACEBOOK_URL = 'og:type';
    const FACEBOOK_DESCRIPTION = 'og:description';
    protected $elementName = 'meta';
    protected $closeTag = false;
}
