<?php

namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

/**
 * Class GreaterThanOrEqualTo
 * @package Oilstone\RsqlParser\Operators
 */
class GreaterThanOrEqualTo extends Operator
{
    /**
     * @var string
     */
    protected $uri = '>=';

    /**
     * @var string
     */
    protected $sql = '>=';
}