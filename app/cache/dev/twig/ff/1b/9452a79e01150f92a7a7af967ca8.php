<?php

/* FrameworkBundle:Exception:exception_full.twig */
class __TwigTemplate_ff1b9452a79e01150f92a7a7af967ca8 extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    public function getParent(array $context)
    {
        if (null === $this->parent) {
            $this->parent = $this->env->loadTemplate("FrameworkBundle:Exception:layout.twig");
        }

        return $this->parent;
    }

    public function display(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $template = "FrameworkBundle:Exception:exception.twig";
        if ($template instanceof Twig_Template) {
            $template->display($context);
        } else {
            echo $this->env->getExtension('templating')->getTemplating()->render($template, $context);
        }
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:exception_full.twig";
    }
}
