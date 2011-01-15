<?php

/* HelloBundle:Hello:index.twig */
class __TwigTemplate_c489aba20f5b5b8c423f558647dcb308 extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    public function getParent(array $context)
    {
        if (null === $this->parent) {
            $this->parent = $this->env->loadTemplate("HelloBundle::layout.twig");
        }

        return $this->parent;
    }

    public function display(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "    Hello ";
        echo twig_escape_filter($this->env, $this->getContext($context, 'name', '4'), "html");
        echo "!
";
    }

    public function getTemplateName()
    {
        return "HelloBundle:Hello:index.twig";
    }
}
