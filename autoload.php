<?php
spl_autoload_register(function ($class) {
    if(strpos($class,'Caasi') === false) return;
    $class = str_replace("Caasi\\", "", $class);
    if(file_exists(__DIR__ . "/Groomer/$class.php")){
        require_once __DIR__ . "/Groomer/$class.php";
    }
});
