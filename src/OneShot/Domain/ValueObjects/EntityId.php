<?php

namespace OneShot\Domain\ValueObjects;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Generate and checks uuids for entities
 */
class EntityId
{
    private string $id;

    private function __construct(
        string $id
    ) {
        $this->id = $id;
    }

    public static function generate() : self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id) : self
    {
        if (false === Uuid::isValid($id))
        {
            throw new ResourceNotFoundException();
        }

        return new self($id);
    }

    public function toString(): string
    {
        return $this->id;
    }
}
