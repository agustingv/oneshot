<?php

namespace OneShot\Domain\Shared;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

abstract class AbstractDomainModel
{

    protected Serializer $serializer;

    public function __construct() 
    {
        $encoders = [new JsonEncoder()];
   
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }


    public function toArray() : array
    {
        return $this->serializer->normalize($this);
    }

    public function fromArray(array $data) : self
    {
        return $this->serializer->denormalize($data, Self::class);
    }

    protected function deserialize(array $data) : array
    {
        foreach ($data as $key => $item) {
            if (null === $item) 
            {
                unset($data[$key]);
                continue;
            }
            if(is_array($item)){
                if (isset($item['type']))
                {
                    $obj = new $item['type']();
                    $data[$key] = $obj->fromArray($item);
                }
            }
        }
        return $data;
    }

    public function convertToColonPath()
    {
        $array = $this->toArray();
        $new_array = [];
        foreach ($array as $key => $value)
        {
            if (is_array($value)) 
            {
                $this->recursive($key, $new_array, $value);
            } else {
                $new_array[$key] = $value;
            }
        }
        return $new_array;
    }

    private function recursive($parent_key, &$new_array, $array) {
        foreach ($array as $key => $value)
        {
            if (is_array($value)) 
            {
                $this->recursive($parent_key.':'.$key, $new_array, $value);
            } else {

                $new_array[$parent_key.':'.$key] = $value;
            }
        }
    }

}