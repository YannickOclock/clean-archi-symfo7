<?php
declare(strict_types=1);

namespace App\Infrastructure\Symfony\Repository;

use App\Domain\TestPost\Entity\Post;
use App\Domain\TestPost\Repository\PostsRepositoryInterface;
use App\Infrastructure\Symfony\Entity\Post as EntityPost;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Class DoctrineORMUserRepository
 * @package RepositoryExample\User
 */
final class DoctrineORMPostRepository extends DoctrineORMRepository implements PostsRepositoryInterface
{
    protected const ENTITY_CLASS = EntityPost::class;
    protected const ALIAS = 'post';

    /**
     * DoctrineORMUserRepository constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, self::ENTITY_CLASS, self::ALIAS);
    }

    public function add(Post $post): void
    {
        // transform Post to EntityPost
        $entityPost = new EntityPost();
        $entityPost->setId(Uuid::fromRfc4122($post->getId()));
        $entityPost->setTitle($post->getTitle());
        $entityPost->setContent($post->getContent());

        $this->manager->persist($entityPost);
        // I usually implement flushing in a Command Bus middleware.
        // But you can flush immediately if you like.
        $this->manager->flush();
    }

    public function remove(Post $post): void
    {
        // transform Post to EntityPost (only id is needed to remove an entity)
        $entityPost = new EntityPost();
        $entityPost->setId(Uuid::fromRfc4122($post->getId()));

        $this->manager->remove($post);
        // I usually implement flushing in a Command Bus middleware.
        // But you can flush immediately if you like.
        $this->manager->flush();
    }

    public function ofId(string $id): ?Post
    {
        $object = $this->manager->find(self::ENTITY_CLASS, $id);
        if ($object instanceof EntityPost) {
            // transform EntityPost to Post
            $post = new Post($object->getId()->toRfc4122(), $object->getTitle(), $object->getContent());
            return $post;
        }
        return null;
    }

    public function all(): iterable 
    {
        $ePosts = $this->query()->getQuery()->getResult();
        $posts = [];
        // transform EntityPost to Post
        foreach ($ePosts as $ePost) {
            $posts[] = new Post($ePost->getId()->toRfc4122(), $ePost->getTitle(), $ePost->getContent());
        }
        return $posts;
    }
}