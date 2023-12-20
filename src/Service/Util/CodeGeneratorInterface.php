<?php

namespace App\Service\Util;

interface CodeGeneratorInterface
{
    public function generateCode(?string $prefix = null, ?int $length = null): string;
}
