<?php

namespace App\Controller\User;

use App\Entity\Photo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/espace-membre/photos', name: 'user_photos_')]
class PhotoController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    #[Route(path: '/', options: ['expose' => true], name: 'index')]
    public function albums(): Response
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository(Photo::class)->findBy([], ['id' => 'DESC']);
        return $this->render('user/pages/photos/index.html.twig', ['data' => $objs]);
    }
}
