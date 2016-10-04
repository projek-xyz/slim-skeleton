<?php

use Projek\Slim\Database\Migration;

return [
    'up' => function (Migration $table) {
        $table->alter('dummy', function () {
            $this->rename('other_dummy');
        });
    },
    'down' => function (Migration $table) {
        $table->alter('other_dummy', function () {
            $this->rename('dummy');
        });
    }
];
