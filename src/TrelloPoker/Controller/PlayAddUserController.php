<?php

namespace TrelloPoker\Controller;

use \TrelloPoker\Functions,
   \Respect\Validation\Validator as v,
   \TrelloPoker\Model\Poker;

class PlayAddUserController extends BaseController
{
    /**
     *
     * @var \TrelloPoker\Model\Poker 
     */
    protected $_model = 'TrelloPoker\Model\Poker';
    
    public function get($idPoker)
    {   
        $data = $this->_mapper->membro()->poker[$idPoker]->fetchAll(); 
        $users = array();
        $poker = new \stdClass();
        foreach ($data as $user) {
            if (!isset($poker->id))
                $poker = $user->poker_id;
            $users[] = $user->member_id;
        }
        echo $this->_twig->render('play/users.phtml', array('poker' => $poker, 'users' => json_encode($users)));
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
						'message' => 'Usuários adicionados ao game com sucesso',
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
				$this->_model->removeUser($dataPut, $idPoker);
				$data = array(
					'success' => array(
						'message' => 'Usuários adicionados ao game com sucesso',
						'code' => 202
					)
				);
			}
		} catch (\Exception $e) {
			Functions::headerException($e);
		}
	}
}

