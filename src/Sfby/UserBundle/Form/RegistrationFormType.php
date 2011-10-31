<?php
namespace Sfby\UserBundle\Form;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('name');
        $builder->add('about');
        
        $builder->add('plainPassword', 'password');
    }

    public function getName()
    {
        return 'sfby_user_registration';
    }
}
