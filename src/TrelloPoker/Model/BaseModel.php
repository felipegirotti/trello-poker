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
    protected $mapper;
    
    /**
     *
     * @var \Respect\Relational\Db
     */
    protected $db;
    
    /**
     *
     * @var \PDO
     */
    protected $conn;

    /**
     *
     * @var \Respect\Config\Container
     */
    protected $config;
        
    public function __construct(
        \Respect\Relational\Mapper $mapper,
        \Respect\Relational\Db $db,
        \Respect\Config\Container $config
    ) {
        $this->mapper = $mapper;
        $this->db = $db;
        $this->config = $config;
    }
    
}

