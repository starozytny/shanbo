<?php

namespace App\Controller;

use App\Entity\Inform;
use App\Repository\PhotoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    #[Route(path: '/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('app/pages/index.html.twig');
    }

    #[Route(path: '/legales/mentions-legales', name: 'app_mentions')]
    public function mentions(): Response
    {
        return $this->render('app/pages/legales/mentions.html.twig');
    }

    #[Route(path: '/legales/politique-confidentiality', name: 'app_politique', options: ['expose' => true])]
    public function politique(): Response
    {
        return $this->render('app/pages/legales/politique.html.twig');
    }

    #[Route(path: '/legales/cookies', name: 'app_cookies')]
    public function cookies(): Response
    {
        return $this->render('app/pages/legales/cookies.html.twig');
    }

    #[Route(path: '/legales/rgpd', name: 'app_rgpd')]
    public function rgpd(): Response
    {
        return $this->render('app/pages/legales/rgpd.html.twig');
    }

    #[Route(path: '/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->redirectToRoute('app_homepage', ['_fragment' => 'restons-en-contact']);
    }

    #[Route(path: '/nous-contacter', name: 'app_contact_old')]
    public function contactOld(): Response
    {
        return $this->redirectToRoute('app_homepage', ['_fragment' => 'restons-en-contact']);
    }

    #[Route(path: '/rester-informer', name: 'app_stay_touch')]
    public function inform(Request $request): RedirectResponse
    {
        if($request->isMethod("POST")){
            $em = $this->doctrine->getManager();

            $email = $request->get("email");
            $critere = $request->get("critere");

            $existe = $em->getRepository(Inform::class)->findOneBy(['email' => $email]);
            if($critere == "" && !$existe){
                $obj = (new Inform())
                    ->setEmail($email)
                ;

                $em->persist($obj);
                $em->flush();

                $this->addFlash("success", "Ta demande a été prise en compte ! A bientôt !");
            }
        }

        return $this->redirectToRoute('app_homepage');
    }

    #[Route(path: '/photos', name: 'app_photos')]
    public function photos(PhotoRepository $repository): Response
    {
        return $this->render('app/pages/photos/index.html.twig', [
            'data' => $repository->findBy([], ['id' => 'DESC'])
        ]);
    }
}
