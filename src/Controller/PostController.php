<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="app_post")
     */
    public function index(): Response
    {
        // todo 4 last posts for homepage
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

    
}
