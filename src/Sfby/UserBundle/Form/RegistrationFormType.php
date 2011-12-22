<?php
namespace Sfby\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        // add your custom field
        $builder->add('username');
        $builder->add('plainPassword', 'password');
        $builder->add('email', 'email');
        $builder->add('name');
        $builder->add('about', 'richtext');
        
    }

    public function getName()
    {
        return 'sfby_user_registration';
    }
}
