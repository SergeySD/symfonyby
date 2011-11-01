<?php

namespace Sfby\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="blog_index")
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
    public function listAction()
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
    
    /**
     * component
     * 
     * @Template
     */
    public function userBlogsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function userCommentsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function userRatesAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            
        );
    }
}
