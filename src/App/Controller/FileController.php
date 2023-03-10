<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\HttpFoundation\RedirectResponse;
use OneShot\Domain\File\File;
use OneShot\Domain\ValueObjects\EntityId;
use OneShot\Application\File\Command\CreateFileCommand;
use App\Files\FileUpload;
use Symfony\Component\HttpFoundation\JsonResponse;


class FileController extends AbstractController
{

    private MessageBusInterface $messageBus;
    
    public function __construct(MessageBusInterface $messageBus)
    {
      $this->messageBus = $messageBus;
    }

  /**
   * @Route("/drop/upload")
   */
    public function uploadFromTextarea(Request $request) : Response
    {       
        $file = $request->files->get('upload');
        $token = $request->request->get('token');

        if ($this->isCsrfTokenValid('post', $token)) 
        {
          if (!empty($file)) 
          {
            $newFile = FileUpload::prepareNewFiles($file, 'posts/images/');
            try {
                $command = new CreateFileCommand(
                  file: $newFile,
                  upFile: $file
                );
                $envelope = $this->messageBus->dispatch($command);
                $handledStamp = $envelope->last(HandledStamp::class);
                $fileUrl = $handledStamp->getResult();
                return new JsonResponse(['url' => $fileUrl, 'id' => $newFile->getId()]);
    
              } catch (\Exception $e) {
                $errors[] = $e->getMessage();
                return new JsonResponse(['url' => $errors]);
              }               
          }
      } else {
        return new JsonResponse(['url' => 'invalid token']);
      }
      $response = new JsonResponse(['url' => 'filename']);
      return $response;
    }
}