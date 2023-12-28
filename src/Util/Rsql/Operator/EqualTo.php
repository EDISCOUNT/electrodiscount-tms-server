<?php
namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

class EqualTo extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=eq=';

    /**
     * @var string
     */
    protected $sql = '=';
}