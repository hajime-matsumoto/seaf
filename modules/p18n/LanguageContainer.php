<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\P18n;

use Seaf;
use Seaf\Pattern;
use Seaf\Data\Container\ArrayContainer;

class LanguageContainer extends ArrayContainer
{
    public function __construct ($dir)
    {
        if (Seaf::Cache( )->has((string) $dir, $dir->mtime())) {
            $this->data = Seaf::Cache()->getCachedData((string) $dir);
        } else {
            foreach ($dir as $file)
            {
                $key = $file->basename(false);
                if ($key == 'default') {
                    foreach($file->toArray() as $k=>$v) {
                        $this->set($k, $v);
                    }
                } else {
                    $array = $file->toArray();
                    $this->set($key, $array);
                }
            }
            Seaf::Cache( )->put((string)$dir, 0, $this->data);
        }
    }
}
