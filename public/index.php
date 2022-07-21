<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Andromeda\Belajar\PHP\MVC\App\Router;
use Andromeda\Belajar\PHP\MVC\Controller\HomeController;

Router::add('GET', '/', HomeController::class, 'index', []);

Router::run();