<?php

namespace TrelloPoker\Controller;

use \TrelloPoker\Functions,
   \Respect\Validation\Validator as v,
   \TrelloPoker\Model\Poker;

class PlayDataController extends BaseController
{
    /**
     *
     * @var \TrelloPoker\Model\Poker 
     */
    protected $model = 'TrelloPoker\Model\Poker';
    
    public function get($idCrypt)
    {
        $id = $this->model->deCryptId($idCrypt);
        Functions::renderJson($this->model->getPlanningPoker($id));
    }
}
