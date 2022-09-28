<?php


namespace App\Service\Data;


use App\Entity\Album;
use App\Service\SanitizeData;

class DataAlbum
{
    private $sanitizeData;

    public function __construct(SanitizeData $sanitizeData)
    {
        $this->sanitizeData = $sanitizeData;
    }

    public function setDataAlbum(Album $obj, $data): Album
    {
        return ($obj)
            ->setName($this->sanitizeData->sanitizeString($data->name))
        ;
    }
}
