<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class PostController extends AbstractController
{
    private $security;
    

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }
   
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


     /**
     * @Route("/api/annonce/ajouter", name="app_api_post_add", methods={"POST"})
     * 
     */
    public function add(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, PostRepository $postRepository): Response
    {

        
        
        // getting the json of notre request
        $json = $request->getContent();
        
        
        try{
            
            $post = $serializer->deserialize($json, Post::class, 'json');
            
        }catch(NotEncodableValueException $e){
            
            return $this->json(["error" => "Json non valide"],Response::HTTP_BAD_REQUEST);
        }  
       

        // Validator will check that my inputs are well filled
        // if incomplete, i will get a sql error when adding
        $errors = $validator->validate($post);
        
        // i iterate on the errors array
        
        if(count($errors) > 0){
            // i create a array of errors
            $errorsArray = [];
            foreach($errors as $error){
                // A l'index qui correspond au champs mal remplis, j'y injecte le/les messages d'erreurs
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($errorsArray,Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        // TODO add the post in database
        
        $post->setSlug($post->getSlug());
        $post->setUser($this->security->getUser());
        $postRepository->add($post,true);
        

        // Renvoi un json avec en premier argument les données et en deuxième un status code
        return $this->json(
            $post,
            Response::HTTP_CREATED,
            [
                "Location" => $this->generateUrl("app_api_post_getOneById", ["id" => $post->getId()])
            ],
            [
                "groups" => "posts"
            ]
        );
    } 


}

