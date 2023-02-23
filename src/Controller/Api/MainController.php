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
       // $posts = $postRepository->findBy([], ['createdAt'=>'DESC'], 4);
        $postsHelper = $postRepository->findTheLastFourPostType(2);
        $postsElder = $postRepository->findTheLastFourPostType(1);

        //dd($posts);
       /*  if (empty($posts)) {
            return $this->json(["error" => "Il n'y a pas de post créé par les utilisateurs pour le moment"], Response::HTTP_NOT_FOUND);
        } */
        if (empty($postsHelper)) {
            return $this->json(["error" => "Il n'y a pas de post créé par les utilisateurs Elders pour le moment"], Response::HTTP_NOT_FOUND);
        }
        if (empty($postsElder)) {
            return $this->json(["error" => "Il n'y a pas de post créé par les utilisateurs Helpers pour le moment"], Response::HTTP_NOT_FOUND);
        }
        // return the information under a json format
        return $this->json([$postsHelper, $postsElder],Response::HTTP_OK,[],["groups" => "posts"]);
    }
}