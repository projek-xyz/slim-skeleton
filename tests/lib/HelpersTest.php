<?php

namespace Projek\Slim\Tests;

use Projek\Slim\Container;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        new Container([]);
    }

    public function test_array_get()
    {
        $data = [
            'foo' => [
                'bar' => 'baz'
            ],
            'bar' => [
                'foo' => [
                    'baz' => 'buzz'
                ]
            ]
        ];

        $this->assertEquals('baz', array_get($data, 'foo.bar'));
        $this->assertEquals('buzz', array_get($data, 'bar.foo.baz'));
    }

    public function test_array_devide()
    {
        list($keys, $values) = array_devide([
            1 => 'one',
            2 => 'two',
            3 => 'three'
        ]);

        $this->assertEquals([1, 2, 3], $keys);
        $this->assertEquals(['one', 'two', 'three'], $values);
    }
}
