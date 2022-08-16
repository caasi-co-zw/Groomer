<?php
spl_autoload_register(function ($class) {
    $class_names = explode('\\',$class);
    $singles = ['Groomer','Database'];
    if(in_array(end($class_names),$singles)):
        $class = str_replace(["Caasi\\",'\\'], ["Groomer/",'/'], $class);
    else:
        $class = str_replace(["Caasi\\",'\\'], ["/",'/'], $class);
    endif;
    if(file_exists(__DIR__ . "/$class.php")){
        require_once __DIR__ . "/$class.php";
    }
});
