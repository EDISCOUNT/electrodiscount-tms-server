<?php

namespace App\Twig\Runtime\Core;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

class ImageRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private ParameterBagInterface $parameters,
        #[Target('default_filesystem')]
        private FilesystemOperator $filesystem,
    ) {
        // Inject dependencies if needed
    }


    
    public function flySystemBase64Filter($image){
        $mime = $this->filesystem->mimeType($image);
        $data = $this->filesystem->read($image);
        // 
        
        $base64 = base64_encode($data);
        return 'data:' . $mime . ';base64,' . $base64;
    }
    public function base64Filter($image)
    {
        $appPath = $this->parameters->get('kernel.project_dir');
        $image = trim($image);
        // $image = realpath($appPath . '/'. $image);
        $image = $appPath . DIRECTORY_SEPARATOR. $image;
        
        // var_dump($image);
        // die();

        // check if the image exists and is readable
        if (file_exists($image) && is_readable($image)) {
            // get the image content and encode it as base 64
            $data = file_get_contents($image);
            $base64 = base64_encode($data);

            // get the image mime type and prepend it to the base 64 string
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $image);
            finfo_close($finfo);

            return 'data:' . $mime . ';base64,' . $base64;
        }

        // return an empty string if the image is not found or not readable
        return $image;
    }
}
