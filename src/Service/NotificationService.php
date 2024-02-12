<?php


namespace App\Service;


use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function createNotification($name, $icon, $user, $url = null)
    {
        $obj = (new Notification())
            ->setName($name)
            ->setIcon($icon)
            ->setUser($user)
            ->setUrl($url)
        ;

        $this->em->persist($obj);
        $this->em->flush();
    }
   
}