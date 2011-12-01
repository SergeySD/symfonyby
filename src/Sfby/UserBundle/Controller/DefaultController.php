<?php

namespace Sfby\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sfby\UserBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function newUsersAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\UserBundle\Entity\User');
        $users = $rep->findBy(array(), array('createdAt' => 'DESC'), 10);
        return array(
            'users' => $users,
        );
    }
    
    /**
     * @Route("/view/{id}", name="profile_view")
     * @Template("SfbyUserBundle:Profile:view.html.twig")
     */

    public function viewAction(User $user)
    {
        return array(
            'user' => $user,
        );
    }
}
