<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const USER_EDIT = 'user_edit';
    public const USER_VIEW = 'user_view';
    public const USER_DELETE = 'user_delete';


    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::USER_EDIT, self::USER_VIEW, self::USER_DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        if($user === $subject){
            return true;
        }
        
        // ... (check conditions and return true to grant permission) ...
      /*   switch ($attribute) {
            case self::USER_EDIT:
                
                break;
            case self::USER_VIEW:
               
                break;
            case self::USER_DELETE:
                
                break;
        }
      */
        return false;
    }

    
    
}
