<?php
namespace App\Handlers;

use App\Utils;
use Slim\Handlers\Error;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Exception;

class ErrorHandler extends Error
{
    use Utils\ViewableAware;
    use Utils\LoggableAware;

    /**
     * {inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $exception)
    {
        $this->logger->critical($exception->getMessage(), [
            $request->getMethod() => (string) $request->getUri()
        ]);

        return parent::__invoke($request, $response, $exception);
    }

    /**
     * {inheritdoc}
     */
    protected function renderHtmlErrorMessage(Exception $exception)
    {
        if (is_null($this->view)) {
            return parent::renderHtmlErrorMessage($exception);
        }

        $title = 'Application Error';

        if ($this->displayErrorDetails) {
            $html = '<p>The application could not run because of the following error:</p>';
            $html .= '<h2>Details</h2>';
            $html .= $this->renderHtmlException($exception);

            while ($exception = $exception->getPrevious()) {
                $html .= '<h2>Previous exception</h2>';
                $html .= $this->renderHtmlException($exception);
            }
        } else {
            $html = '<p>A website error has occurred. Sorry for the temporary inconvenience.</p>';
        }

        $this->view->addData(compact('title', 'html'));

        return $this->view->render('error-500');
    }
}
