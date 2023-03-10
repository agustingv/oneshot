<?php

namespace OneShot\Application\User\Query;

class SingInUserQuery
{
    public function __construct(
        string $mail,
        string $password
    ) {
        $this->mail = $mail;
        $this->password = $password;
    }
}