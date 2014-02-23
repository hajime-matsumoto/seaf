<?php

/* base.twig */
class __TwigTemplate_0428ab8c68a51fa115f2f2e27d66d976e6bdb222fb0a8a67b532eb50bb0ccf43 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'addheader' => array($this, 'block_addheader'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang='ja'>
\t<head>
\t\t<title>水井真希公式ホームページ | Maki Mizui Official Site</title>
\t\t<meta charset='utf-8'>
\t\t<meta content='width=device-width, initial-scale=1.0' name='viewport'>
\t\t<link href='assets/stylesheets/bootstrap.min.css' rel='stylesheet'>
\t\t<link href='assets/stylesheets/bootstrap-responsive.min.css' rel='stylesheet'>
\t\t<link href='assets/stylesheets/kwicks-slider.css' rel='stylesheet'>
\t\t<link href='assets/stylesheets/style.css' rel='stylesheet'>
\t\t<script src='assets/scripts/jquery-1.9.1.min.js'></script>
\t\t<script src='assets/scripts/jquery.flexslider-min.js'></script>
\t\t<script src='assets/scripts/jquery.kwicks-1.5.1.js'></script>
\t\t<script src='assets/scripts/scrolltopcontrol.js'></script>
\t\t<!--[if lt IE 9]>
\t\t<script src='assets/scripts/html5shiv.js'></script>
\t\t<![endif]-->
\t\t";
        // line 18
        $this->displayBlock('addheader', $context, $blocks);
        // line 19
        echo "\t</head>
    <body id='top'>
      <div class='container'>
        <header class='pagetop'>
          <div class='row'>
            <div class='span6 pull-right'>
              <nav>
                <div class='navbar navbar-inverse'>
                  <div class='navbar-inner pull-center'>
                    <div class='container'>
                      <button class='btn btn-navbar' data-target='.nav-collapse' data-toggle='collapse' type='button'>
                        <span class='icon-bar'></span>
                        <span class='icon-bar'></span>
                        <span class='icon-bar'></span>
                      </button>
                    </div>
                    <div class='nav-collapse collapse'>
                      <ul class='nav'>
                        <li>
                          <a href='index'>Home</a>
                        </li>
                        <li>
                          <a href='works'>Works</a>
                        </li>
                        <li>
                          <a href='gallery'>Gallery</a>
                        </li>
                        <li>
                          <a href='http://blog.livedoor.jp/mmizui' target='_blank'>Blog</a>
                        </li>
                        <li>
                          <a href='contact'>Contact</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </nav>
            </div>
            <div class='span6 pull-right'>
              <hgroup>
                <h1>
                  <a href='/'>
                    Maki Mizui
                    <span class='small'>Official Website</span>
                  </a>
                </h1>
                <h2>水井真希 公式ホームページ</h2>
              </hgroup>
            </div>
          </div>
        </header>
        <!-- slider -->
";
        // line 72
        $this->displayBlock('content', $context, $blocks);
        // line 74
        echo "      <footer>
        <div class='container'>
          <div class='toplink'>
            <a href='#top'>
              <span>Back to top</span>
            </a>
          </div>
          <div class='inner'>
            <div class='container'>
              <nav>
                <ul>
                  <li>
                    <a href='index'>Home</a>
                  </li>
                  <li>
                    <a href='works'>Workd</a>
                  </li>
                  <li>
                    <a href='gallery'>Gallery</a>
                  </li>
                  <li>
                    <a href='http://blog.livedoor.jp/mmizui' target='_blank'>Blog</a>
                  </li>
                  <li>
                    <a href='contact'>Contact</a>
                  </li>
                </ul>
              </nav>
              <small>2013 &copy; mmizui.com</small>
            </div>
          </div>
        </div>
      </footer>
      <script src='assets/scripts/bootstrap.min.js'></script>
    </body>
  </head>
</html>
";
    }

    // line 18
    public function block_addheader($context, array $blocks = array())
    {
    }

    // line 72
    public function block_content($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "base.twig";
    }

    public function getDebugInfo()
    {
        return array (  145 => 72,  140 => 18,  99 => 74,  97 => 72,  42 => 19,  40 => 18,  21 => 1,  31 => 3,  28 => 2,);
    }
}
