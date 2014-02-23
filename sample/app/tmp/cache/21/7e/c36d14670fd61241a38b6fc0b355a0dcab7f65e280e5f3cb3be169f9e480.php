<?php

/* ra/base.twig */
class __TwigTemplate_217ec36d14670fd61241a38b6fc0b355a0dcab7f65e280e5f3cb3be169f9e480 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
  <head>
\t<script src=\"http://192.168.100.200:35730/livereload.js\"></script>
\t<script src=\"http://192.168.100.200:35731/livereload.js\"></script>
    <title>水井真希監督 映画 【ら】</title>
    <meta charset='utf-8'>
    <meta content='width=device-width initial-scale=1.0' name='viewport'>
    <meta content='水井真希 監督 映画 【ら】公式WEBサイト' name='description'>
    <meta content='水井真希 監督 映画 【ら】' name='keywords'>
    <meta content='' name='author'>
    <meta content='index,follow' name='robots'>
    <!--
      OGTAG
      ==========================================
    -->
    <!--
      <link href='http://www.hazime.org/index.php' rel='canonical'>
      <link href='https://plus.google.com/103258909747093180301' rel='author'>
    -->
    <meta content='ja_JP' property='og:locale'>
    <meta content='水井真希監督 映画 【ら】' property='og:title'>
    <meta content='水井真希 監督 映画 【ら】公式WEBサイト' property='og:description'>
    <meta content='' property='og:url'>
    <meta content='' property='og:image'>
    <meta content='summary' name='twitter:card'>
    <meta content='@hajime_mat' name='twitter:site'>
    <meta content='水井真希監督 映画 【ら】' name='twitter:domain'>
    <meta content='@hajime_mat' name='twitter:creator'>
    <!--
      IE support
    -->
    <!--[if lt IE 9]>
      <script src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
    <![endif]-->
    <!-- Bootstrap用CSS -->
    <link href='//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css' rel='stylesheet'>
    <!-- Font -->
    <link href='//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css' rel='stylesheet'>
    <!-- 【ら】用のCSS -->
    <link href='assets/css/ra.css' rel='stylesheet'>
  </head>
  <body>
    <div class='container' style='width:1000px'>
      <nav class='navbar navbar-inverse' role='navigation'>
        <div class='navbar-header'>
          <a class='navbar-brand' href='/ra'>【ら】</a>
        </div>
        <div class='navbar-collapse'>
          <ul class='nav navbar-nav'>
            <li>
              <a href='/ra/#news'>最新情報</a>
            </li>
            <li>
              <a href='/ra/yokoku'>予告編</a>
            </li>
            <li>
              <a href='/ra/cast'>キャスト</a>
            </li>
            <li>
              <a href='/ra/staff'>スタッフ</a>
            </li>
          </ul>
          <ul class='nav navbar-nav navbar-right'>
            <li>
              <a href='http://www.facebook.com/mmizuira'>
                <span class='icon-facebook'></span>
                Facebook
              </a>
            </li>
            <li>
              <a href='https://twitter.com/mmizuira'>
                <span class='icon-twitter'>
                  Twitter
                </span>
              </a>
            </li>
          </ul>
        </div>
      </nav>
      ";
        // line 81
        $this->displayBlock('content', $context, $blocks);
        // line 83
        echo "      <footer style='margin-top:200px;mergin-bottom:100px'>
        &copy;
        <a href='http://www.mmizui.com'>Maki MIZUI</a>
        all rights reserved
      </footer>
    </div>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'></script>
    <script src='//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js'></script>
  </body>
</html>
";
    }

    // line 81
    public function block_content($context, array $blocks = array())
    {
        // line 82
        echo "      ";
    }

    public function getTemplateName()
    {
        return "ra/base.twig";
    }

    public function getDebugInfo()
    {
        return array (  121 => 82,  118 => 81,  104 => 83,  102 => 81,  20 => 1,  31 => 3,  28 => 2,);
    }
}
