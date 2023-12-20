<?php
namespace App\Service\Util;


class DefaultCodeGenerator implements CodeGeneratorInterface{
    
	/**
	 * @param null|string $prefix
	 * @return string
	 */
	public function generateCode(?string $prefix = null, ?int $length = null, $retainCase = false): string {
        // return uniqid($prefix, true);
		$length ??= 6;
		$code =  base_convert(bin2hex(random_bytes($length)),16,36);
		if(!$retainCase)
			$code = strtoupper($code);

		return $code;
	}
}