<?php

namespace App\Util\Rsql\Operator;

use Oilstone\RsqlParser\Operators\Operator;

class SameYear extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=sameyear=';

    /**
     * @var string
     */
    protected $sql = 'NOT LIKE';
}
