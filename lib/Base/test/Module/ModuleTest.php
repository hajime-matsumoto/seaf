<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Base\Module;

class TestModuleFacade implements ModuleFacadeIF
{
    use ModuleFacadeTrait;

    protected static $object_name = 'Test';

    public function __construct($module = null)
    {
        if ($module instanceof ModuleIF) {
            $this->setParentModule($module);
        }
    }

    public function sayObjectName( )
    {
        echo $this->getObjectName();
    }
}
class TestModuleMediator implements ModuleMediatorIF
{
    use ModuleMediatorTrait;
    protected static $object_name = 'TestMediator';

    public function __construct($module = null)
    {
        if ($module instanceof ModuleIF) {
            $this->setParentModule($module);
        }
    }
}


/**
 * モジュールのテスト
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateAndSearch ( )
    {
        $t1 = new TestModuleFacade();
        $t1->tag('id',1);
        $t2 = new TestModuleFacade($t1);
        $t2->tag('id',2);
        $t3 = new TestModuleFacade($t2);
        $t3->tag('id',3);
        $t4 = new TestModuleFacade($t3);
        $t4->tag('id',4);

        $this->assertEquals(
            '1', $t4->rootParent()->getTag('id')
        );
        $this->assertEquals(
            '3', $t4->getParent()->getTag('id')
        );
        $this->assertEquals(
            '2', $t4->findParent(function($m){return $m->isTag('id',2);})->getTag('id')
        );
    }

    public function testMediator ( )
    {
        $m1 = new TestModuleMediator();
        $m1->tag('object_name','m1');
        $m1->on('log', function($e) {
            echo $e->log."\n";
        });
        $m1->registerModule('f1', __NAMESPACE__.'\TestModuleFacade');
        $m1->registerModule('m2', __NAMESPACE__.'\TestModuleMediator');
        //$m1->m2->f1->section->section2->sayHello();
        $this->assertInstanceOf(
            'Seaf\Base\Proxy\ProxyRequestIF',
            $m1->m2
        );

        $this->assertInstanceOf(
            'Seaf\Base\Proxy\ProxyRequestIF',
            $m1->m2->f1
        );

        $m1->m2->registerModule('f2', __NAMESPACE__.'\TestModuleFacade');
        $this->assertEquals(
            'm1|m2|f2', $m1->m2->f2->getObjectName( )
        );
        $this->assertEquals(
            'm1|f1', $m1->m2->f2->rootParent()->f1->getObjectName()
        );

        $res = $m1->m2->f2->runAll(['getObjectName']);

        $this->assertEquals(
            'm1|m2|f2', $res['getObjectName']
        );
    }

}
