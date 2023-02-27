<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
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
    public function send(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, MessageRepository $messageRepository, ConversationRepository $conversationRepository, UserRepository $userRepository): JsonResponse
    {
    
     /* // Renvoi un json avec en premier argument les données et en deuxième un status code
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
 */
        // Extract the data from the JSON request
        $data = json_decode($request->getContent(), true);
        $title=$data['title'];
        $content = $data['content'];
        $user1 = $data['userSender'];
        $user2n = $data['userRecipient'];

        $errors = $validator->validate($data);

        if(count($errors) > 0){
            //  create a array of errors
            $errorsArray = [];
            foreach($errors as $error){
                // A l'index qui correspond au champs mal remplis, j'y injecte le/les messages d'erreurs
                $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($errorsArray,Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check if a conversation already exists between the two users
        $conversation =$conversationRepository->findTheConversation($user1, $user2n);
            
        // If a conversation doesn't exist, create a new one
        $user2 = $userRepository->find($user2n);

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setUser1($this->security->getUser());
            $conversation->setUser2($user2);
            $conversation->setTitle($title);
            $conversation->setCreatedAt(new \DateTime());
            $conversationRepository->add($conversation, true);
            
        }

        // Create a new message and add it to the conversation
        $conversation->setUpdatedAt(new \DateTime());
        $message = new Message();
        $message->setContent($content);
        $message->setConversation($conversation);
        $message->setUserSender($this->security->getUser());
        $message->setUserRecipient($user2);
        $message->setCreatedAt(new \DateTime());
        $messageRepository->add($message, true);


        // Return a JSON response indicating success
        return new JsonResponse([
            'success' => true,
            'message' => 'Message created successfully',
        ]);
}
}
