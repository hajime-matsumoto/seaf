<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Core\Component;

use Seaf\Data\Container;

/**
 * セキュリティに関するヘルパ
 */
class Secure
{
    /**
     * ランダム文字列を生成する
     *
     * @param int
     * @return string Sha1
     */
    public function randomString ($size = 40)
    {
        $strong = false;
        do {
            $bytes = openssl_random_pseudo_bytes($size, $strong);
        } while ($strong == false);

        return sha1($bytes);
    }


    /**
     * ランダムパスワードを生成する
     *
     * @param int
     * @param array
     * @return string
     */
    public function randomPassword($length = 10, $chars = []) 
    {
        $size = $max = $bytes = $ret = null;

        $allowed_chars = array_merge([
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
            'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
            'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
            'U', 'V', 'W', 'X', 'Y', 'Z',
            '-', '_', '.'], $chars);

        $max = count($allowed_chars) - 1;

        if (0x7FFFFFFF < $max) {
            throw \RuntimeException('0x7FFFFFFF より大きいです');
        }
        $size = 4 * $length;

        do {
            $bytes = openssl_random_pseudo_bytes($size, $strong);
        } while ($strong == false);

        $ret = '';

        for ($i = 0; $i < $length; $i++) {
            $var = unpack('Nint', substr($bytes, $i, 4))['int'] & 0x7FFFFFFF;
            $fp = (float) $var / 0x7FFFFFFF;
            $ret.= $allowed_chars[(int) round($max * $fp)];
        }

        return $ret;
    }
}
