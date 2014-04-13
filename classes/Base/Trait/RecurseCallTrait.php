<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;


trait RecurseCallTrait
    {
        public function recurseCallIfArray($array, $method, $useKey = true)
        {
            if (!is_array($array)) return false;

            foreach ($array as $k=>$v) {
                if ($useKey) {
                    call_user_func([$this,$method], $k,$v);
                } else {
                    call_user_func([$this,$method], $v);
                }
            }
            return true;
        }
    }
