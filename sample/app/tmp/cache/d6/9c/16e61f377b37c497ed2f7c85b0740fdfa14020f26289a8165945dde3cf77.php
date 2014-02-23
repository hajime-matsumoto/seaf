<?php

/* /ra/index.twig */
class __TwigTemplate_d69c16e61f377b37c497ed2f7c85b0740fdfa14020f26289a8165945dde3cf77 extends Twig_Template
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
        echo "\t<img src='assets/img/RA_WEBtop.jpg' style='width:100%'>
      <h2 id='news'>
        最新情報
        <small>news</small>
      </h2>
      <ul>
        <li>
          ゆうばり国際ファンタスティック映画祭2014オフシアターコンペティション入選
          <br>
          2014年2月27日(木)〜3月3日(火)開催
          <br>
          <a href='http://yubarifanta.com/'>http://yubarifanta.com</a>
        </li>
      </ul>
";
    }

    public function getTemplateName()
    {
        return "/ra/index.twig";
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
