<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 */
namespace Seaf\Base\Command;

/**
 * コマンドリクエスト
 */
class Request implements CommandProxyIF
{
    private $scope;
    private $name;
    private $params = [];

    /**
     * @var Seaf\Base\Command\ResultIF
     */
    private $result;

    /**
     * @var Seaf\Base\Command\CommanderIF
     */
    private $commander;

    /**
     * コンストラクタ
     */
    public function __construct (CommanderIF $commander)
    {
        $this->commander = $commander;
    }

    /**
     * スコープを追加する
     */
    public function __get($name)
    {
        $this->addScope($name);
        return $this;
    }

    /**
     * リクエストをハンドラに送信する
     */
    public function __call($name, $params)
    {
        $this->setName($name);
        $this->setParams($params);
        $result = $this->execute();
        return $result->getReturnValue();
    }

    /**
     * @See Seaf\Base\Command\CommandIF
     */
    public function execute ( )
    {
        // コマンダーに実行を通知
        $this->getCommander( )->recieveRequest($this);
        return $this->getResult( );
    }

    // ========================================
    // オブジェクト設定用のメソッド群
    // ========================================

    public function scope ($scope)
    {
        $this->setScope($scope);
        return $this;
    }

    public function name ($name)
    {
        $this->setName($name);
        return $this;
    }


    public function param ($name, $value = null)
    {
        $this->setParam($name, $value = null);
        return $this;
    }

    // ========================================
    // コマンダーとのやり取り
    // ========================================

    /**
     * コマンダーを取得
     *
     * @return Seaf\Base\Command\Commander
     */
    public function getCommander ( )
    {
        return $this->commander;
    }

    // ========================================
    // セッター
    // ========================================

    /**
     * @See Seaf\Base\Command\CommandProxyIF
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * @See Seaf\Base\Command\CommandProxyIF
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @See Seaf\Base\Command\CommandProxyIF
     */
    public function setParam ($name, $value = null)
    {
        if (is_array($name)) return $this->setParams($name);

        $this->params[$name] = $value;
    }

    /**
     * @See Seaf\Base\Command\CommandProxyIF
     */
    public function setParams ($params)
    {
        foreach ($params as $k => $v)
        {
            $this->params[$k] = $v;
        }
    }

    /**
     * @todo interfaceへ
     * @See Seaf\Base\Command\CommandProxyIF
     */
    public function setResult(Result $result)
    {
        $this->result = $result;
    }

    // ========================================
    // ゲッター
    // ========================================

    /**
     * @See Seaf\Base\Command\CommandProxyIF
     */
    public function getResult ( )
    {
        return $this->result;
    }

    /**
     * @TODO interface
     * @See Seaf\Base\Command\CommandProxyIF
     */
    public function getScope ( )
    {
        return $this->scope;
    }

    public function getName ( )
    {
        return $this->name;
    }

    public function getParams ( )
    {
        return $this->params;
    }

    // ========================================
    // utility
    // ========================================

    /**
     * スコープを追加
     */
    public function addScope ($value)
    {
        $scope = $this->getScope( );
        if (!empty($scope) && !is_array($scope)) {
            $scope = [$scope];
        }
        $scope[] = $value;
        $this->setScope($scope);
    }

    /**
     * スコープを取得
     */
    public function getScopes ( )
    {
        $scope = $this->getScope();
        if (is_array($scope)) {
            return $scope;
        }
        return [$scope];
    }

    /**
     * スコープを文字列に変換
     *
     * 第一引数にセパレータ
     * 第二引数にスコープをアッパーキャメルにするかどうかの真偽値
     *
     * @param string
     * @param bool
     * @return string
     */
    public function getScopeToString ($sep = '.', $useUcFirst = true)
    {
        $parts = [];
        foreach ($this->getScopes( ) as $scope) {
            $parts[] = $useUcFirst ? ucfirst($scope): $scope;
        }
        return implode($sep, $parts);
    }

}
