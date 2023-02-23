<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ConversationController extends AbstractController
{
    private $security;
    

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }
    /**
     * @Route("/api/conversation", name="app_api_conversation")
     */
    public function index(User $user, ConversationRepository $conversationRepository): JsonResponse
    {
        if($user != $this->security->getUser()){
                
            throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à accéder à ces conversation');
        }
        $conversation1=$conversationRepository->findBy(["user1"=>$this->security->getUser()], ["created_at"=>"DESC"]);
        $conversation2=$conversationRepository->findBy(["user2"=>$this->security->getUser()], ["created_at"=>"DESC"]);
        

       // Return a Json with data and status code
        return $this->json([$conversation1, $conversation2], Response::HTTP_OK,[], ["groups" => "conversations"]); 
    }
}
