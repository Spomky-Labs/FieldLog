<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Entry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entry>
 */
final class EntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entry::class);
    }

    public function save(Entry $entity): void
    {
        $this->getEntityManager()
            ->persist($entity);
        $this->getEntityManager()
            ->flush();
    }

    public function remove(Entry $entity): void
    {
        $this->getEntityManager()
            ->remove($entity);
        $this->getEntityManager()
            ->flush();
    }

    /**
     * @return Paginator<Entry>
     */
    public function getPaginatedEntries(int $page, int $limit): Paginator
    {
        $query = $this->createQueryBuilder('e')
            ->orderBy('e.publishedAt', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        return new Paginator($query);
    }
}
