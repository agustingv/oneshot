<?php

namespace OneShot\Application\Post\Command;

use OneShot\Domain\Post\PostRepository;
use OneShot\Application\Post\Command\DeletePostCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeletePostCommandHandler
{
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeletePostCommand $command) : void
    {
        try {
            $this->repository->delete($command->post);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
   
}