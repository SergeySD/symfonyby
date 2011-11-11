<?php

namespace Sfby\BlogBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sfby\BlogBundle\Entity\Blog;
use FOS\UserBundle\Model\UserManagerInterface;

class BlogAdmin extends Admin
{

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
                ->addIdentifier('title')
                ->add('category')
                ->add('user')
                ->add('tags')
                ->add('updatedAt')
        ;
    }

    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('category')
            ->add('user')
            ->add('short_text')
            ->add('tags')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('title')
                ->add('short_text')
                ->add('text')
                ->add('user', 'sonata_type_admin', array(), array('edit' => 'list'))
            ->end()
            ->with('Tags', array('collapsed' => true))
                ->add('tags', 'sonata_type_immutable_array')
            ->end()
        ;
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('user')
        ;
    }
    
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function getUserManager()
    {
        return $this->userManager;
    }

//    protected function configureSideMenu(MenuItemInterface $menu, $action, Admin $childAdmin = null)
//    {
//        if (!$childAdmin && !in_array($action, array('edit'))) {
//            return;
//        }
//
//        $admin = $this->isChild() ? $this->getParent() : $this;
//
//        $id = $admin->getRequest()->get('id');
//
//        $menu->addChild(
//            $this->trans('view_blog'),
//            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
//        );
//    }
}
