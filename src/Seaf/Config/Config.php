<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * コンフィグクラス定義
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Config;

/**
 * コンフィグ
 *
 * 設定値を管理するクラス
 */
use Seaf\Core\Container;
use Seaf\Util\ArrayHelper;

class Config extends Container
{
    public function setConfigArray( $data )
    {
        foreach( $data as $key=>$value )
        {
            $this->setConfig( $key, $value );
        }
    }
    public function setConfig( $key, $value )
    {
        return ArrayHelper::parseSet( $this->getRef(), $key, $value );
    }

    public function getConfig( $key, $default = null )
    {
        $data =  ArrayHelper::parseGet( $this->getRef(), $key, $default );
        if(!is_string($data)) return $data;
        $self = $this;
        return preg_replace_callback('/\{\{(.*)\}\}/', function($m) use($self){
            return $self->getConfig($m[1]);
        }, $data);
    }
}
