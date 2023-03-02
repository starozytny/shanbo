<?php

namespace App\Controller\User;

use App\Entity\Album;
use App\Entity\Group;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/espace-membre/albums", name="user_albums_")
 */
class AlbumController extends AbstractController
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
        $objs = $em->getRepository(Album::class)->findAll();
        return $this->render('user/pages/albums/index.html.twig', ['data' => $objs]);
    }

    /**
     * @Route("/creation", name="create")
     */
    public function create(): Response
    {
        return $this->render('user/pages/albums/create.html.twig');
    }

    /**
     * @Route("/modification/{slug}", name="update")
     */
    public function update(Album $obj, SerializerInterface $serializer): Response
    {
        $obj = $serializer->serialize($obj, 'json', ['groups' => Album::ALBUMS_READ]);
        return $this->render('user/pages/albums/update.html.twig', ['element' => $obj]);
    }

    /**
     * @Route("/album/{slug}", name="read")
     */
    public function read(Album $obj, SerializerInterface $serializer): Response
    {
        $groups = $obj->getGroups();

        $obj    = $serializer->serialize($obj, 'json', ['groups' => Album::ALBUMS_READ]);
        $groups = $serializer->serialize($groups, 'json', ['groups' => Group::GROUP_REAAD]);

        return $this->render('user/pages/albums/read.html.twig', [
            'element' => $obj,
            'groups' => $groups,
        ]);
    }
}