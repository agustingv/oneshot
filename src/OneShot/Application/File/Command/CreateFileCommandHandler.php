<?php

namespace OneShot\Application\File\Command;

use OneShot\Application\File\Command\CreateFileCommand;
use OneShot\Domain\File\File;
use OneShot\Domain\File\FileRepository;
use OneShot\Domain\ValueObjects\EntityId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateFileCommandHandler
{

    public function __construct(FileRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CreateFileCommand $command) : string
    {
        try {
            return $this->repository->create($command->file, $command->upFile);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}