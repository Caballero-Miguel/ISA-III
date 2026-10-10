<?php

declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

use ISA3\Controller\AuthController;
use ISA3\Repository\UsuarioRepository;

(new AuthController(new UsuarioRepository(db())))->logout();

header('Location: login.php');
exit;
