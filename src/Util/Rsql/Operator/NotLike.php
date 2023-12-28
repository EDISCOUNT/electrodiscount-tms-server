<?php
namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

class NotLike extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=notlike=';

    /**
     * @var string
     */
    protected $sql = 'NOT LIKE';
}