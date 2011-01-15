<?php

namespace Application\HelloBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HelloController extends Controller
{
    public function indexAction($name)
    {
        $event = new \Application\HelloBundle\Entity\Event();
        $event->setName($name);

        $em = $this->get('doctrine.orm.entity_manager');
        $repo = $em->getRepository('HelloBundle:Event');
        $event = $repo->findOneByName($name);
        var_dump($event);
        var_dump($event->getCreatedAt());

        return $this->render('HelloBundle:Hello:index.twig', array('name' => $name));

        // render a PHP template instead
        // return $this->render('HelloBundle:Hello:index.php', array('name' => $name));
    }
}
