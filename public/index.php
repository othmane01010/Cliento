<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Bramus\Router\Router;
use App\Exceptions\NotFoundException;
use App\Exceptions\SecurityException;
use App\Exceptions\AuthenticationException;
use App\Traits\LoggerTrait;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

if (($_ENV['APP_ENV'] ?? 'development') === 'development' && ($_ENV['APP_DEBUG'] ?? false) === 'true') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();


$router->get('/', function() {
    if (!empty($_SESSION['admin_id'])) {
        header('Location: /dashboard');
    } else {
        header('Location: /login');
    }
    exit;
});

$router->get('/login', '\App\Controllers\AuthController@showLoginForm');
$router->post('/login', '\App\Controllers\AuthController@login');
$router->get('/logout', '\App\Controllers\AuthController@logout');
$router->post('/logout', '\App\Controllers\AuthController@logout');
$router->get('/subscriptions/receipt/(\d+)', '\App\Controllers\SubscriptionController@receipt');
$router->get('/dashboard', '\App\Controllers\DashboardController@index');

$router->mount('/clients', function() use ($router) {
    $router->get('/', '\App\Controllers\ClientController@index');
    $router->get('/create', '\App\Controllers\ClientController@create');
    $router->post('/store', '\App\Controllers\ClientController@store');
    $router->get('/show/(\d+)', '\App\Controllers\ClientController@show');
    $router->get('/edit/(\d+)', '\App\Controllers\ClientController@edit');
    $router->post('/update/(\d+)', '\App\Controllers\ClientController@update');
    $router->post('/delete/(\d+)', '\App\Controllers\ClientController@delete');
    $router->get('/search', '\App\Controllers\ClientController@search');
});


$router->mount('/plans', function() use ($router) {
    $router->get('/', '\App\Controllers\PlanController@index');
    $router->get('/create', '\App\Controllers\PlanController@create');
    $router->post('/store', '\App\Controllers\PlanController@store');
    $router->get('/edit/(\d+)', '\App\Controllers\PlanController@edit');
    $router->post('/update/(\d+)', '\App\Controllers\PlanController@update');
    $router->post('/toggle/(\d+)', '\App\Controllers\PlanController@toggle');
    $router->post('/delete/(\d+)', '\App\Controllers\PlanController@delete');
});


$router->mount('/subscriptions', function() use ($router) {
    $router->get('/', '\App\Controllers\SubscriptionController@index');
    $router->get('/create', '\App\Controllers\SubscriptionController@create');
    $router->post('/store', '\App\Controllers\SubscriptionController@store');
    $router->get('/show/(\d+)', '\App\Controllers\SubscriptionController@show');
    $router->post('/cancel/(\d+)', '\App\Controllers\SubscriptionController@cancel');
    $router->post('/delete/(\d+)', '\App\Controllers\SubscriptionController@delete');
});


$router->set404(function() {
    header('HTTP/1.1 404 Not Found');
    echo "<div style='text-align:center; padding:60px 20px; font-family:system-ui, sans-serif;'>";
    echo "<h1 style='font-size:72px; color:#e74c3c; margin:0;'>404</h1>";
    echo "<h2 style='color:#2c3e50;'>Page introuvable</h2>";
    echo "<p style='color:#7f8c8d;'>La page que vous recherchez n'existe pas ou a été déplacée.</p>";
    echo "<p><a href='/dashboard' style='display:inline-block; padding:10px 20px; background:#3498db; color:#fff; text-decoration:none; border-radius:5px;'>Retour au tableau de bord</a></p>";
    echo "</div>";
});

try {
    $router->run();
} catch (NotFoundException $e) {
    header('HTTP/1.1 404 Not Found');
    echo "<h1 style='text-align:center; padding:50px; font-family:sans-serif;'>404 - " . htmlspecialchars($e->getMessage()) . "</h1>";
} catch (SecurityException $e) {
    header('HTTP/1.1 403 Forbidden');
    echo "<h1 style='text-align:center; padding:50px; font-family:sans-serif; color:red;'>403 - Accès Refusé: " . htmlspecialchars($e->getMessage()) . "</h1>";
} catch (\Throwable $e) {
    class_exists(LoggerTrait::class) && (new class { use LoggerTrait; })::logError($e);
    header('HTTP/1.1 500 Internal Server Error');
    if (($_ENV['APP_ENV'] ?? '') === 'development' && ($_ENV['APP_DEBUG'] ?? false) === 'true') {
        echo "<pre style='background:#f8d7da; color:#721c24; padding:20px; border-radius:5px;'>";
        echo "<strong>Exception:</strong> " . htmlspecialchars($e->getMessage()) . "\n";
        echo "<strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "\n";
        echo "<strong>Trace:</strong>\n" . htmlspecialchars($e->getTraceAsString());
        echo "</pre>";
    } else {
        echo "<h1 style='text-align:center; padding:50px; font-family:sans-serif;'>Une erreur interne est survenue.</h1>";
    }
}