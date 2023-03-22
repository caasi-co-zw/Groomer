<?php

namespace Groomer\Components;

/**
 * Basic structure of a link
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Link extends BaseComponent
{
    const ID = 'id';
    const REL = 'rel';
    const TYPE = 'type';
    const NAME = 'name';
    const HREF = 'href';
    const CONTENT = 'content';
    const MANIFEST = 'rel="manifest"';
    const STYLESHEET = 'stylesheet';
    const PRELOAD_CSS = 'rel="preload" onload="this.onload=null;this.rel=\'stylesheet\'"';
    const CSS = 'text/css';
    protected $elementName = 'link';
    protected $closeTag = false;
}
