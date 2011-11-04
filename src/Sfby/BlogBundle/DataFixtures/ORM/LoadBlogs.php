<?php

namespace Sfby\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sfby\BlogBundle\Entity\Blog;
use Sfby\BlogBundle\Entity\Category;
use Sfby\UserBundle\Entity\User;

class LoadBlogs extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        $rep = $manager->getRepository('Sfby\BlogBundle\Entity\Blog');
        
        $blog = new Blog();
        $blog->setName('Информационная безопасность');
        $blog->setText('Доброго всем времени суток! Сразу хотелось бы дать две ссылки на материалы, на основе которых составлена эта заметка. Можно ознакомиться непосредственно с источниками и не читать топик, представляющий из себя всего-навсего мой вольный перевод-пересказ основных моментов с небольшим количеством отсебятины.');
        $blog->setUser($manager->merge($this->getReference('admin')));
        $blog->setCategory($manager->merge($this->getReference('category1')));
        $manager->persist($blog);
        $manager->flush();
        
        $blog = $rep->findOneBy(array('name' => 'Информационная безопасность'));
        $blog->setName('Information Security');
        $blog->setText('Good time of day to all! Immediately I would like to give two references to the material on which this notice is made. Can be accessed directly from the source and do not read topic, representing a mere my free translation-paraphrase the main points with a little ad-libbing.');
        $blog->setLocale('en_us'); 
        $manager->persist($blog);
        $manager->flush();
        
        $this->addReference('blog1', $blog);
        
        
        $blog = new Blog();
        $blog->setName('HP оставит бизнес WebOS у себя, разделив его на два подразделения');
        $blog->setText('Туманная ситуация относительно будущего мобильной операционной системы WebOS, находящейся в ведении Hewlett-Packard, способствует обрастанию слухами: вполне авторитетные агенства пророчили продажу WebOS либо её лицензирование ряду компаний, начиная от южно-корейской Samsung и заканчивая Facebook и HTC. Вчера глава Samsung опроверг слухи относительно планов покупки WebOS для своей компании, резонно заметив, что операционных систем у них хватает — Android, Windows Phone 7 и собственная платформа Bada. Сегодня возникла новая инсинуация. Сайт Precental.net опубликовал пост о том, что в их распоряжении оказалось внутренние документы Hewlett-Packard из которых следует, что подразделение WebOS планируется разделить на два — «софтверное», которое станет частью отдела Стратегии и Технологии (Office of Strategy and Technology) и «железное», которое планируется сделать частью Personal Systems Group, занимающее PC-бизнесом Hewlett-Packard. ');
        $blog->setUser($manager->merge($this->getReference('user')));
        $blog->setCategory($manager->merge($this->getReference('category1')));
        $manager->persist($blog);
        $manager->flush();
        
        $blog = $rep->findOneBy(array('name' => 'HP оставит бизнес WebOS у себя, разделив его на два подразделения'));
        $blog->setName('HP WebOS leave business at home, dividing it into two divisions');
        $blog->setText('Misty situation regarding the future of the mobile operating system WebOS, which is run by Hewlett-Packard, contributes to fouling rumors: it is authoritative agency predicted WebOS sale or licensing of a number of companies, ranging from the South Korean Samsung and ending with Facebook and HTC. Yesterday the head of Samsung denied rumors about plans to buy WebOS for the company, it is reasonable noting that the operating systems they have enough - Android, Windows Phone 7 and its own platform Bada. Today, a new innuendo. Website Precental.net published a post about what was in their possession internal documents from Hewlett-Packard which it follows that the unit WebOS will split into two - "software", which will become part of the Strategy and Technology (Office of Strategy and Technology) and "iron", which is scheduled to be part of Personal Systems Group, holding the PC-business Hewlett-Packard.');
        $blog->setLocale('en_us'); 
        $manager->persist($blog);
        $manager->flush();
        
        $this->addReference('blog2', $blog);
        
        
        $blog = new Blog();
        $blog->setName('Оплата счета в барах KillFish');
        $blog->setText('Мы давно планировали выход в офлайн и вот, свершилось. Теперь счет в барах сети KillFish (Питер) можно оплатить при помощи QIWI Кошелька. Механика очень проста: называете бармену номер вашего мобильного телефона, через мгновение на него приходит USSD запрос с суммой счета. Нажав 1, вы оплачиваете счет: деньги списываются с вашего QIWI Кошелька.');
        $blog->setUser($manager->merge($this->getReference('user')));
        $blog->setCategory($manager->merge($this->getReference('category1')));
        $manager->persist($blog);
        $manager->flush();
        
        $blog = $rep->findOneBy(array('name' => 'Оплата счета в барах KillFish'));
        $blog->setName('Payment of bills in bars KillFish');
        $blog->setText('We have long planned out in offline and now, it has happened. Now, through a network of bars KillFish (Peter) can be paid by QIWI purse. The mechanics are very simple: call the bartender your mobile phone number, in a moment it comes USSD request with the bill. By pressing 1, you pay the bill, money deducted from your QIWI purse.');
        $blog->setLocale('en_us'); 
        $manager->persist($blog);
        $manager->flush();
        
        $this->addReference('blog3', $blog);
        
        
        $blog = new Blog();
        $blog->setName('JavaScript как язык с утиной типизацией');
        $blog->setText('JavaScript это мощный язык с динамической типизацией и это позволяет писать код с утиной типизацией. В двух словах утиная типизация позволяет выполнять функцию для обьектов которые обладают нужными нам свойствами (т.к. js функицональный язык то и метод является полноправным свойством). Я уверен что многие используют утиную типизацию, но я думаю данная заметка будет все равно интересна некоторому подмножеству множества аудитории хабра. В этой заметке мы проследим эволюцию функции range из работающей только с числами до функции которая работает с любыми обьектами.');
        $blog->setUser($manager->merge($this->getReference('user')));
        $blog->setCategory($manager->merge($this->getReference('category2')));
        $manager->persist($blog);
        $manager->flush();
        
        $blog = $rep->findOneBy(array('name' => 'JavaScript как язык с утиной типизацией'));
        $blog->setName('JavaScript as a language with duck typing');
        $blog->setText('JavaScript is a powerful language with dynamic typing, and it allows you to write code with duck typing. In a nutshell, duck typing allows you to perform a function for objects that possess the desired properties (as js funkitsonalny language and method that is a full feature). I am sure that many people use duck typing, but I think this article will be still interested in a subset of the audience Habra. In this article we trace the evolution of the range of working only with numbers to a function that works with any kind of objects.');
        $blog->setLocale('en_us'); 
        $manager->persist($blog);
        $manager->flush();
        
        $this->addReference('blog4', $blog);
        
        
    }
    public function getOrder()
    {
        return 30; // the order in which fixtures will be loaded
    }

}
