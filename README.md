# Caasi Groomer?

This is a small lightweight PHP system that let's you build custom websites easily. Our purpose while building this was to have full control of our website content. While this library is very small, it's also powerful - containing solutions to most stackoverflow problems that arise while building a small custom PHP website. The library works well with the Wordpress CMS too. We will create a template for this soon. NB: This system is still under development

## Setting Up

Create a custom php class first. Include the manuload.php file in your file and let your class extend from the Caasi\Groomer as show below. Refer to the examples folder.

```php

include __DIR__ . '/manuload.php';

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
```

You can write your own logic or custom functions like for the footer, header and menu etcetra.
The following functions are however required before these specific function

```php

public function getHeader()
{
    // must be included first especially for Wordpress
    parent::beforeHeader();
    return $this;
}
public function getMenu()
{
    // must be included first especially for Wordpress
    parent::beforeMenu();
    return $this;
}
public function getFooter(array $scripts = [])
{
    parent::beforeFooter($scripts);
    return $this;
}
```

Then instantiate it like this in all your php files that you want to render the site on

```php
// include you site configurations
include __DIR__."/Website.php";

$page = new Website();
$page
    ->setTitle('My Cool Website')
    ->getHead();
```
