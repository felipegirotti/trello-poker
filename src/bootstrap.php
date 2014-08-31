<?php
define('APPLICATION_PATH', __DIR__ . '/TrelloPoker');
define('DIR_PUBLIC', __DIR__ . '/../public_html');
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV')) ? getenv('APPLICATION_ENV') : 'prod');


// show errors
if (APPLICATION_ENV !== 'prod') {
    ini_set('display_error', 1);
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

require_once __DIR__ . '/../vendor/autoload.php';

$nameConfig = 'application_' . APPLICATION_ENV . '.ini';

date_default_timezone_set('America/Sao_Paulo');

$config = new Respect\Config\Container(__DIR__ . '/config/' . $nameConfig);
$pdo = new \PDO($config->connection['dsn'], $config->connection['username'], $config->connection['password']);
$mapper = new Respect\Relational\Mapper($pdo);

$loader = new Twig_Loader_Filesystem(APPLICATION_PATH . '/views');
$twig = new Twig_Environment($loader, array(
    'cache' => APPLICATION_PATH . '/cache' ,
));
