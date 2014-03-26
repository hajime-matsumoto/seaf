<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Module\Session\Handler;

use Seaf;

class FileHandler extends Handler
{
    private $session_path = '/tmp';

    public function isSessionUsed ($sid)
    {
        $file = $this->getSessionFile($sid);
        return $file->isExists();
    }

    public function getSessionFile($sid)
    {
        return Seaf::fileSystem($this->session_path.'/sess-'.$sid);
    }

    public function sessionStart( )
    {
        $file = $this->getSessionFile($this->sessionid);

        if ($file->isExists()) {
            $this->data = unserialize($file->getContents());
        }
    }

    public function sessionStore( )
    {
        $file = $this->getSessionFile($this->sessionid);
        $file->putContents(serialize($this->data));
    }

    public function sessionDestroy( )
    {
        $file = $this->getSessionFile($this->sessionid);
        $file->unlink();
    }
}
