<?php

use src\Blog\Article_Post;
use src\User\Person_Name;

spl_autoload_register(function ($class) {
    $fileName = str_replace('_', '\\', basename($class));
    $file = substr($class, 0, -strlen($fileName)) . $fileName;
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $file) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

$person = new Person_Name(
    1,
    'Luke',
    'Skywalker'
);

$post = new Article_Post(
    1,
    1,
    'Luke Skywalker',
    'Episode IV: A New Hope',
    'A long time ago in a galaxy far, far away....'
);

print_r($post);
