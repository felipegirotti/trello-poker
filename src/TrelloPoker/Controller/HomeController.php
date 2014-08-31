<?php

namespace TrelloPoker\Controller;

class HomeController extends BaseController
{
    
    public function get()
    {
        echo $this->twig->render('home/index.phtml');
    }
}
