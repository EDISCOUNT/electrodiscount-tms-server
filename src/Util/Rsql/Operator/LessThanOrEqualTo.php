<?php

namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

/**
 * Class LessThanOrEqualTo
 * @package Oilstone\RsqlParser\Operators
 */
class LessThanOrEqualTo extends Operator
{
    /**
     * @var string
     */
    protected $uri = '<=';

    /**
     * @var string
     */
    protected $sql = '<=';
}