<?php

namespace Sfby\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Sfby\BlogBundle\Entity\Category;
use Sfby\BlogBundle\Entity\Blog;
use Sfby\BlogBundle\Entity\Tag;

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
            'tag'=>null,
            'category'=>null
        );
    }
    
    /**
     * @Route("/{slug}", name="blog_category")
     * @Template("SfbyBlogBundle:Default:index.html.twig")
     */

    public function categoryAction(Category $category)
    {
        return array(
            'category' => $category,
            'blogs' => $category->getBlogs(),
            'tag'=>null
        );
    }
    
    /**
     * @Route("/tag/{slug}", name="blog_by_tag")
     * @Template("SfbyBlogBundle:Default:index.html.twig")
     */

    public function tagAction(Tag $tag)
    {
        return array(
            'tag' => $tag,
            'blogs' => $tag->getBlogs(),
            'category'=>null
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
