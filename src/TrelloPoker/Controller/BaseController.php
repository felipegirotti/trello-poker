<?php

namespace TrelloPoker\Controller;

use \Respect\Rest\Routable;
use \Respect\Validation\Validator as v;
use \Respect\Relational\Mapper;
use \Respect\Relational\Db;
use \Respect\Config\Container;
use \Twig_Environment;

class BaseController implements Routable
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
     * @var \Respect\Config\Container
     */
    protected $config;
    
    /**
     *
     * @var Twig_Environment
     */
    protected $twig;
    
    /**
     *
     * @var \TrelloPoker\Model\BaseModel
     */
    protected $model;
    
    
    public function __construct(
        \Respect\Relational\Mapper $mapper,
        \Respect\Relational\Db $db,
        \Respect\Config\Container $config,
        Twig_Environment $twig
    ) {
        $this->mapper = $mapper;
        $this->db = $db;
        $this->config = $config;
        $this->twig = $twig;
        if ($this->model) {
            $modelName = $this->model;
            $modelInstance = new $modelName($this->mapper, $this->db, $this->config);
            $this->model = $modelInstance;
        }
    }
    
}

