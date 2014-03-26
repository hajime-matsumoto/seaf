<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
namespace Seaf\Data\Config;

use Seaf;
use Seaf\Data\Container;

class Base
{
    /**
     * データ
     * @var array
     */
    public $data = array();

    /**
     * 環境の名前
     * @var string
     */
    public $envname = 'development';

    /**
     * デフォルトセクション名
     * @var string
     */
    private $default_section = 'default';

    /**
     * コンストラクタ
     */
    public function __construct ( )
    {
    }

    /**
     * 環境名を取得する
     */
    protected function envname ( )
    {
        return Seaf::$production_mode;
    }

    /**
     * __invoke
     *
     * ->getにつなぐ
     *
     * @param string $name
     * @param mixed $default=null
     * @return mixed
     */
    public function __invoke ($name, $default = null)
    {
        return $this->get($name, $default);
    }

    /**
     * ファイルから設定を読み込む
     *
     * @param string $file
     * @return Base
     */
    public function load ($file)
    {
        $file = Seaf::FileSystem($file);

        $this->loadArray($file->toArray());
        return $this;
    }

    /**
     * 配列から設定を読み込む
     *
     * @parame array
     */
    public function loadArray ($data)
    {
        foreach ($data as $sect=>$config)
        {
            $this->data[$sect] = new Container\ArrayContainer($config);
        }
    }

    /**
     * 設定を設定する
     *
     * @param string $name
     * @param mixed $value
     */
    public function set ($name, $value)
    {
        $this->data[$this->envname()][$name] = $value;
    }


    /**
     * 設定を取得する
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $data = $this->_get($name, $default);
        if (empty($data)) {
            return $default;
        }
        return $this->configFilter($data);
    }

    /**
     * 設定を取得する (実体)
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    private function _get($name, $default)
    {
        $current = @$this->data[$this->envname()];
        $default_section = @$this->data[$this->default_section];

        if (isset($current) && $current->has($name)) {
            $data = $current->get($name);
        } elseif (
            $current != $default_section &&
            isset($default_section) &&
            $default_section->has($name)
        ) {
            $data = $default_section->get($name);
        }
        return empty($data) ? $default: $data;
    }


    /**
     * 設定返却時のフィルタ
     *
     * @param mixed $data
     * @return mixed
     */
    public function configFilter($data)
    {
        if (is_array($data)) {
            foreach ($data as $k=>$v) {
                $data[$k] = $this->configFilter($v);
            }
            return $data;
        }

        return preg_replace_callback(
            '/\$(.+)\$/U', function ($m) {
                $name = $m[1];

                if ($this->get($name, false)) {
                    return (string) $this->get($name);
                }

                if (defined($name)) return constant($name);

            },
            $data
        );
    }
}
