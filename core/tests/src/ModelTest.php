<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Database\Models;
use Slim\PDO\Database;
use Slim\PDO\Statement\StatementContainer;

class ModelTest extends DatabaseTestCase
{
    public function tearDown()
    {
        $dummy = new Dummy;
        $this->container->db->query(
            sprintf('TRUNCATE TABLE %s', $dummy->table())
        );
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

        // dump('');
        // $this->assertEquals(1, $dummy->delete());
        $this->assertEquals(1, $model->delete(1));
        $this->assertEquals(1, Dummy::delete(['address' => 'Out there']));
        $this->assertEquals(1, DummyDestructive::delete(['address' => 'Somewhere']));
    }

    public function test_normalize_terms()
    {
        /** @var  Database $db */
        $db = $this->container->get('db');
        $dummy = new Dummy(['name' => 'John Doe', 'address' => 'Somewhere']);
        $method = $this->makeMethodInvokable(Models::class, 'normalizeTerms');

        $dummy->create();

        // Should pass an INT

        $query = $db->select()->from($dummy->table());
        $method->invoke($dummy, $query, 1);
        $this->assertEquals('SELECT * FROM dummy WHERE id = ?', $query->compile());

        // Should pass an Model instance

        $query = $db->select()->from($dummy->table());
        $method->invoke($dummy, $query, $dummy);
        $this->assertEquals('SELECT * FROM dummy WHERE id = ?', $query->compile());

        // Should find multiple column

        $query = $db->select()->from($dummy->table());
        $method->invoke($dummy, $query, ['id' => 1, 'name' => 'John Doe']);
        $this->assertEquals('SELECT * FROM dummy WHERE id = ? AND name = ?', $query->compile());

        // Should find multiple value in a column

        $query = $db->select()->from($dummy->table());
        $method->invoke($dummy, $query, ['name' => ['John Doe', 'Sally Doe']]);
        $this->assertEquals('SELECT * FROM dummy WHERE name IN ( ? , ? )', $query->compile());

        // Should find null column

        $query = $db->select()->from($dummy->table());
        $method->invoke($dummy, $query, ['name' => null]);
        $this->assertEquals('SELECT * FROM dummy WHERE name IS NULL', $query->compile());

        // Should find with another symbols

        $query = $db->select()->from($dummy->table());
        $method->invoke($dummy, $query, ['name <>' => 'Sally Doe', 'id >' => 1]);
        $this->assertEquals('SELECT * FROM dummy WHERE name <> ? AND id > ?', $query->compile());

        // Should find with callable

        $query = $db->select()->from($dummy->table());
        $method->invoke($dummy, $query, function (StatementContainer $query) {
            $query->where('name', '=', 'John Dow')->where('id', '=', 1);
        });
        $this->assertEquals('SELECT * FROM dummy WHERE name = ? AND id = ?', $query->compile());
    }

    public function test_normalize_joins()
    {
        /** @var  Database $db */
        $related = new DummyNoTimestamp();
        $dummy = new Dummy(['name' => 'John Doe', 'address' => 'Somewhere']);
        $method = $this->makeMethodInvokable(Models::class, 'normalizeJoins');

        $dummy->create();

        // Assert default fields

        $this->assertEquals(
            [$related, 'dummy.id', 'dummy.dummy_id'],
            $method->invoke($dummy, DummyNoTimestamp::class)
        );

        // Assert custom $first field

        $this->assertEquals(
            [$related, 'dummy.name', 'dummy.dummy_id'],
            $method->invoke($dummy, DummyNoTimestamp::class, 'name')
        );

        // Assert custom fields

        $this->assertEquals(
            [$related, 'dummy.name', 'dummy.dummy_name'],
            $method->invoke($dummy, DummyNoTimestamp::class, 'name', 'dummy_name')
        );

        // Assert thrown exception

        $this->setExpectedException(\InvalidArgumentException::class);

        $method->invoke($dummy, static::class);
    }

    public function test_should_throw_exception_when_creating_empty_data()
    {
        $this->setExpectedException(\LogicException::class);

        $dummy = new Dummy([]);
        $this->assertEquals(3, $dummy->create());
    }

    public function test_should_throw_exception_when_updating_empty_data()
    {
        $this->setExpectedException(\LogicException::class);

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

class DummyNoTimestamp extends Dummy
{
    protected $timestamps = false;
}

class DummyDestructive extends Dummy
{
    protected $softDeletes = false;
}
