<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Database\Models;

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

    public function tearDown()
    {
        $this->container->db->query('TRUNCATE TABLE dummy');
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

        $this->assertEquals(0, $model->count());

        $this->assertFalse($model->show());
        $this->assertFalse(Sample::show());

        $this->assertFalse($model->create(['foo' => 'bar']));
        $this->assertFalse(Sample::create(['foo' => 'bar']));

        $this->assertFalse($model->patch(['foo' => 'bar']));
        $this->assertFalse(Sample::patch(['foo' => 'bar']));

        $this->assertFalse($model->delete(['foo' => 'bar']));
        $this->assertFalse(Sample::delete(['foo' => 'bar']));
    }

    public function test_should_create_with_certain_method()
    {
        /** @var Dummy $model */
        $model = $this->data(Dummy::class);
        $data = [
            'name' => 'John Doe',
            'address' => 'Somewhere'
        ];

        // Creating

        $this->assertEquals(1, $model->create($data));

        $data['name'] = 'Selly Doe';
        $this->assertEquals(2, Dummy::create($data));

        $data['name'] = 'Don Joe';
        $dummy = new Dummy($data);
        $this->assertEquals(3, $dummy->create());

        // Reading

        $this->assertEquals(3, $dummy->count());
        $this->assertEquals(3, $model->count());
        $this->assertEquals(3, $model->show()->count());
        $this->assertEquals(3, Dummy::show()->count());

        // Countable

        $this->assertEquals(3, count($dummy));
        $this->assertEquals(3, count($model));
        $this->assertEquals(3, count($model->show()));
        $this->assertEquals(3, count(Dummy::show()));

        // Return self instance n fetch

        $fetch = $model->show(['name' => 'John Doe'])->get();
        $this->assertInstanceOf(Dummy::class, $fetch);
        $this->assertEquals('Somewhere', $fetch->address);

        // Updating

        $this->assertEquals(1, $dummy->patch(['address' => 'No clue']));
        $this->assertEquals(1, $model->patch(['address' => 'Homeless'], 1));
        $this->assertEquals(1, Dummy::patch(['address' => 'Out there'], 2));

        // Deleting

        $this->assertEquals(1, $dummy->delete());
        $this->assertEquals(1, $model->delete(1));
        $this->assertEquals(1, Dummy::delete(['address' => 'Out there']));
        $this->assertEquals(1, DummyDestructive::delete(['address' => 'No clue']));
    }

    /**
     * @expectedException \LogicException
     */
    public function test_should_throw_exception_when_creating_empty_data()
    {
        $dummy = new Dummy([]);
        $this->assertEquals(3, $dummy->create());
    }

    /**
     * @expectedException \LogicException
     */
    public function test_should_throw_exception_when_updating_empty_data()
    {
        $dummy = new Dummy([]);
        $this->assertEquals(3, $dummy->patch());
    }
}

class Sample extends Models
{
    protected $table = false;
}

class Dummy extends Models
{
    protected $table = 'dummy';
    protected $softDeletes = true;
}

class DummyNoTimestamp extends Models
{
    protected $table = 'dummy';
    protected $timestamps = false;
    protected $softDeletes = true;
}

class DummyDestructive extends Models
{
    protected $table = 'dummy';
}
