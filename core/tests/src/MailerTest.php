<?php
namespace Projek\Slim\Tests;

class MailerTest extends TestCase
{
    public function setUp()
    {
        $this->settings = [
            'app' => [
                'title' => 'Slim Skeleton Testing',
                'email' => 'admin@example.com',
            ],
            'mailer' => [
                'host'     => getenv('EMAIL_HOST') ?: 'mailtrap.io',
                'port'     => getenv('EMAIL_PORT') ?: 2525,
                'username' => getenv('EMAIL_USER') ?: '',
                'password' => getenv('EMAIL_PASS') ?: '',
            ]
        ];

        parent::setUp();
    }

    public function test_should_sending_email()
    {
        $mailer = $this->container->get('mailer');

        $mailer->to('ferywardiyanto@gmail.com', 'Fery W');

        $this->assertTrue($mailer->send('Test Mail', 'Test'));
    }
}
