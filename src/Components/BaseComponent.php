<?php

namespace Groomer\Components;

/**
 * Basic structure of a base
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class BaseComponent
{
    /**
     * Element id
     */
    const ID = 'id';

    /**
     * Element href source
     */
    const HREF = 'href';

    /**
     * Element name
     */
    const NAME = 'name';

    /**
     * Element source
     */
    const SRC = 'src';
    const PRINT = "print";
    const DONT_PRINT = "do-no-print";
    /**
     * Adds a matching closing tag when enabled
     */
    protected $closeTag = true;
    private $print = false;
    private $results = '';
    private $elementName = null;
    private $value = null;

    /**
     * Pass in a list of keys and their values.
     */
    public function __construct(...$values)
    {
        // get class name
        $className = explode("\\", get_class($this));
        $this->elementName = strtolower(end($className));

        // prepare parameters
        $keys = $strings = [];
        foreach ($values as $value) :

            // do not process if value if empty
            if (!$value) continue;

            // if second value empty while array, 
            // convert the value to a string
            if (is_array($value) && count($value) === 1) $value = $value[0];

            // if custom functionality returns true, do not proceed
            if ($this->skipIf($value)) continue;

            if (is_string($value)) :
                if ($value == self::DONT_PRINT) :
                    $this->print = false;
                    continue;
                elseif ($value == self::PRINT) :
                    $this->print = true;
                    continue;
                endif;
                $strings[] = sprintf(' %s', $value);
            elseif (is_array($value)) :

                // key name is the same as element name
                // make it the element value
                if (strtolower($value[0]) == $this->elementName && !$this->value && $this->closeTag) {
                    $this->value .= $value[1];
                    continue;
                }
                $keys[] = sprintf(' %s="%s"', $value[0], $value[1]);
            endif;
        endforeach;
        
        sort($strings);
        $this->results = implode(' ', $keys) . implode(' ', $strings);
        if ($this->print) {
            print($this->__toString());
        }
    }
    protected function skipIf(&$value): bool
    {
        return false;
    }
    public function __toString()
    {
        if ($this->closeTag) {
            return sprintf('<%s%s>%s</%1$s>', $this->elementName, !$this->results ? null : ' ' . trim($this->results), $this->value);
        }
        return sprintf('<%s %s/>', $this->elementName, trim($this->results));
    }
}
