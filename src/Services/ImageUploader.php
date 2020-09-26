<?php


namespace App\Services;


use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

class ImageUploader extends AbstractController
{
    public function CheckImagesRequirments(File $file){

        $guessExtension = $file->guessExtension();
        list($width, $height) = getimagesize($file);

//        dd($width,$height,$guessExtension,$file->getSize());
        if ($guessExtension !== 'jpg' && $guessExtension !== 'jpeg' && $guessExtension !== 'png') {  // możliwe formaty
            return false; }
        if ($width > 6000 || $height > 5000) { //rozdzielczosc w pikselach . 	6000 × 4000 Pixels = 24 Mpx
            return false; }
        if ($file->getSize() >  6291456) { //4614400
            return false; }

        return true;
    }

    public function UploadImage(File $image){

        $guessExtension = $image->guessExtension();
        $newimage = new Image();
        $newimage->setId();
        $createdAt=$newimage->getId()->getDateTime();
        $directory = $this->getParameter('images_directory')."/" . $createdAt->format("Y") . '/' . $createdAt->format("m");
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = bin2hex(random_bytes(20));
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $guessExtension;

        try {
            $image->move(
                $directory,
                $newFilename
            );
        } catch (FileException $e) {
        }

        $newimage->setName($newFilename);
        $newimage->setOryginalFileName($originalFilename);
//        $newimage->setFilePath($directory . '/' . $newFilename);

        return $newimage;
    }

}