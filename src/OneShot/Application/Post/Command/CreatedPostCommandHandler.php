<?php

namespace OneShot\Application\Post\Command;

use OneShot\Domain\Post\PostRepository;
use OneShot\Application\Post\Command\CreatedPostCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreatedPostCommandHandler
{
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreatedPostCommand $command) : void
    {
        try {
            $this->repository->create($command->post);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
   
}