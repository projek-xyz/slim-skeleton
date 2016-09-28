<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Models;

class ModelTest extends TestCase
{
    public function setUp()
    {
        $this->settings = [
            'db' => [
                'driver' => getenv('DB_DRIVER'),
                'host'   => getenv('DB_HOST'),
                'user'   => getenv('DB_USER'),
                'pass'   => getenv('DB_PASS'),
                'name'   => getenv('DB_NAME'),
            ]
        ];

        parent::setUp();
    }

    private function data($class)
    {
        $data = $this->container->get('data');

        return $data($class);
    }

    public function test_should_get_result()
    {
        /** @var  Sample $model */
        $model = $this->data(Sample::class);

        $this->assertFalse($model->show());
        $this->assertFalse(Sample::get());

        $this->assertFalse($model->create(['foo' => 'bar']));
        $this->assertFalse(Sample::add(['foo' => 'bar']));

        $this->assertFalse($model->edit(['foo' => 'bar']));
        $this->assertFalse(Sample::put(['foo' => 'bar']));

        $this->assertFalse($model->remove(['foo' => 'bar']));
        $this->assertFalse(Sample::del(['foo' => 'bar']));
    }
}

class Sample extends Models
{
    protected $table = false;
}

class Dummy extends Models
{
    protected $table = 'dummy';
}
