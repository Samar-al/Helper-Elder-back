<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ConversationController extends AbstractController
{
    
    
    /**
     * @Route("api/mon-profil/conversation", name="app_api_conversation_list", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(ConversationRepository $conversationRepository): JsonResponse
    {
    
        $conversations = $conversationRepository->findConversationByUserId($this->getUser());
        // Return a Json with data and status code
        return $this->json($conversations, Response::HTTP_OK,[], ["groups" => "users"]); 
    
    }

}
