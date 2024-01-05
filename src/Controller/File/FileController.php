<?php

namespace App\Controller\File;

use App\Service\File\UploaderInterface;
// use League\Flysystem\FilesystemOperator;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\MountManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileController extends AbstractController
{

    public function __construct(
        private UploaderInterface $uploader,
        #[Target('default_filesystem')]
        private FilesystemOperator $defaultStorage,
        // private Filesystem $defaultStorage,
        // private MountManager $mountManager,
    ) {
    }

    #[Route('/file/', name: 'app_file_upload')]
    public function upload(Request $request): JsonResponse
    {

        /**
         * @var UploadedFile
         */
        $file = $request->files->get('file');

        if ($file) {

            // Move the file to the directory where brochures are stored
            try {
                $path = $this->uploader->upload($file);
                $url = $this->uploader->getAbsoluteUrl($path);
            } catch (FileException $e) {
                throw $e;
                // ... handle exception if something happens during file upload
            }

            // updates the 'filename' property to store the PDF file name
            // instead of its contents


            return $this->json([
                'status' => 'success', // error
                'message' => 'File Uploaded',
                'path' => $path,
                'url' => $url,
            ]);
        } else {


            return $this->json([
                'status' => 'error', // error
                'message' => 'File not provided',
            ], Response::HTTP_BAD_REQUEST);
        }
    }



    #[Route('/file/{path}', name: 'app_file_read', requirements: ['path' => '.+'])]
    public function read(Request $request, string $path): Response
    {

        if (!$this->defaultStorage->fileExists($path))
            throw new NotFoundHttpException($request->getPathInfo());



        $name = basename($path);

        $disposition = HeaderUtils::makeDisposition(
            // HeaderUtils::DISPOSITION_ATTACHMENT,
            HeaderUtils::DISPOSITION_INLINE,
            $name
        );

        $headers = [

            'Content-Type' => $this->defaultStorage->mimeType($path),
            'Content-Disposition' => $disposition,
        ];


        return new StreamedResponse(function () use ($path) {
            $stream = $this->defaultStorage->readStream($path);
            fpassthru($stream);
        }, 200, $headers);
    }
}
