<?php

namespace Sfby\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sfby\UserBundle\Entity\User;

use MakerLabs\PagerBundle\Pager;
use MakerLabs\PagerBundle\Adapter\DoctrineOrmAdapter;

class DefaultController extends Controller
{
    protected $per_page = 5;
    /**
     * @Route("/", name="user_index")
     * @Route("/{page}", defaults={"page"=1}, requirements={"page"="\d+"},  name="user_index")
     * @Template()
     */
    public function indexAction($page = 1)
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\UserBundle\Entity\User');
        $qb = $rep->createQueryBuilder('f');
        $adapter = new DoctrineOrmAdapter($qb);
        $pager = new Pager($adapter, array('page' => $page, 'limit' => $this->per_page));
        return array('pager' => $pager);
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
    
    /**
     */
    public function checkFacebookAction()
    {
        
    }

}
