<?php

namespace Sfby\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sfby\DefaultBundle\Entity\Product;
use Sfby\DefaultBundle\Entity\Category;
use Sfby\DefaultBundle\Form\ProductType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;


class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Route("/{_locale}", name="locale", defaults={"_locale"="0"}, requirements={"_locale"="en|ru"})
     * @Template()
     */
    public function indexAction()
    {
        $r = $this->getRequest();
        if ($r->get('_locale'))
        {
            $this->get('session')->setLocale($r->get('_locale'));
            return new RedirectResponse($this->container->get('router')->generate('homepage'));
        }
        return array();
    }
    
    /**
     * header component
     * 
     * @Template
     */
    public function headerAction()
    {
//        var_dump($this->getRequest()->get('request'));
        $route = $this->getRequest()->get('request')->get('_route');
        $controller = $this->getRequest()->get('request')->get('_controller');

        $active = '';
        $subactive = '';
        if (stripos($controller, 'BlogBundle'))
        {
            $active = 'blogs';
        }
        return array(
            'user' => $this->get('security.context')->getToken()->getUser(),
            'active' => $active,
            'subactive' => $subactive,
        );
    }
    /**
     * footer component
     * 
     * @Template
     */
    public function footerAction()
    {
        return array();
    }

//    /**
//     * @Route("/test", name="test")
//     */
//    public function testAction()
//    {
//        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
//        {
//            throw new AccessDeniedException();
//        }
//
//        return new Response('Open');
//    }
//
//
//    /**
//     * @Route("/create", name="create")
//     * @Secure(roles="ROLE_ADMIN")
//     */
//    public function createAction()
//    {
//        $em = $this->getDoctrine()->getEntityManager();
//        for ($a = 0; $a < 10; $a++)
//        {
//            $c = new Category();
//            $c->setName('Categiry ' . $a);
//            $em->persist($c);
//
//            $p = new Product();
//            $p->setName('test ' . $a);
//            $p->setPrice(10 * $a);
//            $p->setDescription('Lorem ipsum dolor');
//            $p->setCategory($c);
////            $p->setCategoryId(1);
//            $em->persist($p);
//        }
//        $em->flush();
//        return $this->forward('SfbyStaticBundle:Content:list');
//    }
//
//    /**
//     * @Route("/list", name="list")
//     * @Route("/{_locale}/list", name="list_loc", defaults={"_locale"="0"}, requirements={"_locale"="en|ru"})
//     * @Template
//     */
//    public function listAction()
//    {
//        $r = $this->getRequest();
//        if ($r->get('_locale')) $this->get('session')->setLocale($r->get('_locale'));
//        
//        $dql = "SELECT p,c FROM SfbyStaticBundle:Product p JOIN p.category c ";
//        $products = $this->getDoctrine()->getEntityManager()->createQuery($dql);
//        $products->setMaxResults(10);
//        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
//        {
//            $products->setMaxResults(30);
//        }
////        $products = $this->getDoctrine()->getRepository('SfbyStaticBundle:Product')
////                ->createQueryBuilder('p')
//////                ->innerJoin('p', 'Category', 'c', 'p.category_id=c.id')
////                ->setMaxResults(20)
////                ->getQuery()->getResult();
//
//        return array(
//            'products' => $products->getResult(),
//            'service' => $this->get('test')->get()
//        );
//    }
//
//    /**
//     * @Route("/edit/{id}", name="edit")
//     * @Secure(roles="ROLE_ADMIN")
//     * @Template
//     */
//    public function editAction(Request $request)
//    {
//        $id = $request->get('id');
//        $em = $this->getDoctrine()->getEntityManager();
//        $product = $em->getRepository('SfbyStaticBundle:Product')->find($id);
//
//        if (!$product)
//        {
//            throw $this->createNotFoundException('No product found for id ' . $id);
//        }
//
//        $form = $this->createForm(new ProductType(), $product);
//
//        if ($request->getMethod() == 'POST')
//        {
//            $form->bindRequest($request);
//            if ($form->isValid())
//            {
////                $em->persist($task);
//                $em->flush();
//                return $this->redirect($this->generateUrl('list'));
//            }
//        }
//
//        return array('form' => $form->createView(),
//            'error' => $form->getErrors(),
//            'product' => $product,
//        );
//    }
//
//    /**
//     * @Route("/delete/{id}", name="delete")
//     * @Secure(roles="ROLE_ADMIN")
//     */
//    public function deleteAction($id)
//    {
//        $em = $this->getDoctrine()->getEntityManager();
//        $product = $em->getRepository('SfbyStaticBundle:Product')->find($id);
//
//        if (!$product)
//        {
//            throw $this->createNotFoundException('No product found for id ' . $id);
//        }
//
//        $em->remove($product);
//        $em->flush();
//
//        return $this->forward('SfbyStaticBundle:Content:list');
//    }
//
//    /**
//     * @Route("/deleteAll", name="deleteAll")
//     * @Secure(roles="ROLE_ADMIN")
//     */
//    public function deleteAllAction()
//    {
//        $em = $this->getDoctrine()->getEntityManager();
//        $arr = $em->getRepository('SfbyStaticBundle:Product')->findAll();
//        foreach ($arr as $product)
//        {
//            $em->remove($product);
//        }
//
//        $arr = $em->getRepository('SfbyStaticBundle:Category')->findAll();
//        foreach ($arr as $category)
//        {
//            $em->remove($category);
//        }
//        $em->flush();
//
//        return $this->forward('SfbyStaticBundle:Content:list');
//    }
//
//    /**
//     * @Route("/details/{id}", name="details")
//     * @Template
//     */
//    public function detailsAction($id)
//    {
//        $em = $this->getDoctrine()->getEntityManager();
//        $cat = $em->getRepository('SfbyStaticBundle:Category')->find($id);
//
//        $products = $cat->getProducts();
//
//        return array('products' => $products);
//    }

}
