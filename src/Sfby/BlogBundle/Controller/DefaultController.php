<?php

namespace Sfby\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function tagCloudAction()
    {
        return array();
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function newUsersAction()
    {
        return array();
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function lastCommentsAction()
    {
        return array();
    }
}
