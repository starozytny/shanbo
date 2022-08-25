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
     * @Route("/article/colombie", name="article_1")
     */
    public function colombie(): Response
    {
        return $this->render('app/pages/blog/articles/colombie.html.twig');
    }

}
