<?php

namespace OneShot\Application\Tag\Command;

use OneShot\Domain\Tag\TagRepository;
use OneShot\Application\Tag\Command\DeleteTagCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteTagCommandHandler
{
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(DeleteTagCommand $command) : void
    {
        try {
            $this->repository->delete($command->tag);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
   
}