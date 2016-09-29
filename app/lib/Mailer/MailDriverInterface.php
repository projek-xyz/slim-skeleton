<?php
namespace Projek\Slim\Mailer;

interface MailDriverInterface
{
    /**
     * Setup Sender
     *
     * @param  string  $email
     * @param  string  $name
     *
     * @return $this
     */
    public function from($email, $name);

    /**
     * Add recipient email address.
     *
     * @param  string  $address
     * @param  string  $name
     *
     * @return $this
     */
    public function to($address, $name = '');

    /**
     * Add email subject.
     *
     * @param  string  $subject
     *
     * @return $this
     */
    public function subject($subject);

    /**
     * Write email body.
     *
     * @param  string  $body
     * @param  array  $data
     *
     * @return $this
     */
    public function content($body, array $data = []);

    /**
     * Add attachments.
     *
     * @param  array|string $filepath
     * @param  string $name
     * @param  string $encoding
     * @param  string $type
     * @param  string $disposition
     *
     * @return $this
     */
    public function attaches($filepath, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment');

    /**
     * Send the thing.
     *
     * @return mixed
     */
    public function send();
}