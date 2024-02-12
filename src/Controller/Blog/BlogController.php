<?php

namespace App\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/histoires-de-burrito', name: 'blog_')]
class BlogController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->render('app/pages/blog/index.html.twig');
    }

    #[Route(path: '/a-la-rencontre-de-Lyon', name: 'article_lyon')]
    public function lyon(): Response
    {
        return $this->render('app/pages/blog/articles/lyon.html.twig');
    }

    #[Route(path: '/debut-d-une-experience-la-colombie', name: 'article_colombie')]
    public function colombie(): Response
    {
        return $this->render('app/pages/blog/articles/colombie.html.twig');
    }

}
