<?php
namespace App\Handlers;

use Exception;
use Slim\Handlers\Error;
use League\Plates\Engine;

class ErrorHandler extends Error
{
    private $view = null;

    public function setView(Engine $view)
    {
        $this->view = $view;
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

        return $this->view->render('error-500', compact('title', 'html'));
    }
}
