<?php
namespace App\Service\File;

// use Gaufrette\Filesystem;
use InvalidArgumentException;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\MountManager;
// use League\Flysystem\Filesystem;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Bridge\Twig\Extension\HttpFoundationExtension;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\PathString;

// \Overblog\GraphQLBundle\Upload\Type\GraphQLUploadType::class;
class Uploader implements UploaderInterface
{


    protected $parameters;
    protected $assetsHelper;
    protected $httpHelper;

    protected $slugger;


    public function __construct(
        ParameterBagInterface $parameters,
        // AssetExtension $assetsHelper,
        HttpFoundationExtension $httpHelper,
        SluggerInterface $slugger,
        // private Filesystem $filesystem,
        private Filesystem $defaultStorage,
        #[Target('default_filesystem')]
        private FilesystemOperator $filesystem,
        // private MountManager $mountManager,
        private UrlGeneratorInterface $urlGenerator,
        // private Filesystem $filesystem,
        )
    {
        $this->parameters = $parameters;
        // $this->assetsHelper = $assetsHelper;
        $this->httpHelper = $httpHelper;
        $this->slugger = $slugger;



        // $this->defaultStorage->temporaryUrl()


        // /** @AssetExtension */
        // $assetsHelper = $container->get('twig.extension.assets');
        // $this->assetsHelper = $assetsHelper;
        // /** @var HttpFoundationExtension */
        // $httpHelper = $container->get('twig.extension.http_foundation');
        // $this->httpHelper = $httpHelper;
    }




    public function upload(UploadedFile $file, string $path = '', ?string $newName = null): string
    {



        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        if (null === $newName) {
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->slugger->slug($originalFilename);
            $newName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        }

        // $publicDir = $this->parameters->get('http.public_dir');
        $absoluteUploadDir =  '';//$this->parameters->get('file.upload.dir');
        $uploadDir = $absoluteUploadDir;

        // $this->assetsHelper-

        $prefix = '';//'local://';
        
        $uploadDir = ($uploadDir?? '') . '/' . $path;

        $filePath = $uploadDir . '/' .$newName;
        $filePath = preg_replace(['~/+~','~^/|/$~'],['/',''],$filePath);

        $filePath = "{$prefix}".$filePath;
        
        $this->filesystem->write($filePath,$file->getContent());

        // $mount = $this->mountManager->
        
        // $filesystem = $this->mountManager->
        // $this->defaultStorage->write($filePath,$file->getContent());

        // $this->filesystem->publicUrl();


        return $filePath;

        // $newFile = $file->move($uploadDir, $newName);
        // $newFileFullPath = $newFile->getPathname();

        // $relativePath = $this->getRelativePath($absoluteUploadDir, $newFileFullPath);

        // $relativePath = preg_replace('~(\\\\)+~u', '/', $relativePath);

        // // return $relativePath;
        
        // $rpath = $this->assetsHelper->getAssetUrl($relativePath);
        // return $rpath;
        // return $this->httpHelper->generateAbsoluteUrl($rpath);
    }


    
    function getRelativeUrl(string $path): string{
        return $this->urlGenerator->generate('app_file_read',[ 'path' => $path]);
    }

    function getAbsoluteUrl(string $path): string{
        $relativeUrl = $this->getRelativeUrl($path);
        return $this->httpHelper->generateAbsoluteUrl($relativeUrl);
    }



    protected function getRelativePath(string $original, string $file): string
    {
        // $file1 = '/path/to/file1.txt';
        // $file2 = '/path/to/subdirectory/file2.txt';

        $dir1 = dirname(realpath($original));
        $dir2 = dirname(realpath($file));

        // Get the common ancestor directory
        $commonDir = '';
        for ($i = 0; $i < min(strlen($dir1), strlen($dir2)); $i++) {
            if ($dir1[$i] !== $dir2[$i]) {
                break;
            }
            // if($i !== 0)
            $commonDir .= $dir1[$i];
        }

        // Construct the relative path of file2 with respect to file1
        $relativePath = substr($file, strlen($commonDir) + 1);

        return $relativePath;

    }




    public function remove(string $url)
    {


        $publicURL = $this->httpHelper->generateAbsoluteUrl($this->assetsHelper->getAssetUrl('/'));




        $ihost = parse_url($publicURL, PHP_URL_HOST);
        $iport = parse_url($publicURL, PHP_URL_PORT);
        $ipath = parse_url($publicURL, PHP_URL_PATH);

        $host = parse_url($url, PHP_URL_HOST);
        $port = parse_url($url, PHP_URL_PORT);
        $path = parse_url($url, PHP_URL_PATH);


        if (($ihost == $host && $iport == $port) || !$host) {


            $publicDir = $this->parameters->get('public_dir');


            $lpath = $publicDir . '/' . $path;


            if (file_exists($lpath))
                unlink($lpath);
            // else 
            //     throw new \RuntimeException("Could not find target file \"{$lpath}\"");


        }


    }

}