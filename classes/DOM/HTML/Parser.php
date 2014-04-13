<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DOM\HTML;

if (!function_exists('file_get_html')) {
    require_once __DIR__.'/lib/simplehtmldom/simple_html_dom.php';
}

class Parser
{
    /**
     * パース
     *
     * @param string
     */
    public static function parse ($html)
    {
        return str_get_html($html);
    }
}
