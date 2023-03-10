<?php

namespace OneShot\Infraestructure\Domain\Tag;

use OneShot\Domain\Tag\Tag;
use OneShot\Domain\Tag\TagRepository;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Cache\CacheInterface;


class FirebaseTagRepository implements TagRepository
{
    private const ORDER_ASC = 1;
    private const ORDER_DESC = 2;
    private $firestoreDb;

    private string $collection = 'tags';
    private CacheInterface $redisCache;

    public function __construct(ContainerInterface $container,CacheInterface $redisCache)
    {
        $this->firestoreDb = $container->get('kreait_firebase.oneshot.firestore')->database();
        $this->redisCache = $redisCache;
    }

    public function find_by_id(string $id) : ?Tag
    {
        $data = $this->firestoreDb
        ->collection($this->collection)
        ->document($id)
        ->snapshot()
        ->data();

        $tag = new Tag();
        return $tag->fromArray($data);

    }

    public function find_by_name (string $name) : array
    {
        $reference = $this->firestoreDb->collection($this->collection);
        $query = $reference
        ->orderBy('name', 1)
        ->where('name', '>=', $name)
        ->where('name', '<=', $name."~");
        $documents = $query->documents();

        $tags = [];
        foreach ($documents as $document)
        {
            $data = $document->data();
            $tag = new Tag();
            $tags[] = $tag->fromArray($data);
        }
        return $tags;
    }

    public function find_by_ids(array $ids) : array
    {
        $reference = $this->firestoreDb->collection($this->collection);
        $query = $reference->where('id', 'in', $ids);
        $documents = $query->documents();

        $tags = [];
        foreach ($documents as $document)
        {
            $data = $document->data();
            $tag = new Tag();
            $tags[] = $tag->fromArray($data);
        }
        return $tags;
    }
    
    public function create(Tag $tag) : void
    {
        $this->firestoreDb
        ->collection($this->collection)
        ->document($tag->getId())
        ->set($tag->toArray());
    }

    public function delete(Tag $tag) : void
    {
        $this->firestoreDb
        ->collection($this->collection)
        ->document($tag->getId())
        ->delete();
    }

    public function update(Tag $tag) : void
    {
        $data = $this->firestoreDb
        ->collection($this->collection)
        ->document($tag->getId());
        $data->set($tag->toArray());
    }
}