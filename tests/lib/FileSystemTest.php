<?php
namespace Projek\Slim\Tests;

use League\Flysystem\Exception;
use Projek\Slim\FileSystem;

class FileSystemTest extends TestCase
{
    public function setUp()
    {
        $this->settings = [
            'filesystem' => $this->fsSettings()
        ];

        parent::setUp();
    }

    public function test_should_access_default_adapter()
    {
        try {
            $FSs = [
                new FileSystem($this->fsSettings()),
                $this->container->get('filesystem')
            ];

            foreach ($FSs as $fs) {
                $this->assertInstanceOf(FileSystem::class, $fs->local);
                $this->assertTrue(is_callable([$fs->local, 'copy']));
                $this->assertTrue(is_callable([$fs, 'copy']));
            }
        } catch (Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    private function fsSettings()
    {
        return [
            'local' => [
                'directory' => realpath('../../storage')
            ]
        ];
    }
}
