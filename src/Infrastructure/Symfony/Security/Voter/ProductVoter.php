<?php

namespace App\Infrastructure\Symfony\Security\Voter;

use App\Infrastructure\Symfony\Entity\Product;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])) {
            return false;
        }
        if(!$subject instanceof Product) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
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
                break;
            case self::DELETE:
                return $this->canDelete();
                break;
        }
    }

    private function canEdit(): bool
    {
        // on pourrait faire un role ROLE_PRODUCT_EDIT_ADMIN par exemple
        return $this->security->isGranted('ROLE_PRODUCT_ADMIN');
    }
    private function canDelete(): bool
    {
        // on pourrait faire un role ROLE_PRODUCT_DELETE_ADMIN par exemple
        return $this->security->isGranted('ROLE_ADMIN');
    }
}