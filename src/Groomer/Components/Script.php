<?php

namespace Caasi\Groomer\Components;

/**
 * Basic structure of a script
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Script extends BaseComponent
{
    const TYPE = 'type';
    const TYPE_MODULE = 'type="module"';
    const TYPE_JAVASCRIPT = 'type="text/javascript"';
    private $elementName = 'script';
}
