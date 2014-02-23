<?php

/* gallery.twig */
class __TwigTemplate_9f15b2ad9b83017a9f0f1119f5484a9183eff65c54979fa0ce3a50418c1444bb extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base.twig");

        $this->blocks = array(
            'addheader' => array($this, 'block_addheader'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "base.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_addheader($context, array $blocks = array())
    {
        // line 3
        echo "\t<link href='assets/gallery/css/rtg.css' rel='stylesheet'>
\t<link href='assets/gallery/css/lightbox.css' rel='stylesheet'>
\t<script src='assets/gallery/js/jquery-1.8.2.min.js'></script>
\t<script src='assets/gallery/js/rtg.js'></script>
\t<script src='assets/gallery/js/lightbox.js'></script>
\t<script src='assets/gallery/js/gallery.js'></script>
";
    }

    // line 10
    public function block_content($context, array $blocks = array())
    {
        // line 11
        echo "        <div class='row'>
          <div class='span12'>
            <div class='pagetitle'>
              <div class='title-inner'>
                <h1>Gallery</h1>
              </div>
            </div>
          </div>
        </div>
        <div id='content'>
          <div class='row'>
            <div class='span12'>
              <div class='rtg-gallery' id='myGallery'>
                <div class='rtg-images'>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/1.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/1.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/2.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/2.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/3.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/3.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/4.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/4.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/5.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/5.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/6.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/6.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/7.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/7.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/8.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/8.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/9.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/9.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/10.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/10.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/1.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/1.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/2.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/2.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/3.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/3.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/4.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/4.jpg'>
                    </a>
                  </div>
                  <div data-category='photo'>
                    <a href='assets/gallery/images/lookbook/5.jpg' rel='lightbox[on]' title=''>
                      <img src='assets/gallery/images/lookbook/5.jpg'>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
";
    }

    public function getTemplateName()
    {
        return "gallery.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  45 => 11,  42 => 10,  32 => 3,  29 => 2,);
    }
}
