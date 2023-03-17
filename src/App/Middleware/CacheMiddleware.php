<?php
namespace App\Middleware;

use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class CacheMiddleware implements MiddlewareInterface
{
    public function __construct(
        private TagAwareCacheInterface $cache,
    ) { }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if (!$message instanceof CachableQueryResult) {
            return $this->continue($envelope, $stack);
        }

        $cacheContexts = $message->getCacheContexts();

        $item = $this->cache->getItem(
            $cacheContexts['id']
        );
        
        if (!$item->isHit()) {
            $item->set($this->continue($envelope, $stack));
            $item->tag($cacheContexts['tags']);
            $this->cache->save($item);
        }

        return $item->get();
    }

    private function continue(Envelope $envelope, StackInterface $stack): Envelope
    {
        return $stack->next()->handle($envelope, $stack);
    }
}