<?php

namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

/**
 * Class GreaterThan
 * @package Oilstone\RsqlParser\Operators
 */
class GreaterThan extends Operator
{
    /**
     * @var string
     */
    protected $uri = '>';

    /**
     * @var string
     */
    protected $sql = '>';
}