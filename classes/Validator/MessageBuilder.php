<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Validator;

use Seaf\Module\P18n\P18n;

class MessageBuilder
{
    private $p18n;

    public function __construct (P18n $p18n = null)
    {
        if ($p18n != null) {
            $this->p18n = $p18n;
        }
    }
    /**
     * フォーマットをセットする
     *
     */
    public function setFormat($key, $format = null)
    {
        if(is_array($key)) {
            foreach($key as $k=>$v) $this->setFormat($k, $v);
            return $this;
        }
        $this->formats[$key] = $format;
        return $this;
    }

    /**
     * ラベルをセットする
     */
    public function setLabel($key, $label = null)
    {
        if (is_array($key)) {
            foreach ($key as $k=>$v) $this->setLabel($k, $v);
            return $this;
        }
        $this->labels[$key] = $label;
        return $this;
    }

    public function getLabel($key) 
    {
        if (isset($this->labels[$key])) {
            return $this->labels[$key];
        } else {
            return $key;
        }
    }

    public function getFormat($key) 
    {
        if (isset($this->formats[$key])) {
            return $this->formats[$key];
        } else {

            // P18Nがセットされていれば使う
            if ($this->p18n) {
                return $this->p18n->getTranslation()->get($key);
            }
            return $key;
        }
    }


    /**
     * メッセージをビルドする
     */
    public function build($errors)
    {
        $new_messages = [];
        foreach($errors as $k=>$v) {
            $new_messages[$k]['label'] = $this->getLabel($k);
            foreach ($v as $err) {
            $new_messages[$k]['messages'][]  = preg_replace_callback(
                    '/\$([^$]+)\$/',
                    function($m) use ($err){
                        return $err[1][$m[1]];
                    },$this->getFormat($err[0]));
            }
        }
        return $new_messages;
    }
}
