<?php

namespace Sfby\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;

use Sfby\BlogBundle\Entity\Category;
use Sfby\BlogBundle\Entity\Blog;
use Sfby\BlogBundle\Entity\Tag;

use Sfby\BlogBundle\Form\BlogType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     * @Template()
     */
    public function indexAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Blog');
        $blogs = $rep->findAll();
        return array(
            'blogs' => $blogs,
        );
    }
    
    /**
     * @Route("/{slug}", name="blog_category")
     * @Template("SfbyBlogBundle:Default:categoryList.html.twig")
     */

    public function categoryAction(Category $category)
    {
        return array(
            'category' => $category,
            'blogs' => $category->getBlogs()
        );
    }
    
    /**
     * @Route("/tag/{slug}", name="blog_by_tag")
     * @Template("SfbyBlogBundle:Default:tagList.html.twig")
     */

    public function tagAction(Tag $tag)
    {
        return array(
            'tag' => $tag,
            'blogs' => $tag->getBlogs()
        );
    }
    
    /**
     * @Route("/view/{slug}", name="blog_view")
     * @Template
     */

    public function viewAction(Blog $blog)
    {
        return array(
            'blog' => $blog,
        );
    }
    
    /**
     * @Route("/edit/{id}", name="blog_edit")
     * @Template
     */

    public function editAction(Blog $blog)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $request = $this->getRequest();
        $form = $this->createForm(new BlogType(), $blog);
        
        $request = $this->getRequest();
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($blog);
                $em->flush();
            }
        }
        
        return array(
            'blog' => $blog,
            'form' => $form->createView(),
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function blogCommentsAction(Blog $blog)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            'blog' => $blog,
            'user' => $user
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function submenuAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Category');
        
        return array(
            'categories' => $rep->findAll(),
            'active' => $this->getRequest()->get('active'),
        );
    }
    
    /**
     * component
     * 
     * @Template("SfbyBlogBundle:Default:list.html.twig")
     */
    public function recentAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Blog');
        return array(
            'blogs' => $rep->findAll(),
            'tag'=>null,
            'category'=>null
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function tagCloudAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Tag');
        $arr = $rep->findForCloud();
        return array(
            'tags' => $arr,
        );
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
    public function userBlogsAction($user)
    {
        $blogs = $user->getBlogs();
        return array(
            'blogs' => $blogs,
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
