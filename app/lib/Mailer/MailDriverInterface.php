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
     * @return static
     */
    public function from($email, $name);

    /**
     * Add recipient email address.
     *
     * @param  string  $address
     * @param  string  $name
     *
     * @return static
     */
    public function to($address, $name = '');

    /**
     * Add CC.
     *
     * @param  string  $address
     * @param  string  $name
     *
     * @return static
     */
    public function cc($address, $name = '');

    /**
     * Add BCC.
     *
     * @param  string  $address
     * @param  string  $name
     *
     * @return static
     */
    public function bcc($address, $name = '');

    /**
     * Add email subject.
     *
     * @param  string  $subject
     *
     * @return static
     */
    public function subject($subject);

    /**
     * Write email body.
     *
     * @param  string  $body
     * @param  array  $data
     *
     * @return static
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
     * @return static
     */
    public function attaches($filepath, $name = '', $encoding = 'base64', $type = '', $disposition = 'attachment');

    /**
     * Send the thing.
     *
     * @return mixed
     */
    public function send();
}
