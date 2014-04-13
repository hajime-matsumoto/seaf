<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf;
use Seaf\Exception;
use Seaf\DB\Handler;
use Seaf\FW\Web\Controller;
use Seaf\Util\Compiler;
use Seaf\Util\FileSystem;

/**
 * アセットパイプライン
 */
class AssetPipeline extends Controller
{
    private $fileSystem;
    private $compiler;


    /**
     * 作成するメソッド
     *
     * @param array
    public static function componentFactory ( )
    {
        $ap = new self();
        $c = Seaf::Config();
        $ap->addPath($c('assetpipeline'));
        return $ap;
    }
     */

    public function __construct ($cfg)
    {
        parent::__construct();
        $this->compiler = new Compiler\AssetCompiler( );
        $this->addPath($cfg['dirs']);
    }

    /**
     * アセットのパスを追加する
     */
    public function addPath($path)
    {
        $this->compiler->fileLoader->addPath($path);
    }

    public function run ($request, $response, $dispatchFlag = false)
    {
        if ($request == null) $request = $this->request();
        if ($response == null) $response = $this->response();

        // パスを取得
        $path = $request->getPath();

        // マイムタイプ
        if (true) {
        switch (FileSystem\helper::getExt($path)) {
        case 'js':
            $this->response()
                ->clear()
                ->header('Content-Type', 'text/javascript')
                ->sendHeaders();
            break;
        case 'css':
            $this->response()
                ->clear()
                ->header('Content-Type', 'text/css')
                ->sendHeaders();
            break;
        }
        }

        // コンパイル+出力
        $this->compiler->compile($path);
        Seaf::System()->halt();
    }
}
