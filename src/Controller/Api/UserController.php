<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\PostRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserController extends AbstractController
{
    private $security;
    

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }


    /**
     * @Route("api/mon-profil", name="app_api_user_myProfil", methods={"GET"})
     *
     * @IsGranted("ROLE_USER")
     */
    public function getMyId(): JsonResponse
        {
            $user = $this->security->getUser();
           
           // Return a Json with data and status code
            return $this->json($user, Response::HTTP_OK,[], ["groups" => "users"]); 
        
        }

    /**
     * @Route("/api/mon-profil/{id}/modifier", name="app_api_user_edit", methods={"POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_USER")
     * 
     */
    public function edit(User $user, Request $request, SerializerInterface $serializer, ValidatorInterface $validator, ManagerRegistry $doctrine): Response
    {
        if($user != $this->security->getUser()){
            throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à modifier ce profil');
        }

        // Getting the JSON of our request
        $json = $request->getContent();

        try {
            $user = $serializer->deserialize($json, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
            
        } catch (NotEncodableValueException $e) {
            
            return $this->json(["error" => "JSON non valide"], Response::HTTP_BAD_REQUEST);
        }

        // Validate the user
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorsArray = [];
            foreach ($errors as $error) {
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($errorsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        
        // Update the user
        $entityManager = $doctrine->getManager();
        $user->setUpdatedAt(new \DateTime('now'));
        $entityManager->flush();

         return $this->json(
            $user,
            Response::HTTP_OK,
            [
             // "Location" => $this->generateUrl("app_api_user_myProfil",["id" => $user->getId()])
            ],
            [
                "groups" => "users"
            ]
        );
    }

    /**
     * @Route("/api/mon-profil/{id}/supprimer", name="app_api_user_delete", methods={"POST"}) 
     * 
     * @IsGranted("ROLE_USER")
     */
    public function delete(User $user, EntityManagerInterface $entityManager, PostRepository $postRepository, ReviewRepository $reviewRepository, ConversationRepository $conversationRepository, MessageRepository $messageRepository, UserRepository $userRepository): JsonResponse
    {
        if($user != $this->security->getUser()){
            throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à supprimer ce profil');
        }

        $userAnonyme = $userRepository->findOneBy(["email"=>"anonyme@anonyme.com"]); // utlisateur anonyme
       
        // Anonymization of posts posted by the user we want to delete
        $posts = $postRepository->findBy(["user"=>$user]);
        if($posts){    
        foreach($posts as $post){
            $post->setUser($userAnonyme);
        }
        }
        
        // Anonymization of reviews posted by the user we want to delete
        $reviewsGiven = $reviewRepository->findBy(["userGiver"=>$user]);
        if($reviewsGiven){
            
        foreach($reviewsGiven as $review){
            $review->setUserGiver($userAnonyme);
        }
        }

        // deleting reviews received by the user we want to delete
        $reviewsTaken = $reviewRepository->findBy(["userTaker"=>$user]);
        if($reviewsTaken){
        foreach($reviewsTaken as $review){
            $reviewRepository->remove($review);
        }
        }

        // Anonymization of messages sent by the user we want to delete
        $messagesSent = $messageRepository->findBy(["userSender"=>$user]);
        if($messagesSent){
            
        foreach($messagesSent as $message){
            $message->setUserSender($userAnonyme);
        }
        }

        // Anonymization of messages received by the user we want to delete
        $messagesReceived = $messageRepository->findBy(["userRecipient"=>$user]);
        if($messagesReceived){
            
        foreach($messagesReceived as $message){
            $message->setUserRecipient($userAnonyme);
        }
        }
        
        // Anonymization of one the user we want te delete participating in a conversation
        $conversations = $conversationRepository->findBy(["user1"=>$user]);
        if($conversations){
            
        foreach($conversations as $conversation){
            $conversation->setUser1($userAnonyme);
        }
        }

        // Anonymization of one the user we want te delete participating in a conversation
        $conversations = $conversationRepository->findBy(["user2"=>$user]);
        if($conversations){
            
        foreach($conversations as $conversation){
            $conversation->setUser2($userAnonyme);
        }
        }
        

;       $entityManager->remove($user);
        $entityManager->flush();
        // Return a JSON response indicating success
        return new JsonResponse([
            'success' => true,
            'message' => 'Votre profil à été supprimé avec succès',
        ]);
        
    }

      /**
     * @Route("api/profil/{id}", name="app_api_user_getUserById", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * Present one user
     */
    public function getUserById(User $user, ReviewRepository $reviewRepository): JsonResponse
    {
       
        // Return a Json with data and status code
         return $this->json($user,Response::HTTP_OK,[],["groups" => "users"]);   
    }
    
}
