<?php
namespace Projek\Slim\Http;

use Projek\Slim\View;
use Slim\Http\Response as SlimResponse;

class Response extends SlimResponse
{
    /**
     * Render the template
     *
     * @param  string   $view
     * @param  string[] $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \LogicException
     */
    public function withView($view, array $data = [])
    {
        $view = app(View::class)->render($view, $data);

        return $this->write($view);
    }
}
