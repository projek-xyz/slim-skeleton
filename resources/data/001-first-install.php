<?php

use Projek\Slim\Database\Blueprint;

return [
    'table' => 'dummy',
    'up' => [
        'id' => ['int' => 11, 'primary', 'null' => false, 'auto_increment'],
        'name' => ['varchar' => 100, 'unique', 'null' => false],
        'address' => ['text', 'null' => false],
        Blueprint::TIMESTAMPS,
        Blueprint::SOFTDELETES,
    ],
];
