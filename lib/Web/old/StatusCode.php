<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\Web;

/**
 * Status Code
 */
class StatusCode
{
    const OK                    = 200;
    const CREATED               = 201;
    const ACCEPTED              = 202;
    const NO_CONTENT            = 204;
    const MOVED_PERMANENTLY     = 301;
    const FOUND                 = 302;
    const TEMPORARY_REDIRECT    = 307;
    const BAD_REQUEST           = 400;
    const METHOD_NOT_ALLOWD     = 405;
    const NOT_FOUND             = 404;
    const INTERNAL_SERVER_ERROR = 500;

    /**
     * @var array HTTP status codes 
     */
    public static $codes = array(  // {{{
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    ); // }}}
}
