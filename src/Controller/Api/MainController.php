<?php

namespace App\Controller\Api;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
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
        if (empty($posts)) {
            return $this->json(["error" => "Il n'y a pas de post créé par les utilisateurs pour le moment"], Response::HTTP_NOT_FOUND);
        }
        // return the information under a json format
        return $this->json($posts,Response::HTTP_OK,[],["groups" => "posts"]);
    }

    /**
     * @Route("/api/home-aidant", name="app_api_main_home_helper", methods={"GET"})
     */
    public function indexHelper(PostRepository $postRepository, UserRepository $userRepository): Response
    {
        
        $posts = $postRepository->findTheLastFourPostType(2);
        //dd($posts);
        // return the information under a json format
        if (empty($posts)) {
            return $this->json(["error" => "Il n'y a pas de post créé par les utilisateurs Helpers pour le moment"], Response::HTTP_NOT_FOUND);
        }
        return $this->json($posts,Response::HTTP_OK,[],["groups" => "posts"]);
        
         
    }

     /**
     * @Route("/api/home-cherchant", name="app_api_main_home_elder", methods={"GET"})
     */
    public function indexElder(PostRepository $postRepository, UserRepository $userRepository): Response
    {
        
        $posts = $postRepository->findTheLastFourPostType(1);
        if (empty($posts)) {
            return $this->json(["error" => "Il n'y a pas de post créé par les utilisateurs Elders pour le moment"], Response::HTTP_NOT_FOUND);
        }
        return $this->json($posts,Response::HTTP_OK,[],["groups" => "posts"]);
        
         
    }
}
