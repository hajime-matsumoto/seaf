<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * WEBモジュール
 */
namespace Seaf\Web;

use Seaf\Util\Util;
use Seaf\Base\Proxy;
use Seaf\Logging;

use Seaf\Base\Module;
use Seaf\Base\Component;

/**
 * モジュールファサード
 */
trait AppTrait
    {
        use ComponentTrait;
        use Component\ComponentContainerTrait;


        public function onInitComponentContainer ( )
        {
            $this->registerComponent([
                'router'   => __NAMESPACE__.'\Component\Router',
                'request'  => __NAMESPACE__.'\Component\Request',
                'response' => __NAMESPACE__.'\Component\Response'
            ]);
        }

        /**
         * URLをマップする
         *
         * @param string
         * @param string
         */
        public function route ($path, callable $action)
        {
            $this->debug(['Routed %s', $path]);
            $this->loadComponent('router')->map($path, $action);
            return $this;
        }


        /**
         * 実行
         *
         * @param string
         * @param string
         */
        public function run ($request = null, $response = null, &$dispatched = false, $nest = 0)
        {
            $this->ensureComponent('request', $request);
            $this->ensureComponent('response', $response);

            if ($dispatched) { return; }

            $this->fireEvent('beforeDispatchLoop',[
                'request'    => $request,
                'response'   => $response,
                'dispatched' => &$dispatched,
                'nest'       => $nest
            ]);

            if ($nest == 0) {
                $this->info(['Recive URL %s',$request->url()]);
            }else{
                $this->info(['Forward %s',$request->getPath()]);
            }

            // ルーティング
            $router = $this->loadComponent('router');

            while($route = $router->route($request)) {

                $this->fireEvent('beforeDispatch',[
                    'route'      => $route,
                    'request'    => $request,
                    'response'   => $response,
                    'dispatched' => &$dispatched,
                    'nest'       => $nest
                ]);

                $this->info(['Route Execute  %s', $route]);

                $result = $route->execute($request, $response, $this);

                $this->fireEvent('afterDispatch',[
                    'route'      => $route,
                    'result'     => $result,
                    'request'    => $request,
                    'response'   => $response,
                    'dispatched' => &$dispatched,
                    'nest'       => $nest
                ]);

                if ($result !== false) {
                    $dispatched = true;
                    break;
                }
                $route->next();
            }

            $this->fireEvent('afterDispatchLoop',[
                'request'    => $request,
                'response'   => $response,
                'dispatched' => &$dispatched,
                'nest'       => $nest
            ]);
            $this->debug(['Dispatch Statut >>> %s <<<', $dispatched ? 'OK': 'NG']);

            if ($nest == 0) {
                $this->fireEvent('afterRun',[
                    'request'    => $request,
                    'response'   => $response,
                    'dispatched' => &$dispatched,
                    'nest'       => $nest
                ]);
                $this->info(['Last Dispatch Statut >>> %s <<<', $dispatched ? 'OK': 'NG']);
            }

        }

    }
