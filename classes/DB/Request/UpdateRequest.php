<?php // vim: set ft=php ts=4 sts=4 sw=4 et:

namespace Seaf\DB\Request;

use Seaf\DB;

class UpdateRequest extends FindRequest
{
    protected $type = 'UPDATE';
}
