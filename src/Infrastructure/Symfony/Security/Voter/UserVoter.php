<?php

namespace App\Infrastructure\Symfony\Security\Voter;

use App\Infrastructure\Symfony\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const DELETE = 'USER_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $user): bool
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        if(!$user instanceof User) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $use, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if(!$user instanceof UserInterface) {
            return false;
        }
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        // l'utilisateur est connecté et n'est pas ADMIN, vérification des permissions
        switch($attribute) {
            case self::EDIT:
                return $this->canEdit();
            case self::DELETE:
                return $this->canDelete();
            default:
                return false;
        }
    }

    private function canEdit()
    {
        // on pourrait faire un role ROLE_PRODUCT_EDIT_ADMIN par exemple
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
    private function canDelete()
    {
        // on pourrait faire un role ROLE_PRODUCT_DELETE_ADMIN par exemple
        return $this->security->isGranted('ROLE_ADMIN');
    }
}