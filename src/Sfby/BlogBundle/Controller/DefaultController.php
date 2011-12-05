<?php

namespace Sfby\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;

use Sfby\BlogBundle\Entity\Category;
use Sfby\BlogBundle\Entity\Blog;
use Sfby\BlogBundle\Entity\Tag;

use Sfby\BlogBundle\Form\BlogType;

use MakerLabs\PagerBundle\Pager;
use MakerLabs\PagerBundle\Adapter\DoctrineOrmAdapter;


class DefaultController extends Controller
{
    protected $per_page_recent = 5;
    protected $per_page = 5;
    /**
     * @Route("/", name="blog_index")
     * @Route("/{page}", defaults={"page"=1}, requirements={"page"="\d+"},  name="blog_index")
     * @Template()
     */
    public function indexAction($page = 1)
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Blog');
        $qb = $rep->createQueryBuilder('f');
        $adapter = new DoctrineOrmAdapter($qb);
        $pager = new Pager($adapter, array('page' => $page, 'limit' => $this->per_page));
        return array('pager' => $pager);
    }
    
    /**
     * @Route("/{slug}", name="blog_category")
     * @Route("/{slug}/{page}", defaults={"page"=1}, requirements={"page"="\d+"},  name="blog_category")
     * @Template("SfbyBlogBundle:Default:categoryList.html.twig")
     */

    public function categoryAction(Category $category, $page = 1)
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Blog');
        $qb = $rep->createQueryBuilder('f');
        $qb->where('f.category = ?1')->setParameter(1, $category);
        $adapter = new DoctrineOrmAdapter($qb);
        $pager = new Pager($adapter, array('page' => $page, 'limit' => $this->per_page));
        return array(
            'pager' => $pager,
            'category' => $category,
            );
    }
    
    /**
     * @Route("/tag/{slug}", name="blog_by_tag")
     * @Route("/tag/{slug}/{page}", defaults={"page"=1}, requirements={"page"="\d+"},  name="blog_by_tag")
     * @Template("SfbyBlogBundle:Default:tagList.html.twig")
     */

    public function tagAction(Tag $tag, $page = 1)
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Blog');
        $qb = $rep->createQueryBuilder('f');
        $qb->where('?1 MEMBER OF f.tags')->setParameter(1, $tag);
        $adapter = new DoctrineOrmAdapter($qb);
        $pager = new Pager($adapter, array('page' => $page, 'limit' => $this->per_page));
        return array(
            'pager' => $pager,
            'tag' => $tag,
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
        $this->setFlash('notice', null);
        
        $request = $this->getRequest();
        $form = $this->createForm(new BlogType(), $blog);
       
        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Tag');
                $rep->processTags($blog);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($blog);
                $em->flush();
                $this->setFlash('notice', 'flash.item.updated');
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
        $qb = $rep->createQueryBuilder('f');
        $qb->addOrderBy('f.createdAt', 'DESC');
        $qb->setMaxResults($this->per_page_recent);
        $adapter = new DoctrineOrmAdapter($qb);
        $pager = new Pager($adapter, array('page' => 1, 'limit' => $this->per_page_recent));
        return array('pager' => $pager);
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
    public function lastCommentsAction()
    {
        return array();
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function userBlogsAction($user, $own = true)
    {
        $blogs = $user->getBlogs();
        return array(
            'blogs' => $blogs,
            'own' => $own,
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
    
    /**
     * 
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }
    
    /**
     * @Route("/tagNames/", name="tag_names")
     */
    public function tagNamesAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Tag');
        $names = $rep->getTagNamesInString("\n");
        $response = new Response($names);
        return $response;
    }
    
    /**
     * @Route("/blogTextPreview/", name="blog_text_preview")
     */
    public function blogTextPreviewAction()
    {
        $request = $this->getRequest();
        $data = '';
        if ($request->getMethod() == 'POST')
            $data = $request->request->get('data');
        return $this->render('SfbyBlogBundle:Default:textPreview.html.twig', array('data' => $data));
    }
}
