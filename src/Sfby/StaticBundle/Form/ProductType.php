<?php

namespace Sfby\StaticBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('description')
            ->add('category')
        ;
    }

    public function getName()
    {
        return 'product';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Sfby\StaticBundle\Entity\Product',
        );
    }

}
