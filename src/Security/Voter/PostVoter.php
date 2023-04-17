<?php

namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class PostVoter extends Voter
{
    public const POST_EDIT = 'post_edit';
    public const POST_DELETE = 'post_delete';

    protected function supports(string $attribute, $post): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::POST_EDIT, self::POST_DELETE])
            && $post instanceof \App\Entity\Post;
    }

    protected function voteOnAttribute(string $attribute, $post, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::POST_EDIT:
                return $this->isAllowed($post, $user);
                break;
            case self::POST_DELETE:
                return $this->isAllowed($post, $user);
                break;
        }

        return false;
    }

    private function isAllowed(Post $post, User $user): bool
    {
        //if the user is the user who add the post, he can edit and delete this one
        return $user === $post->getUser();
    }
}
