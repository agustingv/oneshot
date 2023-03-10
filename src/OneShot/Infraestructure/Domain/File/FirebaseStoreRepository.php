<?php

namespace OneShot\Infraestructure\Domain\File;

use OneShot\Domain\File\File;
use OneShot\Domain\File\FileRepository;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FirebaseStoreRepository implements FileRepository
{
    private $firestoreDb;
    private $storage;

    private string $collection = 'files';
    private CacheInterface $redisCache;

    public function __construct(ContainerInterface $container,CacheInterface $redisCache)
    {
        $this->firestoreDb = $container->get('kreait_firebase.oneshot.firestore')->database();
        $this->storage = $container->get('kreait_firebase.oneshot.storage');

        $this->redisCache = $redisCache;
    }

    public function find_by_id(string $id) : ?File
    {

    }

    public function create(File $file, UploadedFile $upFile) : string
    {
        $bucket = $this->storage->getBucket();
        $f = file_get_contents($upFile->getRealPath());
        $object = $bucket->upload($f, [
            'name' => $file->getPath(),
            'predefinedAcl' => 'publicRead'
        ]);
        $url = 'https://storage.googleapis.com/oneshot-62f66.appspot.com/'.$file->getPath();
        
        $this->firestoreDb
        ->collection($this->collection)
        ->document($file->getId())
        ->set($file->toArray());

        return $url;
    }

    public function update(File $file) : void
    {

    }

    public function delete(File $file) : void
    {

    }

}