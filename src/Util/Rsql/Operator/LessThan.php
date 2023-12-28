<?php

namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

/**
 * Class LessThan
 * @package Oilstone\RsqlParser\Operators
 */
class LessThan extends Operator
{
    /**
     * @var string
     */
    protected $uri = '<';

    /**
     * @var string
     */
    protected $sql = '<';
}