<?php

namespace App\Controller\User;

use App\Entity\Album;
use App\Entity\Photo;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/espace-membre/photos", name="user_photos_")
 */
class PhotoController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/", options={"expose"=true}, name="index")
     */
    public function albums(): Response
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository(Photo::class)->findAll();
        return $this->render('user/pages/photos/index.html.twig', ['data' => $objs]);
    }
}
