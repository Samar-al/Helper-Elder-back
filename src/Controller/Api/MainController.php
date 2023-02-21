<?php

namespace App\Controller\Api;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MainController extends AbstractController
{
    /**
     * @Route("/api", name="app_api_main_home", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['createdAt'=>'DESC'], 4);
        //dd($posts);
        // return the information under a json format
        return $this->json($posts,Response::HTTP_OK,[],["groups" => "posts"]);
    }
}
