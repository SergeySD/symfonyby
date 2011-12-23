<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sfby\UserBundle\Mailer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\Mailer as BaseMailer;

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Mailer extends BaseMailer
{
    public function sendRegistrationEmailMessage(UserInterface $user,$plainPassword)
    {
        $template = $this->parameters['registration.template'];
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'plainPassword'=>$plainPassword
        ));
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['registration'], $user->getEmail());
    }
}
