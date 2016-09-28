<?php

namespace Projek\Slim;

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
        return $this->write(app('view')->render($view, $data));
    }
}
