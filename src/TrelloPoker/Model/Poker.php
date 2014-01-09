<?php

namespace TrelloPoker\Model;

use Respect\Validation\Validator as v;

class Poker extends BaseModel 
{
    
    public function insertPoker(array $data)
    {
        $conn = $this->_db->getConnection();
        try {
            $dateNow = date('Y-m-d H:i:s');
            $conn->beginTransaction();
            // inserindo o poker
            $poker = array(
                'nome' => $data['nome'],
                'member_id' => $data['user-id'],
                'board_id' => $data['board-id'],
                'created_at' => $dateNow,
                'status' => 1
            );
            $this->_db->insertInto('poker', $poker)
                    ->values($poker)
                    ->exec();
            $poker['id'] = $conn->lastInsertId();

            // inserindo os cards
            foreach ($data['card'] as $card) {
                $dataCard = array(
                    'poker_id' => $poker['id'],
                    'card_id' => $card,
                    'pontuacao' => 0
                );
                $this->_db->insertInto('card', $dataCard)
                        ->values($dataCard)
                        ->exec();
            }
            
            $totalMembers = count($data['member']);
            $data['member'][$totalMembers] = $data['user-id'];
            $data['name-member'][$totalMembers] = $data['user-name'];
            // inserindo os membros 
            foreach ($data['member'] as $key => $member) {
                $dataMember = array(
                    'poker_id' => $poker['id'],
                    'member_id' => $member,
                    'fullname' => $data['name-member'][$key],
                );
                $this->_db->insertInto('membro', $dataMember)
                        ->values($dataMember)
                        ->exec();
            }
            $conn->commit();
            $link = '/poker/play/' . base64_encode($poker['id'] . '|' . $dateNow);
            $response = array(
                'success' => array(
                    'message' => 'Poker "'. $poker['nome'] .'" inserido com sucesso, link <a href="'. $link .'">' . $link . '</a>'
                )
            );
            
            return $response;

        } catch (\Exception $e) {
            $conn->rollback();            
            throw new \Exception('Houve um erro durante a inserção do poker');
        }
    }
    
    public function myPokers($memberId)
    {
        $conn = $this->_db->getConnection();
        
    }
    
    /**
     * Retorna o id
     * 
     * @param string $idCrypt
     * @return integer
     */
    public function deCryptId($idCrypt)
    {
        return (int)reset(explode('|', base64_decode($idCrypt)));       
    }
    
    public function getPlanningPoker($id)
    {
        $data = array(
            'users' => array(),
            'poker' => new \stdClass(),
            'card' => new \stdClass(),
            'votes' => array()
        );
        $data['card'] = $this->_mapper->card(array('poker_id' => $id, 'pontuacao' => '0'))->poker->fetch();        
        $data['users'] = $this->_mapper->membro(array('poker_id' => $id))->fetchAll();
        $data['poker'] = $data['card']->poker_id;
        
        return $data;
    }
    
    public function updateLoggedUser(array $data)
    {        
        $validation = v::arr()
                    ->key('member_id', v::string()->notEmpty())
                    ->key('poker_id', v::string()->notEmpty())
                    ->validate($data);
        if ( ! $validation) 
            throw new \Exception('Validação, faltando dados member_id, poker_id');

        $membro = $this->_mapper->membro(array('poker_id' => $data['poker_id'], 'member_id' => $data['member_id']))->fetch();
        if ($membro) {
            $membro->logged = 1;
            $membro->updated_at = date('Y-m-d H:i:s');
            $this->_mapper->membro->persist($membro);
            $this->_mapper->flush();
        }        
    }
    
    public function getMembersAndPontuacao(array $data)
    {
        
        $this->_conn= $this->_db->getConnection();
       
        $sql = "SELECT mhc.*, mem.id, mem.member_id, mem.fullname, mem.updated_at FROM membro mem "
                . "  LEFT JOIN membro_has_card mhc ON mem.id = mhc.membro_id AND mhc.card_id = ? "
                . " WHERE  "
                . " mem.poker_id = ? ";        
        $stmt = $this->_conn->prepare($sql);
        $stmt->execute(array($data['card_id'], $data['poker_id']));
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    
    public function getUsersOfPoker(array $data)
    {
        $newUsers = array();
        $users = $this->getMembersAndPontuacao($data);//$this->_mapper->membro(array('poker_id' => $data['poker_id']))->fetchAll();
        if ($users) {            
            foreach ($users as $user) {
                if ($user->member_id != $data['member_id']) 
                    $newUsers[] = $this->checkIsLogged($user, $data['poker_id']);
                else {
                    $user->logged = 1;
                    $newUsers[] = $user;
                }
            }
        }
        $this->updateLoggedUser($data);
        return $newUsers;
    }
    
    /**
     * Checa se não houve atualização por mais de um minuto
     * Se não houve atualização, atualiza o logged para 0
     * 
     * @param stdClass $user
     * @param integer $idPoker
     */
    private function checkIsLogged($membro, $idPoker)
    {
        $dataLastUp = new \DateTime($membro->updated_at);
        $dateNow = new \DateTime();
        $interval = $dateNow->getTimestamp() - $dataLastUp->getTimestamp();
        if ($interval > 60) {
            $membro->logged = 0;
            $user = $this->_mapper->membro[$membro->id]->fetch();
            $user->logged = 0;
            $this->_mapper->membro->persist($user);
            $this->_mapper->flush();            
        }
        return $membro;
    }
    
    public function addVote(array $data)
    {
        $membro = $this->getMembro(array('member_id' => $data['member_id'], 'poker_id' => $data['poker_id']));
        $membroHasCard = new \stdClass();
        $membroHasCard->membro_id = $membro->id;
        $membroHasCard->card_id = $data['card_id'];
        $membroHasCard->pontuacao = $data['vote'];
        $membroHasCard->created_at = date('Y-m-d H:i:s');
        $this->_mapper->membro_has_card->persist($membroHasCard);
        $this->_mapper->flush();
    }
    
    private function getMembro(array $where)
    {
        return $this->_mapper->membro($where)->fetch();
    }
    
    /**
     * Pontuando o card
     * 
     * @param array $data
     * @return string CARD_ID no Trello
     * @throws \InvalidArgumentException
     */
    public function closeCard(array $data)
    {
        $validate = v::arr()
                        ->key('member_id', v::string()->notEmpty())
                        ->key('card_id', v::string()->notEmpty())
                        ->key('pontuacao', v::string()->numeric())
                        ->validate($data);
        if ( ! $validate)
            throw new \InvalidArgumentException('Parâmetros inválidos');
        
        $card = $this->_mapper
                    ->card[$data['card_id']]
                    ->poker()
                    ->fetch();
        if ($card->poker_id->member_id != $data['member_id'])
            throw new \InvalidArgumentException('Usuário não é dono do game para finalizar');
        
        $card->pontuacao = $data['pontuacao'];        
        $this->_mapper->card->persist($card);
        $this->_mapper->flush();
        return $card->card_id;
    }
    
        
}