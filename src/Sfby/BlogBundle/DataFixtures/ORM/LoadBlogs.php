<?php

namespace Sfby\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sfby\BlogBundle\Entity\Blog;

class LoadBlogs extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        $this->manager = $manager;
        
        $blog = new Blog();
        $blog->setTitle('Информационная безопасность');
        $blog->setText('Доброго всем времени суток! Сразу хотелось бы дать две ссылки на материалы, на основе которых составлена эта заметка. Можно ознакомиться непосредственно с источниками и не читать топик, представляющий из себя всего-навсего мой вольный перевод-пересказ основных моментов с небольшим количеством отсебятины. Доброго всем времени суток! Сразу хотелось бы дать две ссылки на материалы, на основе которых составлена эта заметка. Можно ознакомиться непосредственно с источниками и не читать топик, представляющий из себя всего-навсего мой вольный перевод-пересказ основных моментов с небольшим количеством отсебятины. Доброго всем времени суток! Сразу хотелось бы дать две ссылки на материалы, на основе которых составлена эта заметка. Можно ознакомиться непосредственно с источниками и не читать топик, представляющий из себя всего-навсего мой вольный перевод-пересказ основных моментов с небольшим количеством отсебятины. Доброго всем времени суток! Сразу хотелось бы дать две ссылки на материалы, на основе которых составлена эта заметка. Можно ознакомиться непосредственно с источниками и не читать топик, представляющий из себя всего-навсего мой вольный перевод-пересказ основных моментов с небольшим количеством отсебятины.');
        $blog->setShortText('Доброго всем времени суток! Сразу хотелось бы дать две ссылки на материалы, на основе которых составлена эта заметка. Можно ознакомиться непосредственно с источниками и не читать топик, представляющий из себя всего-навсего мой вольный перевод-пересказ основных моментов с небольшим количеством отсебятины.');
        $blog->setUser($manager->merge($this->getReference('admin')));
        $blog->setCategory($manager->merge($this->getReference('category1')));
        $this->addTags($blog);
        $manager->persist($blog);
        $this->addReference('blog1', $blog);
        
        $blog = new Blog();
        $blog->setTitle('HP оставит бизнес WebOS у себя, разделив его на два подразделения');
        $blog->setText('Туманная ситуация относительно будущего мобильной операционной системы WebOS, находящейся в ведении Hewlett-Packard, способствует обрастанию слухами: вполне авторитетные агенства пророчили продажу WebOS либо её лицензирование ряду компаний, начиная от южно-корейской Samsung и заканчивая Facebook и HTC. Вчера глава Samsung опроверг слухи относительно планов покупки WebOS для своей компании, резонно заметив, что операционных систем у них хватает — Android, Windows Phone 7 и собственная платформа Bada. Сегодня возникла новая инсинуация. Сайт Precental.net опубликовал пост о том, что в их распоряжении оказалось внутренние документы Hewlett-Packard из которых следует, что подразделение WebOS планируется разделить на два — «софтверное», которое станет частью отдела Стратегии и Технологии (Office of Strategy and Technology) и «железное», которое планируется сделать частью Personal Systems Group, занимающее PC-бизнесом Hewlett-Packard. ');
        $blog->setShortText('Туманная ситуация относительно будущего мобильной операционной системы WebOS, находящейся в ведении Hewlett-Packard, способствует обрастанию слухами: вполне авторитетные агенства пророчили продажу WebOS либо её лицензирование ряду компаний, начиная от южно-корейской Samsung и заканчивая Facebook и HTC. ');
        $blog->setUser($manager->merge($this->getReference('user')));
        $blog->setCategory($manager->merge($this->getReference('category1')));
        $this->addTags($blog);
        $manager->persist($blog);
        $this->addReference('blog2', $blog);
        
        $blog = new Blog();
        $blog->setTitle('Оплата счета в барах KillFish');
        $blog->setText('Мы давно планировали выход в офлайн и вот, свершилось. Теперь счет в барах сети KillFish (Питер) можно оплатить при помощи QIWI Кошелька. Механика очень проста: называете бармену номер вашего мобильного телефона, через мгновение на него приходит USSD запрос с суммой счета. Нажав 1, вы оплачиваете счет: деньги списываются с вашего QIWI Кошелька. Мы давно планировали выход в офлайн и вот, свершилось. Теперь счет в барах сети KillFish (Питер) можно оплатить при помощи QIWI Кошелька. Механика очень проста: называете бармену номер вашего мобильного телефона, через мгновение на него приходит USSD запрос с суммой счета. Нажав 1, вы оплачиваете счет: деньги списываются с вашего QIWI Кошелька. Мы давно планировали выход в офлайн и вот, свершилось. Теперь счет в барах сети KillFish (Питер) можно оплатить при помощи QIWI Кошелька. Механика очень проста: называете бармену номер вашего мобильного телефона, через мгновение на него приходит USSD запрос с суммой счета. Нажав 1, вы оплачиваете счет: деньги списываются с вашего QIWI Кошелька.');
        $blog->setShortText('Мы давно планировали выход в офлайн и вот, свершилось. Теперь счет в барах сети KillFish (Питер) можно оплатить при помощи QIWI Кошелька. Механика очень проста: называете бармену номер вашего мобильного телефона, через мгновение на него приходит USSD запрос с суммой счета. Нажав 1, вы оплачиваете счет: деньги списываются с вашего QIWI Кошелька.');
        $blog->setUser($manager->merge($this->getReference('user')));
        $blog->setCategory($manager->merge($this->getReference('category1')));
        $this->addTags($blog);
        $manager->persist($blog);
        $this->addReference('blog3', $blog);
        
        $blog = new Blog();
        $blog->setTitle('JavaScript как язык с утиной типизацией');
        $blog->setText('JavaScript это мощный язык с динамической типизацией и это позволяет писать код с утиной типизацией. В двух словах утиная типизация позволяет выполнять функцию для обьектов которые обладают нужными нам свойствами (т.к. js функицональный язык то и метод является полноправным свойством). Я уверен что многие используют утиную типизацию, но я думаю данная заметка будет все равно интересна некоторому подмножеству множества аудитории хабра. В этой заметке мы проследим эволюцию функции range из работающей только с числами до функции которая работает с любыми обьектами. JavaScript это мощный язык с динамической типизацией и это позволяет писать код с утиной типизацией. В двух словах утиная типизация позволяет выполнять функцию для обьектов которые обладают нужными нам свойствами (т.к. js функицональный язык то и метод является полноправным свойством). Я уверен что многие используют утиную типизацию, но я думаю данная заметка будет все равно интересна некоторому подмножеству множества аудитории хабра. В этой заметке мы проследим эволюцию функции range из работающей только с числами до функции которая работает с любыми обьектами. JavaScript это мощный язык с динамической типизацией и это позволяет писать код с утиной типизацией. В двух словах утиная типизация позволяет выполнять функцию для обьектов которые обладают нужными нам свойствами (т.к. js функицональный язык то и метод является полноправным свойством). Я уверен что многие используют утиную типизацию, но я думаю данная заметка будет все равно интересна некоторому подмножеству множества аудитории хабра. В этой заметке мы проследим эволюцию функции range из работающей только с числами до функции которая работает с любыми обьектами.');
        $blog->setShortText('JavaScript это мощный язык с динамической типизацией и это позволяет писать код с утиной типизацией. В двух словах утиная типизация позволяет выполнять функцию для обьектов которые обладают нужными нам свойствами (т.к. js функицональный язык то и метод является полноправным свойством). ');
        $blog->setUser($manager->merge($this->getReference('user')));
        $blog->setCategory($manager->merge($this->getReference('category2')));
        $this->addTags($blog);
        $manager->persist($blog);
        $this->addReference('blog4', $blog);
        
        $manager->flush();
    }
    public function getOrder()
    {
        return 40; // the order in which fixtures will be loaded
    }
    
    protected function addTags($blog)
    {
        $tags = range(0, 16);
        shuffle($tags);
        $count = rand(1, 5);
        
        for ($a = 0 ; $a < $count; $a++)
        {
            $tag = $this->manager->merge($this->getReference('tag'.$tags[$a]));
            $blog->addTag($tag);
        }
    }

}
