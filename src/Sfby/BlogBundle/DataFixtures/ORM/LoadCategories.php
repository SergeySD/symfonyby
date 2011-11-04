<?php

namespace Sfby\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sfby\BlogBundle\Entity\Category;

class LoadCategories extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        $rep = $manager->getRepository('Sfby\BlogBundle\Entity\Category');
        
        $category = new Category();
        $category->setName('Программирование');
        $manager->persist($category);
        $manager->flush();
        
        $category = $rep->findOneBy(array('name' => 'Программирование'));
        $category->setName('Programming');
        $category->setLocale('en_us'); 
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category1', $category);
        
        $category = new Category();
        $category->setName('Оптимизация');
        $manager->persist($category);
        $manager->flush();
        
        $category = $rep->findOneBy(array('name' => 'Оптимизация'));
        $category->setName('Optimization');
        $category->setLocale('en_us'); 
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category2', $category);
        
        $category = new Category();
        $category->setName('Безопасность');
        $manager->persist($category);
        $manager->flush();
        
        $category = $rep->findOneBy(array('name' => 'Безопасность'));
        $category->setName('Security');
        $category->setLocale('en_us'); 
        $manager->persist($category);
        $manager->flush();
        $this->addReference('category3', $category);
    }
    public function getOrder()
    {
        return 20; // the order in which fixtures will be loaded
    }

}
