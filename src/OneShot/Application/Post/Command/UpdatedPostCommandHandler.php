<?php

namespace OneShot\Application\Post\Command;

use OneShot\Domain\Post\PostRepository;
use OneShot\Application\Post\Command\UpdatedPostCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdatedPostCommandHandler
{
    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdatedPostCommand $command) : void
    {
        try {
            $this->repository->updated($command->post);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
   
}