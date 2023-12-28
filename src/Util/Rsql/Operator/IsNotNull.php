<?php
namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

class IsNotNull extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=isnotnull=';

    /**
     * @var string
     */
    protected $sql = 'IS NOT NULL';
}