<?php

namespace OneShot\Domain\ValueObjects;

class RandomUserImage 
{

    public function __construct(private string $uri) {}

    public static function generate() : void
    {
        $w = 64;
        $h = 64;
                
        $coords = [];
        foreach(range(0,127) as $p){
            $coords[] = rand(0,$w);
            $coords[] = rand(0,$h);
        }

        $image = imagecreatetruecolor($w, $h);

        imagefilledrectangle($image, 0, 0, $w, $h, imagecolorallocate($image, mt_rand(0,255) , mt_rand(0,255) , mt_rand(0,255)));

        imagefilledpolygon($image, $coords, 48, imagecolorallocate($image, mt_rand(0,255) , mt_rand(0,255) , mt_rand(0,255)));
        imagefilledpolygon($image, $coords, 24, imagecolorallocate($image, mt_rand(0,255) , mt_rand(0,255) , mt_rand(0,255)));


        header('Content-type: image/png');
        imagepng($image);
        dump($image);
    }


}