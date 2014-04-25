<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base;

use Seaf\Wrapper;

trait RaiseErrorTrait
    {
        private $codes = [];

        /**
         * エラーコードを登録する
         */
        public function setErrorCode ($code, $exception = 'Exception', $params = [])
        {
            $this->codes[$code] = [
                'exception' => $exception,
                'params' => $params
            ];
        }

        /**
         * エラーを起す
         */
        public function raiseError ($code, $params = [])
        {
            if (!array_key_exists($code, $this->codes)) {
                throw new \Exception('CANT RAISE ERROR CODE '.$code.' IS NOT REGISTERED');
            }

            $info = $this->codes[$code];
            $class = $info['exception'];

            throw Wrapper\ReflectionClass::factory($class)->newInstanceArgs($params);
        }

    }
