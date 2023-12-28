<?php
namespace App\Util\Rsql\Operator;
use Oilstone\RsqlParser\Operators\Operator;

class IsNull extends Operator
{
    /**
     * @var string
     */
    protected $uri = '=isnull=';

    /**
     * @var string
     */
    protected $sql = 'IS NULL';
}