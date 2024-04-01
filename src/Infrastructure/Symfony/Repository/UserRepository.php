<?php

namespace App\Infrastructure\Symfony\Repository;

use App\Domain\User\Entity\User as DomainUser;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Symfony\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
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

    // For Admin Pages
    private function paginate(ORMQueryBuilder $qb, int $page, int $limit): array {
        $result = [];
        $paginator = new Paginator($qb);
        $data = $paginator->getQuery()->getResult();
        if(empty($data)) {
            return $result;
        }

        $total = $paginator->count();

        // on calcule le nombre de résultats
        $pages = ceil($total / $limit);
        $result = [
            'data' => $data,
            'total' => $paginator->count(),
            'pages' => $pages,
            'page' => $page,
            'limit' => $limit
        ];

        return $result;
    }
    public function findUsersPaginated(int $page, int $limit): array
    {
        $limit = abs($limit);
        $query = $this->createQueryBuilder('u')
            ->setMaxResults($limit)
            ->setFirstResult($page * $limit - $limit)
            ->orderBy('u.firstName', 'ASC')
        ;
        $results = $this->paginate($query, $page, $limit);
        return $results;
    }



    // From Domain

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
