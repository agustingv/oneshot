<?php

namespace OneShot\Application\Tag\Command;

use OneShot\Domain\Tag\TagRepository;
use OneShot\Application\Tag\Command\UpdateTagCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateTagCommandHandler
{
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(UpdateTagCommand $command) : void
    {
        try {
            $this->repository->update($command->tag);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
   
}