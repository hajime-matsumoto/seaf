<?php
/**
 * 例外シリーズ
 */
namespace Seaf\Exception;

/**
 * インバリッドコール用の例外
 */
class InvalidCall extends Exception 
{
    /**
     * __construct
     *
     * @param string
     * @param object
     * @return void
     */
    public function __construct ($name, $object)
    {
        parent::__construct(array(
            '%02$s::%01$sは予期せぬ呼び出しです。'
            , $name, get_class($object)
        ));
    }
}
