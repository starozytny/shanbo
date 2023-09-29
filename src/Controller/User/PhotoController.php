<?php

namespace App\Controller\User;

use App\Entity\Photo;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $objs = $em->getRepository(Photo::class)->findBy([], ['id' => 'DESC']);
        return $this->render('user/pages/photos/index.html.twig', ['data' => $objs]);
    }
}
