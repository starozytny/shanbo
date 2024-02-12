<?php

namespace App\Controller;

use App\Entity\Changelog;
use App\Entity\Contact;
use App\Entity\Inform;
use App\Entity\Notification;
use App\Entity\Settings;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    private function getAllData($classe, SerializerInterface $serializer, $groups = User::ADMIN_READ): string
    {
        $em = $this->doctrine->getManager();
        $objs = $em->getRepository($classe)->findAll();

        return $serializer->serialize($objs, 'json', ['groups' => $groups]);
    }

    private function getRenderView(Request $request, SerializerInterface $serializer, $class, $route): Response
    {
        $objs = $this->getAllData($class, $serializer);
        $search = $request->query->get('search');
        if($search){
            return $this->render($route, [
                'donnees' => $objs,
                'search' => $search
            ]);
        }

        return $this->render($route, [
            'donnees' => $objs
        ]);
    }

    #[Route(path: '/', options: ['expose' => true], name: 'homepage')]
    public function index(): Response
    {
        $em = $this->doctrine->getManager();
        $users = $em->getRepository(User::class)->findAll();
        $settings = $em->getRepository(Settings::class)->findAll();

        $totalUsers = count($users); $nbConnected = 0;
        foreach($users as $user){
            if($user->getLastLogin()){
                $nbConnected++;
            }
        }
        return $this->render('admin/pages/index.html.twig', [
            'settings' => $settings ? $settings[0] : null,
            'totalUsers' => $totalUsers,
            'nbConnected' => $nbConnected,
        ]);
    }

    #[Route(path: '/styleguide/html', name: 'styleguide_html')]
    public function styleguideHtml(): Response
    {
        return $this->render('admin/pages/styleguide/index.html.twig');
    }

    #[Route(path: '/styleguide/react', options: ['expose' => true], name: 'styleguide_react')]
    public function styleguideReact(Request  $request): Response
    {
        if($request->isMethod("POST")){
            return new JsonResponse(['code' => true]);
        }
        return $this->render('admin/pages/styleguide/react.html.twig');
    }

    #[Route(path: '/utilisateurs', name: 'users_index')]
    public function users(Request $request, SerializerInterface $serializer): Response
    {
        return $this->getRenderView($request, $serializer, User::class, 'admin/pages/user/index.html.twig');
    }

    #[Route(path: '/parametres', name: 'settings_index')]
    public function settings(): Response
    {
        return $this->render('admin/pages/settings/index.html.twig');
    }

    #[Route(path: '/contact', name: 'contact_index')]
    public function contact(Request $request, SerializerInterface $serializer): Response
    {
        return $this->getRenderView($request, $serializer, Contact::class, 'admin/pages/contact/index.html.twig');
    }

    #[Route(path: '/notifications', options: ['expose' => true], name: 'notifications_index')]
    public function notifications(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(Notification::class, $serializer);

        return $this->render('admin/pages/notifications/index.html.twig', [
            'donnees' => $objs
        ]);
    }

    #[Route(path: '/changelogs', options: ['expose' => true], name: 'changelogs_index')]
    public function changelogs(SerializerInterface $serializer): Response
    {
        $objs = $this->getAllData(Changelog::class, $serializer, User::USER_READ);

        return $this->render('admin/pages/changelog/index.html.twig', [
            'donnees' => $objs
        ]);
    }

    #[Route(path: '/rester-informe', name: 'stay_touch_index')]
    public function inform(Request $request, SerializerInterface $serializer): Response
    {
        return $this->getRenderView($request, $serializer, Inform::class, 'admin/pages/inform/index.html.twig');
    }
}
