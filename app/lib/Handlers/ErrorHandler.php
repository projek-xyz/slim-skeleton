<?php
namespace Projek\Slim\Handlers;

use Projek\Slim\View;
use Psr\Log\LogLevel;
use Slim\Handlers\Error;
use Exception;

class ErrorHandler extends Error
{
    /**
     * {@inheritdoc}
     */
    protected function renderHtmlErrorMessage(Exception $exception)
    {
        if ($this->displayErrorDetails) {
            $html = [
                '<p>The application could not run because of the following error:</p>',
                '<h2>Details</h2>',
                $this->renderHtmlException($exception)
            ];

            while ($exception = $exception->getPrevious()) {
                $html[] = '<h2>Previous exception</h2>';
                $html[] = $this->renderHtmlException($exception);
            }
        } else {
            $html = ['<p>A website error has occurred. Sorry for the temporary inconvenience.</p>'];
        }

        return app(View::class)->render('error::500', [
            'title' => 'Application Error',
            'html' => implode(PHP_EOL, $html)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function logError($message)
    {
        logger(LogLevel::ERROR, $message);
    }
}
