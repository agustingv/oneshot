<?php

namespace OneShot\Infraestructure\Domain\Post;

use OneShot\Domain\Post\Post;
use OneShot\Domain\Post\PostRepository;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Cache\CacheInterface;


class FirebasePostRepository implements PostRepository
{
    private const ORDER_ASC = 1;
    private const ORDER_DESC = 2;
    private $firestoreDb;

    private string $collection = 'posts';
    private CacheInterface $redisCache;

    public function __construct(ContainerInterface $container,CacheInterface $redisCache)
    {
        $this->firestoreDb = $container->get('kreait_firebase.oneshot.firestore')->database();
        $this->redisCache = $redisCache;
    }
  
    public function find_by_id(string $id) : ?Post
    {
        $data = $this->firestoreDb
        ->collection($this->collection)
        ->document($id)
        ->snapshot()
        ->data();

        $post = new Post();
        return $post->fromArray($data);

    }

    public function find_by_tag(array $tag) : array
    {
        $reference = $this->firestoreDb->collection($this->collection);
        $query = $reference->where("tags", "array-contains", $tag);
        $documents = $query->documents();

        $posts = [];
        foreach ($documents as $document)
        {
            $post = new Post();
            $data = $document->data();
            $posts[] = $post->fromArray($data);
        }
        return $posts;   
    }

    public function find_pager(int $page, int $items_page) : array
    {
        $page = ($page === 0)? $page = 'first' : $page;
        $reference = $this->firestoreDb->collection($this->collection);
        $query = $reference
            ->orderBy('createdAt', self::ORDER_DESC)
            ->limit($items_page)
            ->startAfter([$page]);
        $documents = $query->documents();
        $posts = [];
        foreach ($documents as $document)
        {
            $data = $document->data();
            $post = new Post();
            $posts[] = $post->fromArray($data);
        }
        return $posts;

    }

    public function create(Post $post) : void
    {
        $this->firestoreDb
        ->collection($this->collection)
        ->document($post->getId())
        ->set($post->toArray());

    }
    public function update(Post $post) : void
    {
        $data = $this->firestoreDb
        ->collection($this->collection)
        ->document($post->getId());
        $data->set($post->toArray());

    }

    public function delete(Post $post) : void
    {
        $this->firestoreDb
        ->collection($this->collection)
        ->document($post->getId())
        ->delete();

    }

}