<?php // vim: set ft=php ts=4 sts=4 sw=4 et: 
namespace Seaf\Message;

use Seaf\Base;

class TranslatorSection extends Translator
{
    private $prefix;
    private $translator;

    public function __construct (Translator $translator, $prefix)
    {
        $this->translator =  $translator;
        $this->prefix = $prefix;
    }

    public function prefix ($key)
    {
        return $this->prefix.'.'.$key;
    }

    /**
     * コードに紐づくメッセージを取得する
     *
     * @param string
     * @return string
     */
    public function translate ($code)
    {
        return $this->translator->translate($this->prefix($code));
    }
}
