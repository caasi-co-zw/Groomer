<?php

namespace Caasi\Groomer\Components;

/**
 * Basic structure of a meta file
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Meta extends Component
{
    const CONTENT = 'content';
    const PROPERTY = 'property';
    const HTTP_EQUIV = 'http-equiv';
    protected $elementName = 'meta';
    protected $closeTag = false;
}
