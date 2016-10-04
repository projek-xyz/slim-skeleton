<?php

return [
    'up' => function ($migrator) {
        $migrator->create('dummy', [
            'id' => ['int' => 11, 'primary', 'null' => false, 'auto_increment'],
            'name' => ['varchar' => 100, 'null' => false],
            'address' => ['text' => true, 'null' => false],
            'deleted_at' => ['datetime' => true, 'default' => null],
            'created_at' => ['datetime' => true, 'default' => null],
            'updated_at' => ['timestamp' => true, 'default' => null],
        ]);
    },
    'down' => function ($migrator) {
        $migrator->delete('dummy');
    }
];
