<?php
namespace Projek\Slim;

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
     * @return static
     */
    public function from($email, $name = '')
    {
        $this->driver->from($email, $name);

        return $this;
    }

    /**
     * Setup Reciepant
     *
     * @param  string  $email
     * @param  string  $name
     *
     * @return static
     */
    public function to($email, $name = '')
    {
        $this->driver->to($email, $name);

        return $this;
    }

    /**
     * Send the thing.
     *
     * @param  string $subject
     * @param  string $content
     * @param  array $data
     * @param  callable|null $callback
     *
     * @return mixed
     */
    public function send($subject, $content, $data = [], callable $callback = null)
    {
        $this->driver->subject($subject)->content($content, $data);

        if (null !== $callback) {
            $callback($this->driver);
        }

        return $this->driver->send();
    }
}
