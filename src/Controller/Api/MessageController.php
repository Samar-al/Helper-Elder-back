<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


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
     * @Route("/api/mon-profil/{id}/conversation/{id_conversation}", name="app_api_message", methods={"GET"}, requirements={"id"="\d+"})
     * 
     */
    public function index(MessageRepository $messageRepository, ConversationRepository $conversationRepository, User $user,  int $id_conversation): JsonResponse
    {
        
        if($user != $this->security->getUser()){
                
            throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à accéder à ce profil');
        }

        $conversation = $conversationRepository->find($id_conversation);
        

        if (!$conversation) {
            throw $this->createNotFoundException('Conversation not found');
        }
        
        $messages = $messageRepository->findBy(["conversation"=>$conversation], ["createdAt"=>"DESC"]);
        
       // Return a Json with data and status code
        return $this->json($messages, Response::HTTP_OK,[], ["groups"=>"messages"]); 
    }


    /**
     * @Route("/api/message/envoyer", name="app_api_message_send", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function send(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, MessageRepository $messageRepository, ConversationRepository $conversationRepository): JsonResponse
    {

        
        // getting the json of notre request
        $json = $request->getContent();
        
        
        try{
            
            $message = $serializer->deserialize($json, Message::class, 'json');
            
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
        
        $user1=$this->security->getUser();
        $user2=$message->getUserRecipient();
        
        $conversation1 = $conversationRepository->findBy(['user1' => $user1, 'user2' => $user2]);
        $conversation2 = $conversationRepository->findBy(['user1' => $user2, 'user2' => $user1]);

        $conversation = array_merge($conversation1, $conversation2);

        if (!$conversation) {
            // if the conversation dosn't exist
            $conversation = new Conversation();
            $conversation->setTitle("Hello");
            $conversation->setUser1($user1);
            $conversation->setUser2($user2);
            $conversation->setCreatedAt(new \Datetime);
            $conversationRepository->add($conversation, true);
            
        }
        $conversation->setUpdatedAt(new \DateTime());
        $message->setConversation($conversation);
        $messageRepository->add($message, true);

        
        

        // Renvoi un json avec en premier argument les données et en deuxième un status code
        return $this->json(
            $message,
            Response::HTTP_CREATED,
            [
               // "Location" => $this->generateUrl("app_api_conversation_getOneById", ["id" => $conversation->getId()])
            ],
            [
                "groups" => "messages"
            ]
        );

        
    }

    

}
