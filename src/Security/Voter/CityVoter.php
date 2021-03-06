<?php

namespace App\Security\Voter;

use App\Entity\City;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CityVoter extends Voter
{

    /** @var Security */
    private $security;

    /** @var string[] */
    private $attributeMethods = [
        'create' => 'canCreateCity',
        'edit' => 'canEditCity',
        'delete' => 'canDeleteCity'
    ];

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        $isCityArray = false;
        if (is_array($subject)) {
            $isCityArray = true;
            foreach ($subject as $city) {
                if (!$city instanceof City) {
                    $isCityArray = false;
                    break;
                }
            }
        }

        return array_key_exists($attribute, $this->attributeMethods) && ($subject instanceof City || $isCityArray);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        if ($this->supports($attribute, $subject)) {
            return call_user_func([$this, $this->attributeMethods[$attribute]]);
        }

        return false;
    }

    private function canCreateCity() {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canEditCity() {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canDeleteCity() {
        return $this->security->isGranted('ROLE_SUPER_ADMIN');
    }
}
