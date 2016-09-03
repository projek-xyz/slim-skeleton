<?php
namespace App\Controllers;

use App\ContainerAware;
use Slim\Container;

abstract class Controller
{
    use ContainerAware;
}
