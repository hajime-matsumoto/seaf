<?php

/* /ra/cast.twig */
class __TwigTemplate_cefca19df24736e4c5bd0343b7853bb4a9cfa1175ac21983d2dfe4d4eacdc9a2 extends Twig_Template
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
        echo "      <link href='/ra-cast/main.css' rel='stylesheet'>
      <div id='container'>
        <div class='head'></div>
        <div class='cast' id='mayuka'>
          <div class='name'>まゆか</div>
          <div class='cast-name'>加弥乃</div>
          <div class='cast-kana'>KAYANO</div>
          <div class='cast-description'>
            1994 年生まれ。７歳で子役としてデビュー。<br />
            舞台「小公女セーラ」「魔法使いサリー」など、数々の作品に出演。
            ドラマ「チョコミミ」では主人公の一人ミミを演じ、 同キャラクター
            名義で CD をリリース。<br />
            アニメ「ジュエルペット」シリーズではエンディング テーマを歌い
            同年代少女からの支持を得る。<br />
            アイドルグループ AKB48 発足時には当時 12 歳の最年少メンバー
            として話題を集め、幅広いファンに応援される。<br />
            現在は女優として映画を中心に活動。<br />
            公開待機『少女は異世界で戦った』（金子修介監督） では主演の一人
            としてアクションにも挑戦している。<br />
          </div>
        </div>
        <div class='cast cast2' id='otoko'>
          <div class='name'>男</div>
          <div class='cast-name'>小場賢</div>
          <div class='cast-kana'>KEN KOBA</div>
        </div>
        <div class='cast cast2' id='misato'>
          <div class='name'>みさと</div>
          <div class='cast-name'>ももは</div>
          <div class='cast-kana'>MOMOHA</div>
        </div>
        <div class='cast cast2' id='lina'>
          <div class='name'>リナ</div>
          <div class='cast-name'>衣緒菜</div>
          <div class='cast-kana'>IONA</div>
        </div>
        <div class='cast cast3' id='kasumi'>
          <div class='name'>かすみ</div>
          <div class='cast-name'>文月</div>
          <div class='cast-kana'>FUZUKI</div>
        </div>
        <div class='cast cast3' id='yuko'>
          <div class='name'>ゆうこ</div>
          <div class='cast-name'>亜季</div>
          <div class='cast-kana'>AKI</div>
        </div>
        <div class='cast cast3' id='haha'>
          <div class='name'>みさとの母</div>
          <div class='cast-name'>佐倉萌</div>
          <div class='cast-kana'>MOE SAKURA</div>
        </div>
        <div class='cast cast3' id='sakaya'>
          <div class='name'>酒屋店員</div>
          <div class='cast-name'>久住翠希</div>
          <div class='cast-kana'>MIZUKI KUSUMI</div>
        </div>
        <div class='cast cast3' id='hukurou'>
          <div class='name'>フクロウ</div>
          <div class='cast-name'>屋敷紘子</div>
          <div class='cast-kana'>HIROKO YASHIKI</div>
        </div>
      </div>
";
    }

    public function getTemplateName()
    {
        return "/ra/cast.twig";
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
