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
    protected $_model = 'TrelloPoker\Model\Poker';
    
    public function get($idCrypt) 
    {
        $id = $this->_model->deCryptId($idCrypt);
        Functions::renderJson($this->_model->getPlanningPoker($id));
    }
}