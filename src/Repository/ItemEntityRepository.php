<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ItemEntity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ItemEntity>
 */
class ItemEntityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ItemEntity::class);
    }

    public function findOneByName(string $name): ?ItemEntity
    {
        $item = $this->createQueryBuilder('i')
            ->andWhere('i.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $item instanceof ItemEntity ? $item : null;
    }
}
