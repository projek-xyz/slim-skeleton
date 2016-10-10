<?php
namespace Projek\Slim\Tests;

class HelperTest extends TestCase
{
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

        $this->assertEquals(['bar' => 'baz'], array_get($data, 'foo'));
        $this->assertEquals('baz', array_get($data, 'foo.bar'));
        $this->assertEquals('buzz', array_get($data, 'bar.foo.baz'));
        $this->assertEquals(null, array_get($data, 'foobar'));
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

    public function test_directory()
    {
        // Absolute Path

        $ds = DIRECTORY_SEPARATOR;
        $this->assertEquals(ROOT_DIR, directory(''));
        $this->assertEquals(ROOT_DIR.'app'.$ds, directory('app'));
        $this->assertEquals(ROOT_DIR.'app'.$ds.'data'.$ds, directory('app.data'));

        // Relative Path

        $this->assertEquals('', directory('storage', 'storage'));
        $this->assertEquals('uploads'.$ds, directory('storage.uploads', 'storage'));
    }
}
