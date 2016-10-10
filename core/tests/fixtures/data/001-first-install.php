<?php

use Projek\Slim\Database\Blueprint;

return [
    'table' => 'dummy',
    'up' => function (Blueprint $schema) {
        $schema->create([
            'id' => ['int' => 11, 'primary', 'null' => false, 'auto_increment'],
            'name' => ['varchar' => 100, 'null' => false],
            'address' => ['text', 'null' => false],
            Blueprint::TIMESTAMPS,
            Blueprint::SOFTDELETES,
        ]);
    },
    'down' => function (Blueprint $schema) {
        $schema->delete();
    }
];
