<?php
/* vim: set expandtab ts=4 sw=4 sts=4: */

/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラス定義ファイル
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Core;
use Seaf\Util\DispatchHelper;

/**
 * イベントコンテナ
 *
 * イベントを保持するクラス
 */
class EventContainer extends Container
{
    /**
     * フックコンテナ
     */
    private $hookContainer;

    /**
     * イベントを保持する
     */
    public function __construct( )
    {
        $this->hookContainer = new Container( );
    }

    /**
     * フックの登録
     *
     * @param string $key
     * @param callback $callbaek
     */
    public function addHook( $name, $callback = null )
    {
        $this->hookContainer->stack( $name, $callback );
    }

    /**
     * イベントの実行
     */
    public function trigger( $name, $params )
    {
        if( $this->hookContainer->has( $name ) )
        {
            foreach( $this->hookContainer->restore( $name ) as $event )
            {
                $continue = DispatchHelper::dispatch($event, $params);
            }
        }
    }

    public function report()
    {
        printf("\n登録されているフック\n");
        foreach( $this->hookContainer->getRef() as $k => $v ) {
            $methods = array();
            foreach( $v as $event ) {
                if(is_array($event)) {
                    list($class,$method) = $event;
                    $func = new \ReflectionMethod(
                        get_class($class), $method
                    );
                    $method = get_class($class).'::'.$func->getName();
                    $method.= " in ";
                    $method.= $func->getFileName();
                    $method.= " line ";
                    $method.= $func->getStartLine();
                }else{
                    $func = new \ReflectionFunction($event);
                    $method = $func->getShortName();
                    $method.= " in ";
                    $method.= $func->getFileName();
                    $method.= " line ";
                    $method.= $func->getStartLine();
                }
                $methods[] = $method;
            }
        
            printf("%s : %s\n", $k, implode($methods));
        }
    }


}
