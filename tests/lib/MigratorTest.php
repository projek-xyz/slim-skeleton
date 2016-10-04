<?php
namespace Projek\Slim\Tests;

class MigratorTest extends TestCase
{
    public function setUp()
    {
        $this->settings = [
            'migration' => [
                'directory' => ROOT_DIR.'tests/stubs',
            ]
        ];

        parent::setUp();
    }

    public function test_should_be_true()
    {
        $this->assertTrue(true);
    }
}
