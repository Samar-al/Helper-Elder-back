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
    private $security;
    

    public function __construct(Security $security)
    {
        // Avoid calling getUser() in the constructor: auth may not
        // be complete yet. Instead, store the entire Security object.
        $this->security = $security;
    }
    /**
     * @Route("api/mon-profil/{id}/conversation", name="app_api_conversation_list", methods={"GET"}, requirements={"id"="\d+"} )
     *
     * Edit one user in the front-office
     * @IsGranted("ROLE_USER")
     */
    public function index(User $user, ConversationRepository $conversationRepository): JsonResponse
    {
        
        if($user != $this->security->getUser()){
            
            throw $this->createAccessDeniedException('Access denied: Vous n\'êtes pas autorisé à accéder à ce profil');
        }

        $user=$this->security->getUser();
        $conversations = $conversationRepository->findConversationByUserId($this->security->getUser()->getId());
        // Return a Json with data and status code
        return $this->json($conversations, Response::HTTP_OK,[], ["groups" => "users"]); 
    
    }

}
