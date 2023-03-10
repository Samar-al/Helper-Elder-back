<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Secur;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

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
    public function getHelpersPosts(UserRepository $userRepository): JsonResponse
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
    public function getEldersPosts(UserRepository $userRepository): JsonResponse
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
        
        //  iterate on the errors array
        
        if(count($errors) > 0){
            //  create a array of errors
            $errorsArray = [];
            foreach($errors as $error){
                // A l'index qui correspond au champs mal remplis, j'y injecte le/les messages d'erreurs
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($errorsArray,Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        // add the post in database
        
        $post->setSlug($post->getSlug());
        $post->setUser($this->security->getUser());
        $postRepository->add($post,true);
        

        // Renvoi un json avec en premier argument les données et en deuxième un status code
        return $this->json(
            $post,
            Response::HTTP_CREATED,
            [
              //  "Location" => $this->generateUrl("app_api_post_getOneById", ["id" => $post->getId()])
            ],
            [
                "groups" => "posts"
            ]
        );
    } 

    /**
     * @Route("/api/annonce/{id}/modifier", name="app_api_post_edit", methods={"POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine, Post $post): JsonResponse
    {
        
        $this->denyAccessUnlessGranted('post_edit', $post);
       

        // Getting the JSON of our request
        $json = $request->getContent();
        
        try {
           $post = $serializer->deserialize($json, Post::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $post]);
         
        } catch (Exception $e) {
            //dd($e->getMessage());
            return $this->json(["error" => "JSON non valide:".$e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        
        // Validate the post
        $errors = $validator->validate($post);
        if (count($errors) > 0) {
            $errorsArray = [];
            foreach ($errors as $error) {
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($errorsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Update the post
        $entityManager = $doctrine->getManager();
        $post->setSlug($post->getSlug());
        $post->setUpdatedAt(new \DateTime('now'));
        $post->setUser($this->security->getUser());
        
        $entityManager->flush();

        return $this->json(
            $post,
            Response::HTTP_OK,
            [
               // "Location" => $this->generateUrl("app_api_post_getOneById", ["id" => $post->getId()])
            ],
            [
                "groups" => "posts"
            ]
        );
    }

    /**
     * @Route("/api/annonce/{id}/supprimer", name="app_api_post_delete", methods={"POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     * 
     */
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        
        $this->denyAccessUnlessGranted('post_delete', $post);
       
        $entityManager->remove($post);
        $entityManager->flush();
        
        return new JsonResponse(['message' => 'Annonce supprimée avec succès']);
    }


}

