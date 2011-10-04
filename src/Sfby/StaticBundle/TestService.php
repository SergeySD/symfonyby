<?php

namespace Sfby\StaticBundle;

use Symfony\Component\Templating\EngineInterface;


class TestService 
{
    /**
     * 
     * @var EngineInterface
     */
    protected $templating;
    
    protected $test;

    public function __construct(EngineInterface $templating, $test)
    {
        $this->templating = $templating;
        $this->test = $test;
    }
    
    public function get($test = null)
    {
        return $this->templating->render('SfbyStaticBundle::test.html.twig', array('test' => $test?$test:$this->test));
    }
}
