<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Cache\Engine;

use Seaf\Module\Kvs;

class FileEngine extends Base
{
    private $kvs;

    public function initEngine ( )
    {
        $this->kvs = new Kvs\Engine\FileEngine( );
    }

    public function setPath ($path)
    {
        $this->kvs->setPath($path);
    }

    public function _flush ( )
    {
        $this->kvs->flush();
    }

    public function _set ($key, $value, $expire)
    {
        $stat = array(
            'expires' => $expire
        );
        $this->kvs->set($key, $value, $stat);
    }

    public function _has ($key)
    {
        $result = $this->kvs->get($key, $stat);
        if ($result == false) {
            return false;
        }
        return true;
    }

    public function _get ($key, &$stat = null)
    {
        $data = $this->kvs->get($key, $stat);
        if (time() >= $stat['expires']) {
            return false;
        }
        return $data;
    }
}
