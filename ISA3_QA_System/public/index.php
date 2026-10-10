<?php

declare(strict_types=1);

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

use ISA3\Support\Auth;

if (Auth::esta_logueado()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
