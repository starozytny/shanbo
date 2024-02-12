<?php

namespace App\Controller;

use App\Entity\Changelog;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/espace-membre', name: 'user_')]
class UserController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    #[Route(path: '/', name: 'homepage', options: ['expose' => true])]
    public function index(): Response
    {
        $em = $this->doctrine->getManager();

        $changelogs = $em->getRepository(Changelog::class)->findBy(['isPublished' => true], ['createdAt' => 'DESC'], 5);

        return $this->render('user/pages/index.html.twig', [
            'changelogs' => $changelogs
        ]);
    }

    #[Route(path: '/profil', name: 'profil', options: ['expose' => true])]
    public function profil(): Response
    {
        /** @var User $obj */
        $obj = $this->getUser();

        return $this->render('user/pages/profil/index.html.twig',  [
            'obj' => $obj
        ]);
    }

    #[Route(path: '/modifier-profil', name: 'profil_update')]
    public function profilUpdate(SerializerInterface $serializer): Response
    {
        /** @var User $data */
        $data = $this->getUser();
        $data = $serializer->serialize($data, 'json', ['groups' => User::ADMIN_READ]);
        return $this->render('user/pages/profil/update.html.twig',  ['donnees' => $data]);
    }
}
