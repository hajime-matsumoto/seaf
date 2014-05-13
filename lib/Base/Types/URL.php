<?php // vim: set ft=php ts=4 sts=4 sw=4 et:
/**
 * Seaf Project
 *
 * 型ライブラリ
 */
namespace Seaf\Base\Types;

use Seaf\Util\Util;
/**
 * URL型
 */
class URL extends SeparatedString
{
    private $data = [];
    private $query = '';
    private $server = 'http://localhost';

    public function __construct($default = [])
    {
        parent::__construct('/', $default);
    }

    public function init ($url)
    {
        $p = Util::Dictionary(parse_url($url));
        $this->server = sprintf(
            '%s://%s%s',
            $p->get('scheme','http'),
            $p->get('host'),
            (
                ($p->isEmpty('port') || $p->get('port') == 80) ? 
                "": 
                ":".$p->get('port')
            )
        );
        $this->query = $p->get('query');
        $this->initPath($p->get('path'));
    }

    public function getQuery( )
    {
        return $this->query;
    }

    public function initPath($path)
    {
        parent::init($path);
    }

    public function toPath( )
    {
        $path = parent::__toString();
        if ($path) {
            if ($path{0} != '/') {
                $path ="/".$path;
            }
            return $path;
        }
        return '/';
    }

    public function __toString( )
    {
        $string = parent::__toString();

        if (empty($string)) {
            $string = '/';
        }else{
            if ($string{0} != '/') {
                $string = '/'.$string;
            }
        }
        return $this->server.$string;
    }
}
