<?php
namespace App\Handlers;

use Slim\Handlers\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use League\Plates\Engine;

class NotFoundHandler extends NotFound
{
    private $view;

    public function setView(Engine $view)
    {
        $this->view = $view;
    }

    /**
     * {inheritdoc}
     */
    protected function renderHtmlErrorMessage(ServerRequestInterface $request)
    {
        if (is_null($this->view)) {
            return parent::renderHtmlErrorMessage($request);
        }

        $title = 'Page Not Found';
        $desc = 'The page you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly. If all else fails, you can visit our home page at the link below.';
        $homeUrl = (string)($request->getUri()->withPath('')->withQuery('')->withFragment(''));

        return $this->view->render('error-404', compact('title', 'desc', 'homeUrl'));
    }
}
