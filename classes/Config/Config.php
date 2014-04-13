<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Config;

use Seaf\Base;
use Seaf\Cache;

class Config
{
    use Base\SeafAccessTrait;
    use Base\CacheTrait;
    use Base\LoggerTrait;

    const DEFAUOT_CONFIG_NAME = 'setting';
    const DEFAULT_SECTION = 'default';

    private $dir;
    private $configContainers = [];
    private $section = 'development';

    public static function factory ($cfg)
    {
        $dir = $cfg['dir'];

        $Config = new Config($dir);
        return $Config;
    }

    public function __construct ($dir)
    {
        $this->dir = $this->sf()->FileLoader()->dir($dir);
        $this->load(self::DEFAUOT_CONFIG_NAME);
        $this->section($this->sf()->Reg()->get('env'));
    }

    public function section($section)
    {
        $this->section = $section;
        return $this;
    }

    public function load ($name)
    {
        $file = $this->dir->find($name.".yaml");

        // キャッシュ機能を有効にする
        $this->configContainers = $this->useCache(
            $file->getFileName(),
            function ( ) use ($file) {
                if (!$file->isExists()) {
                    throw new Exception\InvalidFileName([
                        'File %s は読み込めません', $file
                    ]);
                }

                // データを取り込む
                $array = $file->toArray();

                // セクション毎にコンフィグコンテナを作成する
                $configContainers = [];
                foreach ($array as $section=>$values) {
                    $configContainers[$section] = new ConfigContainer($values);
                }
                return $configContainers;
            }, 0, $file->mtime(), $cache_status
        );

        $this->debug('Cache-Status', $cache_status);
    }

    public function __invoke($name)
    {
        return $this->get($name);
    }

    public function get($name, $default = null)
    {
        if (
            isset($this->configContainers[$this->section]) &&
            $this->configContainers[$this->section]->has($name)
        ){
            $result = $this->configContainers[$this->section]->get($name);
        }else{
            $result =  $this->configContainers[self::DEFAULT_SECTION]->get($name, $default);
        }

        if (!$result) return $default;

        return $this->configFilter($result);

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
