<?php

namespace App\Util\Rsql\Operator;

use Oilstone\RsqlParser\Operators\Operator;

class SameMonth extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=samemonth=';

    /**
     * @var string
     */
    protected $sql = 'NOT LIKE';
}
