<?php

namespace TrelloPoker\Controller;

use \TrelloPoker\Functions;
use \Respect\Validation\Validator as v;
use \TrelloPoker\Model\Poker;

class PlayAddUserController extends BaseController
{
    /**
     *
     * @var \TrelloPoker\Model\Poker 
     */
    protected $model = 'TrelloPoker\Model\Poker';
    
    public function get($idPoker)
    {
        $data = $this->mapper->membro()->poker[$idPoker]->fetchAll();
        $users = array();
        $poker = new \stdClass();
        foreach ($data as $user) {
            if (!isset($poker->id)) {
                $poker = $user->poker_id;
            }
            $users[] = $user->member_id;
        }
        echo $this->twig->render('play/users.phtml', array('poker' => $poker, 'users' => json_encode($users)));
    }

    public function post($idPoker)
    {
        try {
            $data = array();
            if ($idPoker) {
                $dataPost = $_POST;
                $this->_model->addUserForPoker($dataPost, $idPoker);
                $data = array(
                    'success' => array(
                        'message' => 'Users added successful',
                        'code' => 202
                    )
                );
            }
            Functions::headerHttpCode(202);
            Functions::renderJson($data);
        } catch (\Exception $e) {
            Functions::headerException($e);
        }
    }

    public function delete($idPoker)
    {
        try {
            $data = array();
            if ($idPoker) {
                $dataPut = Functions::dataPut();
                $this->model->removeUser($dataPut, $idPoker);
                $data = array(
                    'success' => array(
                        'message' => 'Users added successful',
                        'code' => 202
                    )
                );
            }
        } catch (\Exception $e) {
            Functions::headerException($e);
        }
    }
}
