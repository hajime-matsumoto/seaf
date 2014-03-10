<?php
namespace Seaf\Core;

/**
 * 環境クラス用のヘルパマネージャ
 * =============================
 *
 * 役割
 * -----------------------------
 * 1. 環境クラスにヘルパを提供する
 * 2. グローバルに定義されたヘルパマネージャを参照する
 */
class HelperManager
{
    const GLOBAL_KEY = 'global_helper_manager';

    /**
     * コンストラクタ
     *
     * @param
     * @return void
     */
    public function __construct (Environment $env = null)
    {
        $this->env = $env;
    }

    public function map ($name, $command)
    {
        if ($this->isMaped($name)) {
            throw new Exception(array('%sは既にマップされています',$name));
        }
        $this->remap($name, $command);
    }

    public function remap ($name, $command)
    {
        if (is_string($command)) {
            $command = array($this->env,$command);
        }
        $this->commands[$name] = $command;
    }

    public function isMaped($name) 
    {
        if (isset($this->commands[$name])) return true;

        $global = Kernel::registry()->get(self::GLOBAL_KEY);
        if (is_object($global) && $global !== $this && $global->isMaped($name)) {
            $this->map($name, $global->get($name));
            return true;
        }

        return false;
    }

    public function get($name) 
    {
        if ($this->isMaped($name)) {
            $command =  $this->commands[$name];
            if (is_array($command) && is_string($command[0]) && !is_callable($command)) {
                $command[0] = $this->env->getComponent($command[0]);
            }
            return $command;
        }
        throw new Exception(array('%sは登録されていません。',$name));
    }

    public function bind ($object, $list) 
    {
        foreach ($list as $name => $command) {
            $this->map($name, array($object,$command));
        }
    }

    public function globalize( )
    {
        Kernel::registry()->set(self::GLOBAL_KEY, $this);
    }
}
