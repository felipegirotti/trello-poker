<?php

namespace TrelloPoker\Model;

class Poker extends BaseModel 
{
    
    public function insertPoker(array $data)
    {
        $conn = $this->_db->getConnection();
        try {
            $conn->beginTransaction();
            // inserindo o poker
            $poker = array(
                'nome' => $data['nome'],
                'member_id' => $data['user-id'],
                'board_id' => $data['board-id'],
                'created_at' => date('Y-m-d H:i:s'),
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

            $data['member'][] = $data['user-id'];
            // inserindo os membros 
            foreach ($data['member'] as $member) {
                $dataMember = array(
                    'poker_id' => $poker['id'],
                    'member_id' => $member
                );
                $this->_db->insertInto('membro', $dataMember)
                        ->values($dataMember)
                        ->exec();
            }
            $conn->commit();

            $response = array(
                'success' => array(
                    'message' => 'Poker "'. $poker['nome'] .'" inserido com sucesso, link /poker/planning/' . $poker['id']
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
    
}