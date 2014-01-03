<?php

namespace TrelloPoker\Controller;

use \Respect\Rest\Routable,
    \Respect\Validation\Validator as v,
    \Respect\Relational\Mapper,
    \Respect\Relational\Db,
    \Respect\Config\Container,
    \Twig_Environment;

class BaseController implements Routable
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
    
    /**
     *
     * @var \Respect\Config\Container
     */
    protected $_config;
    
    /**
     *
     * @var Twig_Environment
     */
    protected $_twig;
    
    /**
     *
     * @var \TrelloPoker\Model\BaseModel
     */
    protected $_model;           
    
    
    public function __construct(\Respect\Relational\Mapper $mapper, 
            \Respect\Relational\Db $db,
            \Respect\Config\Container $config,
            Twig_Environment $twig) 
    {
        $this->_mapper = $mapper;
        $this->_db = $db;
        $this->_config = $config;
        $this->_twig = $twig;
        if ($this->_model) {
            $modelName = $this->_model;
            $modelInstance = new $modelName($this->_mapper, $this->_db);
            $this->_model = $modelInstance;
        }        
    }
    
}

