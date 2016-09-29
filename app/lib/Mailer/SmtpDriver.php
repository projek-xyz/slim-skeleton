<?php
namespace Projek\Slim\Mailer;

use Psr\Log\LogLevel;

class SmtpDriver implements MailDriverInterface
{
    /**
     * @var  PHPMailer
     */
    protected $mail;

    /**
     * @var  array
     */
    protected $settings = [
        'host' => '',
        'port' => '',
        'username' => '',
        'password' => '',
        'auth' => true,
        'secure' => 'tsl',
    ];

    /**
     * @var  array
     */
    private $debugMode = [
        'debug' => 3,
        'development' => 2,
        'production' => 1,
        'testing' => 0,
    ];

    /**
     * @param  array  $settings
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $settings = [])
    {
        $settings = array_merge($this->settings, $settings);

        $this->mail = new \PHPMailer(true);

        $this->mail->Host = $settings['host'];
        $this->mail->Port = $settings['port'];
        $this->mail->Username = $settings['username'];
        $this->mail->Password = $settings['password'];

        $this->mail->isSMTP();

        $this->mail->SMTPAuth = $settings['auth'];
        $this->mail->SMTPSecure = $settings['secure'];
    }

    /**
     * Set mailer debug mode
     *
     * @param  string  $mode
     *
     * @return $this
     */
    public function debugMode($mode)
    {
        if (!isset($this->debugMode[$mode])) {
            $mode = 'production';
        }

        $this->mail->SMTPDebug = $this->debugMode[$mode];
        $this->mail->Debugoutput = function ($str, $mode) {
            logger(LogLevel::DEBUG, $str, ['debugMode' => $mode]);
        };

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function from($email, $name)
    {
        $this->mail->setFrom($email, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function to($address, $name = '')
    {
        $this->mail->addAddress($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function cc($address, $name = '')
    {
        $this->mail->addCC($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function bcc($address, $name = '')
    {
        $this->mail->addBCC($address, $name);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function subject($subject)
    {
        $this->mail->Subject = $subject;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function content($body, array $data = [])
    {
        if (strpos($body, '::') !== false) {
            $this->mail->isHTML(true);

            $body = app('view')->render($body, $data);
        }

        $this->mail->Body = $body;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function attaches($filepath, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment')
    {
        if (is_string($filepath)) {
            $this->mail->addAttachment($filepath, $name, $encoding, $type, $disposition);
        }

        foreach ($filepath as $path => $name) {
            if (is_numeric($path)) {
                $path = $name;
                $name = '';
            }

            $this->mail->addAttachment($path, $name);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function send()
    {
        return $this->mail->send();
    }
}
