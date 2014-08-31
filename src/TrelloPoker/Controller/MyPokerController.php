<?php

namespace TrelloPoker\Controller;

class MyPokerController extends BaseController
{
    /**
     *
     * @var \TrelloPoker\Model\Poker
     */
    protected $model = 'TrelloPoker\Model\Poker';
    
    public function get($memberId)
    {
        $data = array('pokers' => $this->_model->myPokers($memberId));
        echo $this->_twig->render('mypoker/index.phtml', $data);
    }
}
