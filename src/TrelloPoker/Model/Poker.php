<?php

namespace TrelloPoker\Model;

use Respect\Validation\Validator as v;

class Poker extends BaseModel
{
    const STATUS_ATIVO = 1;
    const STATUS_INATIVO = 0;
    
    public function insertPoker(array $data)
    {
        $conn = $this->db->getConnection();
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
            $this->db->insertInto('poker', $poker)
                    ->values($poker)
                    ->exec();
            $poker['id'] = $conn->lastInsertId();

            // insert  cards
            foreach ($data['card'] as $card) {
                $dataCard = array(
                    'poker_id' => $poker['id'],
                    'card_id' => $card,
                    'pontuacao' => 0,
                    'status' => self::STATUS_ATIVO
                );
                $this->db->insertInto('card', $dataCard)
                        ->values($dataCard)
                        ->exec();
            }
            
            $totalMembers = count($data['member']);
            $data['member'][$totalMembers] = '{"id" : "'
                . $data['user-id']
                . '", "name" : "'
                . $data['user-name']
                . '"}';
            //insert members
            $this->addMember($data['member'], $poker['id']);
            $conn->commit();
            $link = '/poker/play/' . base64_encode($poker['id'] . '|' . $dateNow);
            $response = array(
                'success' => array(
                    'message' => 'Game "'
                        . $poker['nome'] .
                        '" created successful, shared the link of the members <a href="'. $link .'">' . $link . '</a>'
                )
            );
            
            return $response;

        } catch (\Exception $e) {
            $conn->rollback();
            throw new \Exception('Houve um erro durante a inserção do poker');
        }
    }

    /**
	 * Add members ao card
	 * 
	 * @param array $dataMembers Dados dos membros
	 * @param integer $idPoker ID do poker game
	 * @return boolean
	 */
    private function addMember(array $dataMembers, $idPoker)
    {
        foreach ($dataMembers as $value) {
            $member = json_decode($value);
            $dataMember = array(
                'poker_id' => $idPoker,
                'member_id' => $member->id,
                'fullname' => $member->name,
                'logged' => self::STATUS_INATIVO,
            );
            $this->db->insertInto('membro', $dataMember)
                ->values($dataMember)
                ->exec();
        }
        return true;
    }
    
    public function myPokers($memberId)
    {
        $conn = $this->db->getConnection();
        
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
            'cards_finish' => array(),
            'votes' => array()
        );
        $data['card'] = $this->mapper->card(array('poker_id' => $id, 'status' => self::STATUS_ATIVO))->fetch();
        $data['users'] = $this->mapper->membro(array('poker_id' => $id))->fetchAll();
        $data['poker'] = $this->mapper->poker[$id]->fetch();
        $data['cards'] = $this->mapper->card(array('poker_id' => $id))->fetchAll();
        return $data;
    }
    
    public function updateLoggedUser(array $data)
    {
        $validation = v::arr()
                    ->key('member_id', v::string()->notEmpty())
                    ->key('poker_id', v::string()->notEmpty())
                    ->validate($data);
        if (! $validation) {
            throw new \Exception('Validation, fields member_id, poker_id');
        }

        $membro = $this->mapper->membro(
            array('poker_id' => $data['poker_id'], 'member_id' => $data['member_id'])
        )->fetch();

        if ($membro) {
            $membro->logged = 1;
            $membro->update_page = 0;
            $membro->updated_at = date('Y-m-d H:i:s');
            $this->mapper->membro->persist($membro);
            $this->mapper->flush();
        }
    }
    
    public function getMembersAndPontuacao(array $data)
    {
        $this->conn= $this->db->getConnection();
       
        $sql = "SELECT mhc.*, mem.id, mem.member_id, mem.fullname, mem.updated_at, mem.logged, mem.update_page "
                . " FROM membro mem "
                . "  LEFT JOIN membro_has_card mhc ON mem.id = mhc.membro_id AND mhc.card_id = ? "
                . " WHERE  "
                . " mem.poker_id = ? ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array($data['card_id'], $data['poker_id']));

        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    
    public function getUsersOfPoker(array $data)
    {
        $newUsers = array();
        $users = $this->getMembersAndPontuacao($data);
        if ($users) {
            foreach ($users as $user) {
                if ($user->member_id != $data['member_id']) {
                    $newUsers[] = $this->checkIsLogged($user, $data['poker_id']);
                } else {
                    $user->logged = 1;
                    $newUsers[] = $user;
                }
            }
        }
        $card = $this->mapper->card[$data['card_id']]->fetch();
        $response = array(
            'users' => $newUsers,
            'pontuacao' => $card->pontuacao,
            'status' => $card->status
        );
        $this->updateLoggedUser($data);

        return $response;
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
        $membro->logged = $membro->logged ?: 0;
        if ($interval > 60) {
            $membro->logged = 0;
            $user = $this->mapper->membro[$membro->id]->fetch();
            $user->logged = 0;
            $this->mapper->membro->persist($user);
            $this->mapper->flush();
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
        $this->mapper->membro_has_card->persist($membroHasCard);
        $this->mapper->flush();
    }
    
    private function getMembro(array $where)
    {
        return $this->mapper->membro($where)->fetch();
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
        if (! $validate) {
            throw new \InvalidArgumentException('Parâmetros inválidos');
        }
        
        $this->isOwnerGame($data['card_id'], $data['member_id']);
        $card = $this->mapper->card[$data['card_id']]->fetch();
        $card->pontuacao = $data['pontuacao'];
        $card->updated_at = date('Y-m-d H:i:s');
        $card->status = self::STATUS_INATIVO;
        $this->mapper->card->persist($card);
        $this->mapper->flush();

        return $card->card_id;
    }
    
    private function isOwnerGame($cardId, $memberId)
    {
        $card = $this->mapper
                    ->card[$cardId]
                    ->poker()
                    ->fetch();
        if ($card->poker_id->member_id != $memberId) {
            throw new \InvalidArgumentException('Usuário não é dono do game para finalizar');
        }

        return true;
    }
    
    public function regame(array $data)
    {
        $validate = v::arr()
                        ->key('card_id', v::string()->notEmpty())
                        ->key('poker_id', v::string()->notEmpty())
                        ->key('member_id', v::string()->notEmpty());
        if (! $validate) {
            throw new Exception('Parâmetros inválidos');
        }
        
        $this->isOwnerGame($data['card_id'], $data['member_id']);
        
        $membroHasCards = $this->mapper->membro_has_card(array('card_id' => $data['card_id']))->card()->fetchAll();
        try {
            $this->conn = $this->db->getConnection();
            $this->conn->beginTransaction();
            foreach ($membroHasCards as $card) {
                $stmt = $this->conn->prepare('DELETE FROM membro_has_card WHERE card_id = ?');
                $stmt->execute(array($card->card_id->id));
                $stmt = $this->conn->prepare('UPDATE membro SET update_page = 1 WHERE poker_id = ?');
                $stmt->execute(array($card->card_id->poker_id));
            }
            $this->conn->commit();

            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw new \Exception('Ops! There was an error regame, try again');
        }
            
    }

    /**
	 * API publica para adicionar membros ao game
	 * 
	 * @param array $data Dados para inserção dos membros ao game
	 * @param integer $idPoker ID do poker
	 * @return boolean
	 * @throws \InvalidArgumentException
	 */
    public function addUserForPoker(array $data, $idPoker)
    {
        $poker = $this->mapper->poker[$idPoker]->fetch();

        $this->validateUserOwner($poker, $data['member_id']);

        return $this->addMember($data['member'], $idPoker);
    }

    public function removeUser(array $data, $idPoker)
    {
        $poker = $this->_mapper->poker[$idPoker]->fetch();

        $this->validateUserOwner($poker, $data['owner']);

        $member = $this->_mapper->membro(array('member_id' => $data['member_id'], 'poker_id' => $idPoker))->fetch();
        if (!$member) {
            throw new \InvalidArgumentException('Member not found');
        }

        $this->mapper->membro->remove($member);
        $this->mapper->flush();

        return true;
    }

    private function validateUserOwner($poker, $memberId)
    {
        if (!$poker) {
            throw new \InvalidArgumentException('Number of the game not found');
        }

        if ($poker->member_id != $memberId) {
            throw new \InvalidArgumentException('You not owner of the game');
        }
    }
}
