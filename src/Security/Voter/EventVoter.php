<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Event;
use Symfony\Component\Security\Core\Security;

class EventVoter extends Voter
{

    /** @var Security */
    private $security;

    /** @var string[] */
    private $attributeMethods = [
        'create' => 'canCreateEvent',
        'edit' => 'canEditEvent',
        'delete' => 'canDeleteEvent'
    ];

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return array_key_exists($attribute, $this->attributeMethods) && $subject instanceof Event;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        if ($this->supports($attribute, $subject)) {
            return call_user_func([$this, $this->attributeMethods[$attribute]],$user, $subject);
        }

        return false;
    }

    private function canCreateEvent(User $user, Event $subject) {
        return $this->security->isGranted('ROLE_USER');
    }

    private function canEditEvent(User $user, Event $subject) {
        return $subject->isOwner($user);
    }

    private function canDeleteEvent(User $user, Event $subject) {
        return $this->canEditEvent($user, $subject);
    }
}
