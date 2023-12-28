<?php
namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

class Like extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=like=';

    /**
     * @var string
     */
    protected $sql = 'LIKE';
}