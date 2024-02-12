<?php


namespace App\Service;


use PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException;
use PHPImageWorkshop\Exception\ImageWorkshopException;
use PHPImageWorkshop\ImageWorkshop;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(private $publicDirectory, private $privateDirectory, private readonly SluggerInterface $slugger)
    {
    }

    public function upload(UploadedFile $file, $folder=null, $isPublic=true, $reducePixel=false): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $directory = $isPublic ? $this->getPublicDirectory() : $this->getPrivateDirectory();
            $directory = $directory . '/' . $folder;

            if($directory){
                if(!is_dir($directory)){
                    mkdir($directory, 0777, true);
                }
            }

            $file->move($directory, $fileName);

            $fileOri = $directory . "/" . $fileName;

            $layer = ImageWorkshop::initFromPath($fileOri);

            if($reducePixel){
                $layer->resizeInPixel(null, $reducePixel, true);
            }else if($layer->getHeight() > 2160){
                $layer->resizeInPixel(null, 2160, true);
            }

            $layer->save($directory, $fileName);
        } catch (FileException|ImageWorkshopException|ImageWorkshopLayerException) {
            return false;
        }

        return $fileName;
    }

    /**
     * @throws ImageWorkshopLayerException
     * @throws ImageWorkshopException
     */
    public function thumbs($fileName, $folderImages, $folderThumbs, $width = null, $height = 755): string
    {
        if($folderThumbs){
            if(!is_dir($folderThumbs)){
                mkdir($folderThumbs, 0777, true);
            }
        }

        $fileOri = $folderImages . "/" . $fileName;

        $layer = ImageWorkshop::initFromPath($fileOri);
        $layer->resizeInPixel($width, $height, true);

        $fileName = "thumbs-" . $fileName;

        $layer->save($folderThumbs, $fileName);

        return $fileName;
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

            $fileName = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME); // useless because uniqid();
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
