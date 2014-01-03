<?php

namespace TrelloPoker\Model;

use \Respect\Relational\Mapper,
    \Respect\Relational\Db;

class BaseModel 
{
    
    /**
     *
     * @var \Respect\Relational\Mapper
     */
    protected $_mapper;
    
    /**
     *
     * @var \Respect\Relational\Db
     */
    protected $_db;
        
    public function __construct(\Respect\Relational\Mapper $mapper,
        \Respect\Relational\Db $db) {
        $this->_mapper = $mapper;
        $this->_db = $db;
    }
    
}

