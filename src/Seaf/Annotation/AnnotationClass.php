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

namespace Seaf\Annotation;

use Seaf\Util\AnnotationHelper;
use ReflectionClass;

/**
 * アノテーション管理クラス
 */
class AnnotationClass
{
    private $class;
    private $classAnnotation;

    /**
     * コンストラクタ
     */
    public function __construct( $class )
    {
        if( is_object($class) )
        {
            $class = get_class($class);
        }
        $this->class = new ReflectionClass($class);
    }

    /**
     * クラスアノテーションを取得する
     */
    public function getClassAnnotation()
    {
        if( !empty($this->classAnnotation) )
        {
            return $this->classAnnotation;
        }

        return $this->classAnnotation = AnnotationHelper::getAnnotation(
            $this->class->getDocComment()
        );
    }

    /**
     * メソッドアノテーションを取得する
     */
    public function getMethodAnnotation()
    {
        if( !empty($this->methodAnnotation) )
        {
            return $this->methodAnnotation;
        }

        $this->methodAnnotation = array();

        foreach( $this->class->getMethods() as $method )
        {
            $name = $method->getName();
            $this->methodAnnotation[$name] = AnnotationHelper::getAnnotation(
                $method->getDocComment()
            );
        }
        return $this->methodAnnotation;
    }
}
/* vim: set expandtab ts=4 sw=4 sts=4: et*/
