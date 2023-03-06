<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\PostRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/back-office")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/utilisateur", name="app_back_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('back/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/utilisateur/ajouter", name="app_back_user_add", methods={"GET", "POST"})
     */
    public function add(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
            $user->setCreatedAt(new \DateTime('now'));
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/utilisateur/{id}/", name="app_back_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('back/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/utilisateur/{id}/edit", name="app_back_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
            $user->setUpdatedAt(new \DateTime());
            $userRepository->add($user, true);

            return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/utilisateur/{id}/supprimer", name="app_back_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository, PostRepository $postRepository, ReviewRepository $reviewRepository, ConversationRepository $conversationRepository, MessageRepository $messageRepository): Response
    {
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
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        return $this->redirectToRoute('app_back_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
