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
        $this->assertFalse(Sample::get());

        $this->assertFalse($model->create(['foo' => 'bar']));
        $this->assertFalse(Sample::add(['foo' => 'bar']));

        $this->assertFalse($model->edit(['foo' => 'bar']));
        $this->assertFalse(Sample::put(['foo' => 'bar']));

        $this->assertFalse($model->remove(['foo' => 'bar']));
        $this->assertFalse(Sample::del(['foo' => 'bar']));
    }

    public function test_should_create_with_certain_method()
    {
        $model = $this->data(Dummy::class);
        $data = [
            'name' => 'John Doe',
            'address' => 'Somewhere'
        ];

        // Creation

        $this->assertEquals(1, $model->create($data));

        $data['name'] = 'Selly Doe';
        $this->assertEquals(2, Dummy::add($data));

        $data['name'] = 'Don Joe';
        $dummy = new Dummy($data);
        $this->assertEquals(3, $dummy->create());

        // Reading

        $this->assertEquals(3, count($model->show()->fetchAll()));
        $this->assertEquals(3, count(Dummy::get()->fetchAll()));
        $this->assertEquals(3, $model->count());

        $fetch = $model->show(['name' => 'John Doe'])->fetch();
        $this->assertInstanceOf(Dummy::class, $fetch);
        $this->assertEquals("Somewhere", $fetch->address);

        // Update

        $this->assertTrue($model->edit(['address' => 'Homeless'], 1));
        $this->assertTrue(Dummy::put(['address' => 'Out there'], 2));

        $dummy = new Dummy(['name' => 'Don Joe']);
        $this->assertTrue($dummy->edit(['address' => 'No clue']));

        // Deletion

        $this->assertTrue($model->remove(1));
        $this->assertTrue(Dummy::del(['address' => 'Out there']));
        $this->assertTrue(DummyDestructive::del(['address' => 'Out there']));
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
        $this->assertEquals(3, $dummy->edit());
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

class DummyNoTimestamp extends Models
{
    protected $table = 'dummy';
    protected $timestamps = false;
}

class DummyDestructive extends Models
{
    protected $table = 'dummy';
    protected $destructive = true;
}
