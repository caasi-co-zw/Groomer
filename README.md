# Caasi Groomer?
This is a small lightweight PHP Class that let's you create custom websites easily. Our purpose while building this was to have full control of our website content. While this library is very small, it's also powerful - containing solutions to most stackoverflow problems that arise while building a small custom website.

# How to use it
All you need to do is include the ```Webpage.php``` file and initiate the class. You can customize this file to any class name of your choice, as long as it extends from Groomer.
```php
use Caasi\Groomer\;

// include or require the class
include __DIR__."/Webpage.php";

$page = new Webpage();
```