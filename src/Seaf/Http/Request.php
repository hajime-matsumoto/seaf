<?php
/**
 * Seaf: Simple Easy Acceptable micro-framework.
 *
 * クラスを定義する
 *
 * @author HAjime MATSUMOTO <mail@hazime.org>
 * @copyright Copyright (c) 2014, Seaf
 * @license   MIT, http://seaf.hazime.org
 */

namespace Seaf\Http;

use Seaf\Seaf;
use Seaf\Core\Base;
use Seaf\Component\Request as SeafRequest;
use Seaf\Collection\ArrayCollection;

/**
 * REQUESTコンポーネント
 */
class Request extends SeafRequest
{
    public function __construct()
    {
        parent::__construct();

        $server = new ArrayCollection($_SERVER);
        $request = new ArrayCollection($_REQUEST);

        // リクエストURLの設定
        $this->setURL( $server->get('REQUEST_URI', '/') );

        // ベースURLの設定
        $this->setBaseURL(
            dirname($server->get('SCRIPT_NAME'))
        );

        // メソッドの設定
        $this->setMethod(
            $server->get(
                'HTTP_X_HTTP_METHOD_OVERRIDE',
                $request->get(
                    '_method',
                    $server->get(
                        'REQUEST_METHOD', 'GET'
                    )
                )
            )
        );

        // リクエストパラメータのセット
        foreach( $_REQUEST as $k=>$v )
        {
            $this->set( $k,$v );
        }
    }

}

/* vim: set expandtab ts=4 sw=4 sts=4: et*/
