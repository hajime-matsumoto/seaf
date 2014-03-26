<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Util\FileSystem;

class File implements \Iterator
{
    protected $path = "";

    /**
     * @param string path
     */
    public function __construct ($path)
    {
        $this->path = $path;
    }

    /**
     * @param string path
     */
    public function ext ($ext = null)
    {
        if ($ext != null) {
            $this->path .= '.'.$ext;
            return $this;
        }

        if (false === $p = strrpos($this->path, '.')) {
            return false;
        }

        return substr($this->path, $p+1);
    }

    /**
     * 配列にする
     *
     * @return array
     */
    public function toArray ( )
    {
        switch ($this->ext()) {
        case 'php':
            return include $this->path;
            break;
        case 'yaml':
            return yaml_parse_file($this->path);
            break;
        }
        return array();
    }

    /**
     * 書き込み可能か
     *
     * @return bool
     */
    public function isWritable ( )
    {
        return is_writable($this->path);
    }

    /**
     * 文字列にする
     *
     * @return string
     */
    public function __toString( )
    {
        return $this->path;
    }

    /**
     * ディレクトリか
     *
     * @return bool
     */
    public function isDir ( )
    {
        return is_dir($this->path);
    }
    
    /**
     * 存在するか
     *
     * @return bool
     */
    public function isExists ( )
    {
        return file_exists($this->path);
    }

    /**
     * ファイルの中身を取得する
     *
     * @return string
     */
    public function getContents ( )
    {
        return file_get_contents($this->path);
    }

    /**
     * ファイルに中身を書き込む
     *
     * @param string
     * @return string
     */
    public function putContents ($data)
    {
        file_put_contents($this->path, $data);
    }

    /**
     * ディレクトリからファイルを取得する
     *
     * @return string
     */
    public function get ($path)
    {
        return new File($this->path.'/'.$path);
    }

    /**
     * PHPの実行結果を取得
     *
     * @return string
     */
    public function includeWithVars ($vars)
    {
        extract($vars);

        ob_start();
        include $this->path;
        return ob_get_clean();
    }

    /**
     * Requireする
     */
    public function requireOnce ( )
    {
        require_once $this->path;
    }

    /**
     * 削除する
     */
    public function unlink ( )
    {
        unlink ($this->path);
    }

    /**
     * ベース名を取得
     *
     * @param bool 拡張子を消すフラグ
     */
    public function basename($withExt = true)
    {
        $name = basename($this->path);

        if ($withExt) return $name;
        return substr($name, 0, strrpos($name, '.'));
    }

    //----------------------------
    // ArrayAccess
    //----------------------------

    public function current( )
    {
        return Base::factory($this->path.'/'.$this->current_file);
    }

    public function rewind( )
    {
        $this->dir = dir($this->path);
        $this->idx = 0;
    }

    public function next( )
    {
        $this->idx++;
    }

    public function key( )
    {
        return $this->idx;
    }

    public function valid( )
    {
        $this->current_file = $this->dir->read();
        if ($this->current_file{0} == '.') {
            return $this->valid();
        }
        return $this->current_file;
    }
}
