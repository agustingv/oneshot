<?php

namespace OneShot\Application\User\Query;

class VerifiedTokenUserQuery
{
    public function __construct(
        string $idToken,
    ) {
        $this->idToken = $idToken;
    }
}