<?php

namespace App\Controller;

use App\Entity\Inform;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function index(): Response
    {
        return $this->render('app/pages/index.html.twig');
    }

    /**
     * @Route("/legales/mentions-legales", name="app_mentions")
     */
    public function mentions(): Response
    {
        return $this->render('app/pages/legales/mentions.html.twig');
    }

    /**
     * @Route("/legales/politique-confidentialite", options={"expose"=true}, name="app_politique")
     */
    public function politique(): Response
    {
        return $this->render('app/pages/legales/politique.html.twig');
    }

    /**
     * @Route("/legales/cookies", name="app_cookies")
     */
    public function cookies(): Response
    {
        return $this->render('app/pages/legales/cookies.html.twig');
    }

    /**
     * @Route("/legales/rgpd", name="app_rgpd")
     */
    public function rgpd(): Response
    {
        return $this->render('app/pages/legales/rgpd.html.twig');
    }

    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact(): Response
    {
//        return $this->render('app/pages/contact/index.html.twig');
        return $this->redirectToRoute('app_homepage', ['_fragment' => 'restons-en-contact']);
    }

    /**
     * @Route("/nous-contacter", name="app_contact_old")
     */
    public function contactOld(): Response
    {
        return $this->redirectToRoute('app_homepage', ['_fragment' => 'restons-en-contact']);
    }

    /**
     * @Route("/rester-informer", name="app_stay_touch")
     */
    public function inform(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if($request->isMethod("POST")){
            $em = $this->doctrine->getManager();

            $email = $request->get("email");
            $critere = $request->get("critere");

            $existe = $em->getRepository(Inform::class)->findOneBy(['email' => $email]);
            if($critere == "critere" && !$existe){
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
}
