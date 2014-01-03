<?php
define('APPLICATION_PATH', __DIR__ . '/TrelloPoker');
define('DIR_PUBLIC', __DIR__ . '/public');
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV')) ? getenv('APPLICATION_ENV') : 'development');

ini_set('display_error', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

$config = new Respect\Config\Container(__DIR__ . '/config/application.ini');
$pdo = new \PDO($config->connection['dsn'], $config->connection['username'], $config->connection['password']);
$mapper = new Respect\Relational\Mapper($pdo);

$loader = new Twig_Loader_Filesystem(APPLICATION_PATH . '/views');
$twig = new Twig_Environment($loader, array(
    /*'cache' => APPLICATION_PATH . '/cache' ,*/
));



