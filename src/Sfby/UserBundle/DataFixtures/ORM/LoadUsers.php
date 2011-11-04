<?php

namespace Sfby\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sfby\UserBundle\Entity\User;

class LoadUsers extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        $um = $this->container->get('fos_user.user_manager');
        
        $admin = new User();
        $admin->setAlgorithm('sha512');
        $admin->setUsername('admin');
        $admin->setUsernameCanonical('admin');
        $admin->setEmail('admin@test.com');
        $admin->setEmailCanonical('admin@test.com');
        $admin->setName('Admin');
        $admin->setAbout('One for all and all for one, Muskehounds are always ready. One for all and all for one, helping everybody. One for all and all for one, it\'s a pretty story. Sharing everything with fun, that\'s the way to be. One for all and all for one, Muskehounds are always ready. One for all and all for one, helping everybody. One for all and all for one, can sound pretty corny. If you\'ve got a problem chum, think how it could be.');
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setEnabled(true);
        $admin->setPlainPassword('admin');
        $um->updatePassword($admin);
        
        $user = new User();
        $user->setAlgorithm('sha512');
        $user->setUsername('user');
        $user->setUsernameCanonical('user');
        $user->setEmail('user@test.com');
        $user->setEmailCanonical('user@test.com');
        $user->setName('New User');
        $user->setAbout('80 days around the world, we\'ll find a pot of gold just sitting where the rainbow\'s ending. Time - we\'ll fight against the time, and we\'ll fly on the white wings of the wind. 80 days around the world, no we won\'t say a word before the ship is really back. Round, round, all around the world. Round, all around the world. Round, all around the world. Round, all around the world.');
        $user->setRoles(array());
        $user->setEnabled(true);
        $user->setPlainPassword('user');
        $um->updatePassword($user);

        $manager->persist($admin);
        $manager->persist($user);
        $manager->flush();
        
        $this->addReference('admin', $admin);
        $this->addReference('user', $user);
    }
    public function getOrder()
    {
        return 10; // the order in which fixtures will be loaded
    }

}
