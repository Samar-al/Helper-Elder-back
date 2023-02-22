<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Entity\Conversation;
use App\Entity\User;

use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\Id;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ConversationController extends AbstractController
{
    /**
     * @Route("/api/profil/{id}/conversation", name="app_api_conversations", methods={"GET"})
     * 
     */
    public function index(UserInterface $user, ConversationRepository $conversationRepository, UserRepository $userRepository): Response
    {
        // Get all the conversations by user id
        // 
    }

    /**
     * @Route("/api/profil/{id_user}/conversation/{id_conversation}", name="app_api_conversation_messages", methods={"GET"})
     */
    public function findById(MessageRepository $messageRepository, ConversationRepository $conversationRepository): Response
    {
        $messages = $messageRepository->find();
        dd($messages);
        return $this->json($messages, Response::HTTP_OK, []);
    }

    /**
     * @Route("/api/profil/{id_user}/conversation/{id_conversation}/supprimer", name="app_api_conversation_delete", methods={"POST"})
     */
    public function delete(MessageRepository $messageRepository, ConversationRepository $conversationRepository): Response
    {
        return $this->render('conversation/index.html.twig', [
            'controller_name' => 'ConversationController',
        ]);
    }
}
