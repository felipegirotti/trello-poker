<?php

namespace TrelloPoker\Controller;

use \TrelloPoker\Functions,
   \Respect\Validation\Validator as v,
   \TrelloPoker\Model\Poker;

class PlayController extends BaseController
{
    /**
     *
     * @var \TrelloPoker\Model\Poker 
     */
    protected $_model = 'TrelloPoker\Model\Poker';
    
    public function get($idCrypt) 
    {
        $id = $this->_model->deCryptId($idCrypt);
        $data = $this->_model->getPlanningPoker($id);
        echo $this->_twig->render('play/index.phtml', array('data' => $data));
    }
}

