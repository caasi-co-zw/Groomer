<?php

namespace Caasi\Groomer\Components;

/**
 * Basic structure of a meta file
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Meta
{
    const NAME = 'name';
    const HREF = 'href';
    const CONTENT = 'content';
    const PROPERTY = 'property';
    const HTTP_EQUIV = 'http-equiv';
    private $results = '';
    private $elementName = 'meta';

    /**
     * Pass in a list of keys and their values.
     */
    public function __construct(...$values)
    {
        $keys = $strings = [];
        foreach ($values as $value) :
            if (is_string($value)) :
                $strings[] = sprintf(' %s', $value);
            elseif (is_array($value)) :
                $keys[] = sprintf(' %s="%s"', $value[0], $value[1]);
            endif;
        endforeach;
        sort($strings);
        $this->results = implode(' ', $keys) . implode(' ', $strings);
        print($this->__toString());
    }
    public function __toString()
    {
        return sprintf('<%s %s>', $this->elementName, trim($this->results));
    }
}
