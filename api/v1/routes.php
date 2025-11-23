<?php
$routes = [

    '^/api/v1/cars/get$' => 'cars/get.php',
    '^/api/v1/cars/create$' => 'cars/create.php',
    '^/api/v1/cars/createBunch$' => 'cars/createBunch.php',
    '^/api/v1/cars/update/(\d+)$' => 'cars/update.php',
    '^/api/v1/cars/delete/(\d+)$' => 'cars/delete.php',


    '^/api/v1/testDrives/create$' => 'testDrives/create.php',
];
