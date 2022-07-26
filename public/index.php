<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Andromeda\Belajar\PHP\MVC\App\Router;
use Andromeda\Belajar\PHP\MVC\Config\Database;
use Andromeda\Belajar\PHP\MVC\Controller\HomeController;
use Andromeda\Belajar\PHP\MVC\Controller\UserController;

Database::getConnection("prod");

// Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller
Router::add('GET', '/users/register', UserController::class, 'register', []);
Router::add('POST', '/users/register', UserController::class, 'postRegister', []);
Router::add('GET', '/users/login', UserController::class, 'login', []);
Router::add('POST', '/users/login', UserController::class, 'postLogin', []);

Router::run();