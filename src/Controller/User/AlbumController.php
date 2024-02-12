<?php

namespace App\Controller\User;

use App\Entity\Album;
use App\Entity\Group;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/espace-membre/albums', name: 'user_albums_')]
class AlbumController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    #[Route(path: '/', name: 'index', options: ['expose' => true])]
    public function albums(): Response
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository(Album::class)->findAll();
        return $this->render('user/pages/albums/index.html.twig', ['data' => $objs]);
    }

    #[Route(path: '/creation', name: 'create')]
    public function create(): Response
    {
        return $this->render('user/pages/albums/create.html.twig');
    }

    #[Route(path: '/modification/{slug}', name: 'update')]
    public function update(Album $obj, SerializerInterface $serializer): Response
    {
        $obj = $serializer->serialize($obj, 'json', ['groups' => Album::ALBUMS_READ]);
        return $this->render('user/pages/albums/update.html.twig', ['element' => $obj]);
    }

    #[Route(path: '/album/{slug}', name: 'read')]
    public function read(Album $obj, SerializerInterface $serializer): Response
    {
        $groups = $obj->getGroups();

        $obj    = $serializer->serialize($obj, 'json', ['groups' => Album::ALBUMS_READ]);
        $groups = $serializer->serialize($groups, 'json', ['groups' => Group::GROUP_READ]);

        return $this->render('user/pages/albums/read.html.twig', [
            'element' => $obj,
            'groups' => $groups,
        ]);
    }
}
