<?php


namespace App\Services;


use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadHelper
{

    private $blogDir  = '/Blog';
    private $maxImgSize = 1000*1000*5;
    private $authType = ["jpeg","jpg","png"];

    private $uploadPath;

    public function __construct(string $uploadPath)
    {
        $this->uploadPath = $uploadPath;
    }

    public function validateImg(UploadedFile $image){

        if ($image->getSize()>$this->maxImgSize)
            return false;
        else if (!in_array($image->guessExtension(),$this->authType)){
            return false;
        }
        return true;
    }

    public function uploadImage(UploadedFile $image, $imageName, $imageDir):File{

        $destination = $this->uploadPath.$imageDir;
        $newFilename = Urlizer::urlize($imageName)."-".uniqid().'.'.$image->guessExtension();
        return  $image->move($destination, $newFilename);
    }


    public function uploadBlogImage(UploadedFile $image, $imageName):File{
        return $this->uploadImage($image,$imageName,$this->blogDir);
    }
}
