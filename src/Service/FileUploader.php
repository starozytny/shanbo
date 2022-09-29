<?php


namespace App\Service;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $publicDirectory;
    private $privateDirectory;
    private $slugger;

    public function __construct($publicDirectory, $privateDirectory, SluggerInterface $slugger)
    {
        $this->publicDirectory = $publicDirectory;
        $this->privateDirectory = $privateDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, $folder=null, $isPublic=true): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            if($folder){
                if(!is_dir($folder)){
                    mkdir($folder);
                }
            }

            $directory = $isPublic ? $this->getPublicDirectory() : $this->getPrivateDirectory();
            $directory = $directory . '/' . $folder;

            $file->move($directory, $fileName);
        } catch (FileException $e) {
            return false;
        }

        return $fileName;
    }

    public function createThumb($type, $source, $destination, $tailleW, $tailleH): string
    {
        $sourceDirectory = $type == "public" ? $this->getPublicDirectory() : $this->getPrivateDirectory();
        $source = $sourceDirectory . $source;

        list($width, $height) = getimagesize($source);
        if ($width < $height) { // == portrait
            $tailleH = $tailleH + 50;
        }

        $ratio_orig = $width/$height;
        $w = $tailleW;
        $h = $tailleH;

        if ($w/$h > $ratio_orig) {
            $w = $h*$ratio_orig;
        } else {
            $h = $w/$ratio_orig;
        }

        ini_set('gd.jpeg_ignore_warning', true);
        $src = @imagecreatefromjpeg($source);
        $thumb = imagecreatetruecolor($w, $h);
        @imagecopyresampled($thumb, $src, 0, 0, 0, 0, $w, $h, $width, $height);

        $nameWithoutExt = pathinfo($source)['filename'];
        $name = $nameWithoutExt . '.' . pathinfo($source)['extension'];

        @imagejpeg($thumb, $destination . "/" . $name,75);

        return $name;
    }

    public function deleteFile($fileName, $folderName, $isPublic = true)
    {
        if($fileName){
            $file = $this->getDirectory($isPublic) . $folderName . '/' . $fileName;
            if(file_exists($file)){
                unlink($file);
            }
        }
    }

    public function replaceFile($file, $oldFileName, $folderName, $isPublic = true): ?string
    {
        if($file){
            $oldFile = $this->getDirectory($isPublic) . $folderName . '/' . $oldFileName;

            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // useless because uniqid();
            if($oldFileName && file_exists($oldFile) && $fileName !== $oldFileName){
                unlink($oldFile);
            }

            return $this->upload($file, $folderName, $isPublic);
        }

        return null;
    }

    private function getDirectory($isPublic)
    {
        $path = $this->privateDirectory;
        if($isPublic){
            $path = $this->publicDirectory;
        }

        return $path;
    }

    public function getPublicDirectory()
    {
        return $this->publicDirectory;
    }

    public function getPrivateDirectory()
    {
        return $this->privateDirectory;
    }


}
