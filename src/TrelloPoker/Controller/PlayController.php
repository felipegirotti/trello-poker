<?php

namespace TrelloPoker\Controller;

use TrelloPoker\Model\Poker;

class PlayController extends BaseController
{
    /**
     *
     * @var \TrelloPoker\Model\Poker 
     */
    protected $model = 'TrelloPoker\Model\Poker';
    
    public function get($idCrypt)
    {
        $id = $this->model->deCryptId($idCrypt);
        $data = $this->model->getPlanningPoker($id);
        echo $this->twig->render('play/index.phtml', array('data' => $data));
    }
}
