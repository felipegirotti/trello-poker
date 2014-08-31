<?php

namespace TrelloPoker\Controller;

use \TrelloPoker\Functions;
use \Respect\Validation\Validator as v;
use \TrelloPoker\Model\Poker;

class PokerController extends BaseController
{
    /**
     *
     * @var \TrelloPoker\Model\Poker 
     */
    protected $model = 'TrelloPoker\Model\Poker';
    
    public function get()
    {
        Functions::renderJson('Trello Poker');
    }
    
    public function post()
    {
        $data = Functions::dataPut();

        $validation = v::arr()
                        ->key('nome', v::string()->notEmpty())
                        ->key('card', v::arr()->length(1))
                        ->key('member', v::arr()->length(1))
                        ->key('board-id', v::string()->notEmpty())
                        ->key('user-id', v::string()->notEmpty());
        if (!$validation->validate($data)) {
            Functions::headerException(
                new Exception('Validate fields, name, card, member, board-id, user-id', 406)
            );
        }
        
        try {
            $response = $this->model->insertPoker($data);
            Functions::headerHttpCode(201);
            Functions::renderJson($response);
        } catch (\Exception $e) {
            Functions::headerException(new \Exception('Ops! There was an error, try again'));
        }
        
    }
}
