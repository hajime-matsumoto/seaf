<?php

/* /ra/yokoku.twig */
class __TwigTemplate_bb4cb042e71b53a7b18702eeb5a80f98e3c0b171b3ea4e14ad32ef133c572f73 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("ra/base.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "ra/base.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_content($context, array $blocks = array())
    {
        // line 3
        echo "     <div class='video-container'>
        <iframe allowfullscreen='' frameborder='0' src='http://www.youtube-nocookie.com/embed/MOp0BRrwtmw?rel=0'></iframe>
      </div>
";
    }

    public function getTemplateName()
    {
        return "/ra/yokoku.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 3,  28 => 2,);
    }
}
