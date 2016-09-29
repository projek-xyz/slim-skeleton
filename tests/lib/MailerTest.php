<?php
namespace Projek\Slim\Tests;

class MailerTest extends TestCase
{
    public function setUp()
    {
        $this->settings = [
            'app' => [
                'title' => 'Slim Skeleton',
                'email' => 'admin@example.com',
            ],
            'mailer' => [
                'host'     => getenv('EMAIL_HOST') ?: '',
                'port'     => getenv('EMAIL_PORT') ?: '',
                'username' => getenv('EMAIL_USER') ?: '',
                'password' => getenv('EMAIL_PASS') ?: '',
            ]
        ];

        parent::setUp();
    }

    public function test_sending_email()
    {
        $mailer = $this->container->get('mailer');

        $mailer->to('ferywardiyanto@gmail.com', 'Fery W')->send('Test Mail', 'Test');

        $this->assertTrue(true);
    }
}
