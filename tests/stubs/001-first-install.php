<?php

use Projek\Slim\Database\Blueprint;

return [
    'up' => function () {
        $this->create('dummy', [
            'id' => ['int' => 11, 'primary', 'null' => false, 'auto_increment'],
            'name' => ['varchar' => 100, 'null' => false],
            'address' => ['text', 'null' => false],
            Blueprint::TIMESTAMPS,
            Blueprint::SOFTDELETES,
        ]);
    },
    'down' => function () {
        $this->delete('dummy');
    }
];
