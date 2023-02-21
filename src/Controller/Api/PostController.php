<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/api/annonce/aidant", name="app_api_post_helper", methods={"GET"})
     */
    public function getHelpersPosts(PostRepository $postRepository, UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy(['type'=>2]);
        if(!$users){
            return $this->json(["error" => "Il n'y à pas d'utilisateur Helpers pour le moment"],Response::HTTP_NOT_FOUND);
        }
        $posts = [];
        foreach($users as $user){

            $userPosts = $user->getPosts();
            foreach ($userPosts as $post) {
                $posts[]= $post;
            }
              
        }
           
        return $this->json($posts,Response::HTTP_OK,[],["groups" => "posts"]); 
        
    }


     /**
     * @Route("/api/annonce/recherche-aide", name="app_api_post_elder", methods={"GET"})
     */
    public function getEldersPosts(PostRepository $postRepository, UserRepository $userRepository): Response
    {

        $users = $userRepository->findBy(['type'=>1]);
        if(!$users){
            return $this->json(["error" => "Il n'y à pas d'utilisateur Elders pour le moment"],Response::HTTP_NOT_FOUND);
        }
        $posts = [];
        foreach($users as $user){

            $userPosts = $user->getPosts();
            foreach ($userPosts as $post) {
                $posts[]= $post;
            }
              
        }
           
        return $this->json($posts,Response::HTTP_OK,[],["groups" => "posts"]); 
    }

    /**
     * @Route("/api/annonce/{id}", name="app_api_post_getOneById", methods={"GET"}, requirements={"id"="\d+"})
     * @Route("/api/annonce/{slug}", name="app_api_post_getOneBySlug", methods={"GET"})
     */
    public function getOne(Post $post): Response
    {

        // Returns a Json with first argument data and 2nd argument the status code
        return $this->json($post,Response::HTTP_OK,[],["groups" => "posts"]);
    }

    
}

