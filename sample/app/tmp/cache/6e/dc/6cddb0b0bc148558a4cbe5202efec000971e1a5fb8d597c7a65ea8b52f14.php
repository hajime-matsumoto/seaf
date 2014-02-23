<?php

/* index.twig */
class __TwigTemplate_6edc6cddb0b0bc148558a4cbe5202efec000971e1a5fb8d597c7a65ea8b52f14 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("base.twig");

        $this->blocks = array(
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
    public function block_content($context, array $blocks = array())
    {
        // line 3
        echo "        <figure id='slider'>
          <div class='row'>
            <div class='span12'>
              <div class='flexslider'>
                <ul class='slides'>
                  <li>
                    <img alt='水井真希' src='assets/images/slider/slide1.jpg'>
                  </li>
                  <li>
                    <img alt='水井真希' src='assets/images/slider/slide3.jpg'>
                  </li>
                  <li>
                    <img alt='水井真希' src='assets/images/slider/slide4.jpg'>
                  </li>
                  <li>
                    <img alt='水井真希' src='assets/images/slider/slide6.jpg'>
                  </li>
                  <li>
                    <img alt='水井真希' src='assets/images/slider/slide7.jpg'>
                  </li>
                </ul>
              </div>
              <span id='responsiveFlag'></span>
            </div>
          </div>
        </figure>
        <div class='row'>
          <div class='span8' id='content'>
            <section>
              <h1 class='line'>
                <span>News</span>
              </h1>
              <ul class='unstyled'>
                <li>
                  <b>映画</b>
                  <p>
                    <a href='/ra'>水井真希初監督作品【ら】</a>
                    <br>
                    <a href='http://yubarifanta.com/'>
                      ゆうばり国際ファンタスティック映画祭2014
                    </a>
                    オフシアターコンペティション入選
                    <br>
                    2014年2月27日(木)〜3月3日(火)開催
                  </p>
                  <p>
                    2013年12月11日 BD/DVD発売 「ABC・オブ・デス」（日本版特典オーディオコメンタリー参加）
                  </p>
                </li>
                <li>
                  <b>撮影会</b>
                  <p>
\t\t\t\t\t 2014年3月15日（土）
                    <a href='mailto:newspromotion@nifty.com?subject=%E3%83%8B%E3%83%A5%E3%83%BC%E3%82%B9%E3%83%97%E3%83%AD%E3%83%A2%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3%E6%92%AE%E5%BD%B1%E4%BC%9A&amp;body=%E6%B0%B4%E4%BA%95%E7%9C%9F%E5%B8%8C%E6%92%AE%E5%BD%B1%E4%BC%9A%E4%BA%88%E7%B4%84%E7%94%B3%E3%81%97%E8%BE%BC%E3%81%BF%0D%0A%0D%0A%E6%B0%8F%E5%90%8D%0D%0A%0D%0A%E4%BD%8F%E6%89%80%0D%0A%0D%0A%E3%83%A1%E3%83%BC%E3%83%AB%E3%82%A2%E3%83%89%E3%83%AC%E3%82%B9%0D%0A%0D%0A%E5%B9%B4%E9%BD%A2%0D%0A%0D%0A%E9%9B%BB%E8%A9%B1%E7%95%AA%E5%8F%B7%0D%0A%0D%0A%E6%92%AE%E5%BD%B1%E9%96%8B%E5%82%AC%E6%97%A5%E3%83%BB%E5%B8%8C%E6%9C%9B%E6%99%82%E9%96%93%0D%0A%E3%82%AA%E3%83%97%E3%82%B7%E3%83%A7%E3%83%B3%20VTR%E5%B8%8C%E6%9C%9B%0D%0A%0D%0A%E3%81%9D%E3%81%AE%E4%BB%96%E3%81%94%E8%B3%AA%E5%95%8F%E7%AD%89'>ニュースプロモーション撮影会＠御苑前スタジオ</a>
                  </p>
                </li>
              </ul>
            </section>
            <section>
              <h1 class='line'>
                <span>Profile</span>
              </h1>
              <div class='row'>
                <div class='span3'>
                  <img alr='水井真希' src='assets/images/profile.jpg'>
                </div>
                <div class='span5'>
                  <section>
                    <h1>
                      水井 真希
                      <span class='small'>Maki Mizui</span>
                    </h1>
                    <h2>経歴</h2>
                    <p>
                      十代後半で監督園子温に師事。美術スタッフとして映画業界に入る。
                      <br>
                      その後、西村映造で特殊メイク・造型を学ぶ。
                      <br>
                      経験を生かし、幽霊役で映画初出演。グラビアや役者活動に軸を移す。
                      <br>
                      主演映画「終わらない青」「イチジクコバチ」など。
                      <br>
                      幸の薄い少女役、またはゾンビや妖怪などの人外役が極端に多い。
                      <br>
                      フリーペーパー「月刊水井」を発行中。
                    </p>
                  </section>
                </div>
                <nav class='link'>
                  <a href='/works'>Read more...</a>
                </nav>
              </div>
            </section>
          </div>
          <div class='span4 sidebar'>
            <div class='video-container'>
              <iframe allowfullscreen frameborder='0' src='http://www.youtube-nocookie.com/embed/MOp0BRrwtmw?rel=0'></iframe>
            </div>
            <a class='twitter-timeline' data-widget-id='312940645742428160' href='https://twitter.com/mmizui'>@mmizui からのツイート</a>
            <script>
              !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"//platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");
            </script>
          </div>
        </div>
      </div>
";
    }

    public function getTemplateName()
    {
        return "index.twig";
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
