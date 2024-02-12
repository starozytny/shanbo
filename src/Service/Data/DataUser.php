<?php


namespace App\Service\Data;


use App\Entity\User;
use App\Service\SanitizeData;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DataUser
{
    public function __construct(private readonly SanitizeData $sanitizeData)
    {
    }

    public function setData(User $obj, $data): User
    {
        if (isset($data->roles)) {
            $obj->setRoles($data->roles);
        }

        $username = isset($data->username) ? $this->sanitizeData->fullSanitize($data->username) : $data->email;

        return ($obj)
            ->setUsername($username)
            ->setFirstname(ucfirst((string) $this->sanitizeData->sanitizeString($data->firstname)))
            ->setLastname(mb_strtoupper((string) $this->sanitizeData->sanitizeString($data->lastname)))
            ->setEmail($data->email)
        ;
    }
}