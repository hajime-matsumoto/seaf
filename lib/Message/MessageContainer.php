<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Message;

use Seaf\Container;

class MessageContainer extends Container\ArrayContainer
{
    private $files;
    private $messages = [];
    private $locale;
    private $Translator;

    public function __construct ($locale, Translator $translator)
    {
        $this->locale = $locale;
        $this->Translator = $translator;

        // 全メッセージをロードする
        $dir = $translator->getMessageDir();
        $len = strlen($dir);
        $files = [];
        while($dirs = glob($dir.'/*', GLOB_ONLYDIR)) {
            $dir.= "/*";
            $files = array_merge($files, glob($dir.'/'.$locale.".yaml"));
        }
        $this->loadFiles($files, $len+1);
    }

    public function hasTranslation($key)
    {
        if (!$this->hasVar($key)) {
            foreach ($this->files as $k=>$v) {
                if(strpos($key, $k) == 0) {
                    $this->loadFiles($k);
                }
            }
            return $this->hasVar($key);
        }
        return true;
    }

    public function getTranslation($key)
    {
        if ($this->hasTranslation($key)) {
            return $this->getVar($key);
        }
        return false;
    }

    public function loadFiles ($files, $offset)
    {
        foreach ($files as $file) {
            $prefix = str_replace('/','.',dirname(substr($file, $offset)));

            $data = $this->Translator->getCacheHandler( )->useCache(
                $file,
                function(&$isSuccess) use ($file, $offset) {
                    $data = yaml_parse_file($file);
                    if ($data) $isSuccess = true;
                    return $data;
                },
                0,
                filemtime($file),
                $status
            );
            $this->setVar($prefix, $data);
        }
    }
}
