<?php

namespace TrelloPoker\Controller;

class HomeController extends BaseController 
{
    
    public function get()
    {
        echo $this->_twig->render('home/index.phtml');
    }    
}

