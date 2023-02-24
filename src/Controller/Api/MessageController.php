<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;


class MessageController extends AbstractController
{
    private $security;
    

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }
    /**
     * @Route("/api/conversation/{id_conversation}", name="app_api_message", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function index(User $user, UserRepository $userRepository): JsonResponse
    {
        if($user != $this->security->getUser()){
                
            throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à accéder à ce profil');
        }

        $user = $userRepository->find($this->security->getUser());

       // Return a Json with data and status code
        return $this->json($user, Response::HTTP_OK,[], ["groups" => "users"]); 
    }

    /**
     * @Route("/api/message/envoyer", name="app_api_message_send", methods={"POST"})
     */
    public function send(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, MessageRepository $messageRepository, ConversationRepository $conversationRepository): Response
    {

        
        // getting the json of notre request
        $json = $request->getContent();
        
        
        try{
            
            $message = $serializer->deserialize($json, Post::class, 'json');
            
        }catch(NotEncodableValueException $e){
            
            return $this->json(["error" => "Json non valide"],Response::HTTP_BAD_REQUEST);
        }  
       

        // Validator will check that my inputs are well filled
        // if incomplete, i will get a sql error when adding
        $errors = $validator->validate($message);
        
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


        // TODO add the message in database

        //check if there's existing conversation or create a new one
        $user=$this->security->getUser();
        
        //! user1 ou user2 ou bien user1 et user2
        $conversation = $conversationRepository->findOneBy(['user1' => $user, 'user2' => $user]);

        if (!$conversation) {
            // if the conversation dosn't exist
            $conversation = new Conversation();
            $conversation->setUser1($user);
            $conversation->setUser2($user2); //! Comment récupérer les donné du post
            $conversationRepository->add($conversation, true);
            
        }
        
        $message->setConversation($conversation);
        $message->setUserSender($this->security->getUser());
        $message->setUserRecipient($conversation->getUser2()); //à preremplir dans le formulaire?
        $messageRepository->add($message, true);

        
        

        // Renvoi un json avec en premier argument les données et en deuxième un status code
        return $this->json(
            $message,
            Response::HTTP_CREATED,
            [
                "Location" => $this->generateUrl("app_api_post_getOneById", ["id" => $conversation->getId()])
            ],
            [
                "groups" => "posts"
            ]
        );
    }
}
