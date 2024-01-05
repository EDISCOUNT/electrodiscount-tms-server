<?php
namespace App\Service\File;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;




interface UploaderInterface{

    function upload(UploadedFile $file, string $path = '', ?string $newName = null): string;

    function getRelativeUrl(string $path): string;

    function getAbsoluteUrl(string $path): string;
    
}