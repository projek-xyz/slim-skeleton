<?php
namespace App\Actions;

use App\Actions;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeAction extends Actions
{
    public function index(Request $req, Response $res, $args)
    {
        if (isset($args['name'])) {
            return $this->view->render('hello', [
                'name' => $args['name'],
                'desc' => 'Welcome to world',
            ]);
        }

        return $this->view->render('home');
    }

    public function email(Request $req, Response $res)
    {
        $mail = $this->mailer->to('feryardiant@gmail.com', 'Fery Wardiyanto')
            ->withSubject('Coba Email Sender')
            ->withBody('Hallo Fery');

        if ($mail->send()) {
            $this->logger->info('Email Send', []);

            return $res->write('Email Send');
        }

        $this->logger->warning('Email not send');

        return $res->write('Email not send');
    }
}
