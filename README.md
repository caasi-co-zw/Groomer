# Caasi Groomer?

This is a small lightweight PHP system that let's you build custom websites easily. Our purpose while building this was to have full control of our website content. While this library is very small, it's also powerful - containing solutions to most stackoverflow problems that arise while building a small custom PHP website. The library works well with the Wordpress CMS too. We will create a template for this soon. NB: This system is still under development

## Setting Up

All you need to do is create a custom class file that extends from the Groomer class. This is where you can prefeed some of your system configurations such as stylesheets and javascripts file.

```php

namespace Caasi\Groomer;

include __DIR__ . '/autoload.php';

class Website extends Groomer
{
    public $variable1;

    public function __construct($config = [], $cb = null)
    {
        // your code
        $this->setStyles(
            array(
                '/css/style.min.css'
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
    parent::beforeGetHeader(); // must be included first
    return $this;
}
public function getMenu()
{
    parent::beforeGetMenu(); // must be included first
    return $this;
}
public function getFooter(array $scripts = [])
{
    parent::beforeGetFooter($scripts);
    return $this;
}
```

Then instantiate it like this in all your php files that you want to render the site on

```php
use Caasi\Groomer;

// include or require the class
include __DIR__."/Website.php";

$page = new Website();
$page
    ->setTitle('My Cool Website')
    ->getHead();
```

## Functions

Allow me to tell you more about our super functions ;)


## addStyles() or  setStyles()

```php
$page->addStyles('/css/style.css');
```

Now this function accepts either an array or a url. A url is useful when building your site without a CMS like WordPress. In this case you can enter only the url starting with either a single forwad slash or doubled for external files.
