<?php
namespace Projek\Slim\Handlers;

use Projek\Slim\Utils;
use Projek\Slim\Contracts\LoggableInterface;
use Projek\Slim\Contracts\ViewableInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LogLevel;
use Slim\Handlers\Error;
use Exception;

class ErrorHandler extends Error implements ViewableInterface
{
    use Utils\ViewableAware;

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $exception)
    {
        logger(LogLevel::CRITICAL, $exception->getMessage(), [
            $request->getMethod() => (string) $request->getUri()
        ]);

        return parent::__invoke($request, $response, $exception);
    }

    protected function renderHtmlErrorMessage(Exception $exception)
    {
        if (is_null($this->view)) {
            return parent::renderHtmlErrorMessage($exception);
        }

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

        $this->view->addData([
            'title' => 'Application Error',
            'html' => implode(PHP_EOL, $html)
        ]);

        return $this->view->render('error::500');
    }
}
