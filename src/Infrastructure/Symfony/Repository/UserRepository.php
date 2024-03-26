<?php

namespace App\Infrastructure\Symfony\Repository;

use App\Domain\User\Entity\User as DomainUser;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Symfony\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        ManagerRegistry $registry,
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }


    public function add(DomainUser $domainUser): void
    {
        $user = new User();
        $user->setId(Uuid::fromRfc4122($domainUser->getId()));
        $user->setEmail($domainUser->getEmail());
        $user->setFirstName($domainUser->getFirstName());
        $user->setLastName($domainUser->getLastName());

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $domainUser->getPassword()
        );
        $user->setPassword($hashedPassword);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function remove(DomainUser $domainUser, bool $flush = false): void
    {
        $user = $this->findOneBy(['email' => $domainUser->getEmail()]);
        if (!$user) {
            return;
        }

        $this->getEntityManager()->remove($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findOneByEmail(string $email): ?DomainUser
    {
        $user = $this->findOneBy(['email' => $email]);

        if (!$user) {
            return null;
        }

        return new DomainUser(
            $user->getId()->toRfc4122(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getFirstName(),
            $user->getLastName()
        );
    }
}
