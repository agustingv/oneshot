<?php

namespace OneShot\Application\Tag\Command;

use OneShot\Domain\Tag\TagRepository;
use OneShot\Application\Tag\Command\CreateTagCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateTagCommandHandler
{
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateTagCommand $command) : void
    {
        try {
            $this->repository->create($command->tag);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
   
}