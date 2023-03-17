<?php

namespace App\Middleware;

interface CachableQueryResult
{
    public function getCacheContexts(): array;
}