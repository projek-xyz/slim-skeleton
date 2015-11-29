<?php
namespace App\Handlers;

use Slim\Handlers\NotFound;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundHandler extends NotFound
{
    /**
     * {inheritdoc}
     */
    protected function renderHtmlErrorMessage(ServerRequestInterface $request)
    {
        $homeUrl = (string)($request->getUri()->withPath('')->withQuery('')->withFragment(''));
        // just comment it for now, $contentType = 'text/html';
        $output = <<<END
<html>
    <head>
        <title>Page Not Found</title>
        <style>
            body{
                margin:0;
                padding:30px;
                font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;
            }
            h1{
                margin:0;
                font-size:48px;
                font-weight:normal;
                line-height:48px;
            }
            strong{
                display:inline-block;
                width:65px;
            }
        </style>
    </head>
    <body>
        <h1>Page Not Found</h1>
        <p>
            The page you are looking for could not be found. Check the address bar
            to ensure your URL is spelled correctly. If all else fails, you can
            visit our home page at the link below.
        </p>
        <a href='$homeUrl'>Visit the Home Page</a>
    </body>
</html>
END;

        return $output;
    }
}
