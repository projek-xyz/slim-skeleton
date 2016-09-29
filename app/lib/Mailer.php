<?php
namespace Projek\Slim;

use PHPMailer;

class Mailer
{
    /**
     * @var  Mailer\MailDriverInterface
     */
    protected $driver;

    /**
     * @param  Mailer\MailDriverInterface $driver
     */
    public function __construct(Mailer\MailDriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Setup Sender
     *
     * @param  string  $email
     * @param  string  $name
     *
     * @return Mailer\MailDriverInterface
     */
    public function from($email, $name)
    {
        return $this->driver->from($email, $name);
    }

    /**
     * Send the thing.
     *
     * @param  string $content
     * @param  string $subject
     * @param  callable $callback
     *
     * @return mixed
     */
    public function send($content, $subject, callable $callback)
    {
        $this->driver->content($content)->subject($subject);

        $callback($this->driver);

        return $this->driver->send();
    }
}
