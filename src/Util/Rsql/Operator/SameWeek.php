<?php

namespace App\Util\Rsql\Operator;

use Oilstone\RsqlParser\Operators\Operator;

class SameWeek extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=sameweek=';

    /**
     * @var string
     */
    protected $sql = 'NOT LIKE';
}
