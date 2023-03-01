<?php

namespace App\Security\Voter;

use App\Entity\Conversation;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ConversationVoter extends Voter
{
    
    public const CONVERSATION_VIEW = 'conversation_view';
    public const CONVERSATION_ADD = 'conversation_add';

    protected function supports(string $attribute, $conversation): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CONVERSATION_VIEW, self::CONVERSATION_ADD])
            && $conversation instanceof \App\Entity\Conversation;
    }

    protected function voteOnAttribute(string $attribute, $conversation, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }


        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::CONVERSATION_VIEW:
                return $this->isAllowed($conversation, $user);
                break;
            case self::CONVERSATION_ADD:
                return $this->canSend($conversation, $user);
                break;    
        }

        return false;
    }
    private function isAllowed(Conversation $conversation, User $user){
        return $user === $conversation->getUser1() || $user === $conversation->getUser2();
    }

    private function canSend(Conversation $conversation, User $user){
        if($user === $conversation->getUser1() || $user === $conversation->getUser2()){
            return true;
        } 
        return false;
    }
}
