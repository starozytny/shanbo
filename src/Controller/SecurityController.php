<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Expiration;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    #[Route(path: '/login', name: 'app_login', options: ['expose' => true])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             if($this->isGranted('ROLE_ADMIN')) return $this->redirectToRoute('admin_homepage');
             if($this->isGranted('ROLE_USER')) return $this->redirectToRoute('user_homepage');
         }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('app/pages/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/connected', name: 'app_logged')]
    public function logged(): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $em = $this->doctrine->getManager();

        if ($user) {
            $user->setLastLogin(new \DateTime());
            $em->flush();

            if($this->isGranted('ROLE_ADMIN')) return $this->redirectToRoute('admin_homepage');
            if($this->isGranted('ROLE_USER')) return $this->redirectToRoute('user_homepage');
        }

        return $this->redirectToRoute('app_login');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): never
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/reinitialisation/mot-de-passe/{token}-{code}', name: 'app_password_reinit')]
    public function reinit($token, $code, Expiration $expiration): Response
    {
        $em = $this->doctrine->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['token' => $token]);
        if(!$user){
            throw new NotFoundHttpException("Cet utilisateur n'existe pas.");
        }

        if((!$user->getForgetAt() || !$user->getForgetCode())
            || ($user->getForgetCode() && $user->getForgetCode() != $code)){
            return $this->render('app/pages/security/reinit.html.twig', ['error' => true]);
        }

        if($user->getForgetAt()){
            if ($expiration->isExpiredByMinutes($user->getForgetAt(), new \DateTime(), 30)) {
                return $this->render('app/pages/security/reinit.html.twig', ['error' => true]);
            }
        }

        return $this->render('app/pages/security/reinit.html.twig', ['token' => $token]);
    }
}
