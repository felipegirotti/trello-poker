<?php

namespace TrelloPoker\Controller;

use \TrelloPoker\Functions,
   \Respect\Validation\Validator as v,
   \TrelloPoker\Model\Poker;

class PokerController extends BaseController
{
    /**
     *
     * @var \TrelloPoker\Model\Poker 
     */
    protected $_model = 'TrelloPoker\Model\Poker';
    
    public function get() 
    {
        Functions::renderJson('Trello Poker');
    }
    
    public function post() 
    {
        $data = Functions::dataPut();
        //VALIDAÇÂO
        $validation = v::arr()
                        ->key('nome', v::string()->notEmpty())
                        ->key('card', v::arr()->length(1))
                        ->key('member', v::arr()->length(1))
                        ->key('board-id', v::string()->notEmpty())
                        ->key('user-id', v::string()->notEmpty());
        if (!$validation->validate($data)) {
            Functions::headerException( new Exception('Validação de campos, nome, card, member, board-id, user-id', 406) );
        }
        
        try {
            $response = $this->_model->insertPoker($data);
            Functions::headerHttpCode(201);
            Functions::renderJson($response);
        } catch (\Exception $e) {
            Functions::headerException(new \Exception('Houve um erro durante a inserção do poker'));
        }
        
    }
    
}

