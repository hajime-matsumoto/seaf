<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

use Seaf\Cache;
use Seaf\Registry;

/**
 * アセットコントローラ
 */
class WebAssetController extends WebController
{
    use Cache\CacheUserTrait;

    private $coffeeCommand = 'coffee';
    private $sassCommand   = 'sass';

    protected $dirs = [];

    protected $compileOptions = [ ];

    public function setupWebController ( )
    {
        parent::setupWebController ( );

        $this->setCacheKey('web.assets');

        $this->compileOptions = [
            'coffee' => [
                '-c' => '',
                '-p' => '',
                '-s' => ''
            ],
            'sass' => [
                '--compass'=>'',
                '--cache-location' => seaf_registry_get('cache_dir'),
                '-s'=>'',
                '-E'=>'utf-8'
            ]
        ];
    }


    public function setupByConfig($cfg)
    {
        $cfg = seaf_container($cfg);
        $this->addAssetDir($cfg('dirs'));
    }

    public function addAssetDir($dir)
    {
        if (is_array($dir)) {
            foreach ($dir as $v) $this->addAssetDir($v);
            return $this;
        }
        if (!empty($dir)) {
            $this->dirs[] = $dir;
        }
        return $this;
    }

    /**
     * @return bool
     */
    protected function findRealFile($path, &$found, &$ext, &$foundExt)
    {
        $this->extMap = [
            'js' => ['js', 'coffee'],
            'css' => ['css', 'sass']
        ];

        $found = false;
        $foundExt = false;
        $ext = null;

        foreach ($this->dirs as $dir) {
            $file_path = $dir.'/'.$path;
            $ext = substr($file_path, ($p = strrpos($file_path, '.'))+1);
            $file_name = substr($file_path, 0, $p);

            if (!isset($this->extMap[$ext])) {
                throw new Exception\AssetExtentionInvalid($ext);
            }
            foreach ($this->extMap[$ext] as $neadle) {
                $try_file = $file_name.'.'.$neadle;
                if (file_exists($try_file)) {
                    $found = $try_file;
                    $foundExt = $neadle;
                    return true;
                }
            }
            return false;
        }
    }

    protected function compile($path, &$ext) 
    {
        $data = $this->getCacheHandler( )->useCacheIfNotDebug(
            $path,
            function (&$isSuccess) use ($path, &$ext){
                if (!$this->findRealFile($path, $found, $ext, $foundExt)) {
                    $this->notfound('No Assets File For This Path In ('.implode($this->dirs).')');
                    return false;
                }

                if ($ext == $foundExt) { //コンパイルの必要なし
                    return file_get_contents($found);
                }

                $method = 'compile'.ucfirst($foundExt);
                if (!method_exists($this, $method)) {
                    $this->notfound('Compile '.$foundExt.' Not Found');
                    return false;
                }

                $data = $this->$method($found);

                if (!empty($data)) {
                    $isSuccess = true;
                }

                return $data;
            }, 0, 0, $cacheStatus
        );

        $this->debug(['%s Cache: %s', $path, $cacheStatus ? 'HIT': 'MISS']);
        return $data;
    }

    protected function compileCoffee ($file)
    {
        $command = 'cat '.$file.' | '.$this->coffeeCommand.' '.$this->buildOpts(
            $this->compileOptions['coffee']
        );
        $command.= ' 2>&1 ';
        $data = exec($command, $body, $ret);
        if ($ret > 0) { // エラー
            $this->error(['Compiling Error %s', implode($body,"\n")]);
        }
        return implode($body, "\n");
    }

    protected function compileSass ($file)
    {
        $command = 'cat '.$file.' | '.$this->sassCommand.' '.$this->buildOpts(
            $this->compileOptions['sass']
        );

        foreach ($this->dirs as $dir) {
            $command.= '--load-path "'.$dir.'" ';
        }

        $command.= ' 2>&1 ';

        $data = exec($command, $body, $ret);
        if ($ret > 0) { // エラー
            $this->error(['Compiling Error %s', implode($body,"\n")]);
        }
        return implode($body, "\n");
    }

    private function buildOpts ($opts)
    {
        $option_string = '';
        foreach ($opts as $k=>$v) {
            $option_string.= "$k ";
            if (!empty($v)) {
                $option_string.= "$v ";
            }
        }
        return $option_string;
    }
}
