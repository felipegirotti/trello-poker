<?php
require_once '../bootstrap.php';

$router = new Respect\Rest\Router();
$db = new Respect\Relational\Db($pdo);
$configController = array($mapper, $db, $config, $twig);

$router->get('/', 'TrelloPoker\Controller\HomeController', $configController);

$router->any('/my/*', 'TrelloPoker\Controller\MyPokerController', $configController);

$router->get('/poker', 'TrelloPoker\Controller\PokerController', $configController);

$router->post('/poker/add', 'TrelloPoker\Controller\PokerController', $configController);

