<?php

use Projek\Slim\Database\Migration;

return [
    'up' => function () {
        $this->create('dummy', [
            'id' => ['int' => 11, 'primary', 'null' => false, 'auto_increment'],
            'name' => ['varchar' => 100, 'null' => false],
            'address' => ['text', 'null' => false],
            Migration::TIMESTAMPS,
            Migration::SOFTDELETES,
        ]);
    },
    'down' => function () {
        $this->delete('dummy');
    }
];
