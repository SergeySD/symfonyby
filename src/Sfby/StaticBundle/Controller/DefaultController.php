<?php

namespace Sfby\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('SfbyStaticBundle:Default:index.html.twig', array());
    }
}
