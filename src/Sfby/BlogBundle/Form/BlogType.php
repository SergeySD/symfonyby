<?php

namespace Sfby\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('keywords')
            ->add('short_text')
            ->add('text', 'richtext')
            ->add('category')
            ->add('tags_text', 'autocomplete')
        ;
    }

    public function getName()
    {
        return 'sfby_blog_form';
    }
}
