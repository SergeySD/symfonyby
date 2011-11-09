<?php

namespace Sfby\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sfby\BlogBundle\Entity\Tag;

class LoadTags extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        $arr = array('Стив Джобс', 'Указ № 60', 'Украина', 'Хакеры', 'Электронная Беларусь', 'Юрий Зиссер', 'Яндекс', 'акции', 'антивирус', 'аутсорсинг', 'байнет', 'безопасность', 'блог', 'браузер', 'вакансии', 'встреча', 'дизайн');
        foreach ($arr as $i=>$name)
        {
            $tag = new Tag();
            $tag->setName($name);
            $manager->persist($tag);
            $this->addReference('tag'.$i, $tag);
        }
        $manager->flush();
    }
    public function getOrder()
    {
        return 30; // the order in which fixtures will be loaded
    }
}
