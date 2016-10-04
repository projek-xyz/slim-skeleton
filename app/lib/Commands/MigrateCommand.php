<?php
namespace Projek\Slim\Commands;

use Projek\Slim\Action;
use Projek\Slim\Response;
use Slim\Http\Request;

class MigrateCommand extends Action
{
    /**
     * @api  GET  /
     * @param  Request  $req
     * @param  Response $res
     * @param  array    $args
     * @return mixed
     */
    public function __invoke(Request $req, Response $res, $args)
    {
        $action = isset($args['action']) ? $args['action'] : 'up';
        $migrate = $this->migrator->migrate($action);

        if (true === $migrate) {
            return 'Migration successful';
        } elseif (null === $migrate) {
            return 'No Migration executed';
        }

        return 'Migration fail';
    }
}
