<?php


namespace App\Service\Data;


use App\Entity\Album;
use App\Entity\Group;
use App\Service\SanitizeData;

class DataAlbum
{
    public function __construct(private readonly SanitizeData $sanitizeData)
    {
    }

    public function setDataAlbum(Album $obj, $data): Album
    {
        return ($obj)
            ->setSlug(null)
            ->setName($this->sanitizeData->sanitizeString($data->name))
        ;
    }

    public function setDateGroup(Group $obj, $data): Group
    {
        return ($obj)
            ->setName($this->sanitizeData->sanitizeString($data->name))
        ;
    }
}
