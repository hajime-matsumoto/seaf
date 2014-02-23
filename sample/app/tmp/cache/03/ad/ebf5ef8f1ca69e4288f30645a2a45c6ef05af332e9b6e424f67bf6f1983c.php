<?php

/* contact.twig */
class __TwigTemplate_03adebf5ef8f1ca69e4288f30645a2a45c6ef05af332e9b6e424f67bf6f1983c extends Twig_Template
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
    }

    // line 4
    public function block_content($context, array $blocks = array())
    {
        // line 5
        echo "        <div class='row'>
          <div class='span12'>
            <div class='pagetitle'>
              <div class='title-inner'>
                <h1>Contact</h1>
              </div>
            </div>
          </div>
        </div>
        <div class='row'>
          <div class='span12'>
            <section>
              <h1 class='line'>
                <span>コンタクトフォーム</span>
              </h1>
              <p>ご意見、ご質問、メッセージ等々お気軽にお問い合わせください。</p>
              <form action='/confirm' class='form-horizontal' method='post'>
                <fieldset>
                  <div class='control-group'>
                    <label class='control-label'>お名前</label>
                    <div class='controls'>
                      <input class='input-xlarge' id='name' name='name' type='text' value=''>
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>
                      <span class='label label-info'>必須</span>
                      メールアドレス
                    </label>
                    <div class='controls'>
                      <input class='input-xlarge' id='mail' name='mail' type='text' value=''>
                    </div>
                  </div>
                  <div class='control-group'>
                    <label class='control-label'>
                      <span class='label label-info'>必須</span>
                      メッセージ
                    </label>
                    <div class='controls'>
                      <textarea class='input-xlarge' id='message' name='message' rows='4'></textarea>
                    </div>
                  </div>
                  <div class='form-actions'>
                    <button class='btn btn-info' type='submit'>送信</button>
                    <button class='btn' type='reset'>キャンセル</button>
                  </div>
                </fieldset>
              </form>
            </section>
          </div>
        </div>
      </div>
";
    }

    public function getTemplateName()
    {
        return "contact.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 5,  34 => 4,  29 => 2,);
    }
}
