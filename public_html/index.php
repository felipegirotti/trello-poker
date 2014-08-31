<?php
require_once __DIR__ . '/../src/bootstrap.php';

use TrelloPoker\Functions,
    TrelloPoker\Model\Poker,
    Respect\Validation\Validator as v;

/* @var $twig \Twig_Environment */
$router = new Respect\Rest\Router();
$db = new Respect\Relational\Db($pdo);
$configController = array($mapper, $db, $config, $twig);
$model = new Poker($mapper, $db, $config);

$router->get('/', 'TrelloPoker\Controller\HomeController', $configController);

$router->any('/my/*', 'TrelloPoker\Controller\MyPokerController', $configController);

$router->get('/poker', 'TrelloPoker\Controller\PokerController', $configController);

$router->post('/poker/add', 'TrelloPoker\Controller\PokerController', $configController);

$router->get('/poker/play/*', 'TrelloPoker\Controller\PlayController', $configController);

$router->post('/poker/play/users/', function () use ($model) {
    try {
        $users = $model->getUsersOfPoker($_POST);
        Functions::renderJson(is_array($users) ? $users : array());
    } catch (\Exception $e) {
        Functions::headerException($e);
    }
});

$router->post('/poker/play/vote/*', function ($idUser) use ($model) {
    $data = $_POST;
    $data['member_id'] = $idUser;
    $model->addVote($data);
});

$router->post('/poker/play/close', function () use ($model) {
    $data = $_POST;
    try {
        $cardId = $model->closeCard($data);
        Functions::renderJson(array('success' => array('message' => 'Save successful', 'card' => $cardId)));
    } catch (\Exception $e) {
        Functions::headerException($e);
    }
});

$router->post('/poker/play/regame', function () use ($model) {
    $data = $_POST;
    try {
        $regame = $model->regame($data);
        if ($regame) {
            Functions::headerHttpCode(202);
            Functions::renderJson(array('success' => array('message' => 'Regame successul', 'code' => 202)));
        }
    } catch (\Exception $e) {
        Functions::headerException($e);
    }
});

$router->any('/poker/play/add-user/*', 'TrelloPoker\Controller\PlayAddUserController', $configController);

$router->get('/about', function () use ($twig) {

    echo $twig->render('home/about.phtml');

});
