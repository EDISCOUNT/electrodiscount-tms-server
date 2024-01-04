<?php

namespace App\Util\Rsql\Operator;

use Oilstone\RsqlParser\Operators\Operator;

class SameDay extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=sameday=';

    /**
     * @var string
     */
    protected $sql = 'NOT LIKE';
}
