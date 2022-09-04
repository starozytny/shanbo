<?php

namespace App\Controller\Blog;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/blog", name="blog_")
 */
class BlogController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('app/pages/blog/index.html.twig');
    }

    /**
     * @Route("/article/a-la-rencontre-de-Lyon", name="article_lyon")
     */
    public function lyon(): Response
    {
        return $this->render('app/pages/blog/articles/lyon.html.twig');
    }

    /**
     * @Route("/article/debut-d-une-experience-la-colombie", name="article_colombie")
     */
    public function colombie(): Response
    {
        return $this->render('app/pages/blog/articles/colombie.html.twig');
    }

}
