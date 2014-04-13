<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\FW\Web;


class View extends \Seaf\View\View
{
    private $ctrl;

    public function __construct (Controller $ctrl)
    {
        parent::__construct();
        $this->ctrl = $ctrl;
    }

    public function enable ( )
    {
        $this->ctrl->bind($this, [
            'display' => '_display'
        ]);
    }

    public function _display ($option, $datas = [])
    {
        $datas = array_merge($this->toArray(),$this->ctrl->response()->getParams());
        $datas['ctrl'] = $this->ctrl;
        return $this->display($option['template'], $datas);
    }
}
