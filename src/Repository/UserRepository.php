<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User[]    findByCustom($active = null, $member = null, $begin = null, $end = null, $type = [])
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByCustom($active = null, $member = null, $begin = null, $end = null, $type = []): array
    {
        $qb = $this->createQueryBuilder('u');
        $expr = $qb->expr();

        $exprs = [];
        $argIndex = 0;
        if (!is_null($active)) {
            $argName = '?' . $argIndex;
            $exprs[] = $expr->eq('u.isActive', $argName);
            $qb->setParameter($argIndex, $active);
            $argIndex++;
        }
        if (!is_null($member)) {
            $argName = '?' . $argIndex;
            $exprs[] = $expr->eq('u.isMember', $argName);
            $qb->setParameter($argIndex, $member);
            $argIndex++;
        }
        if (!is_null($begin)) {
            $argName = '?' . $argIndex;
            $exprs[] = $expr->gte('u.lastLoginAt', $argName);
            $qb->setParameter($argIndex, $begin);
            $argIndex++;
        }
        if (!is_null($end)) {
            $argName = '?' . $argIndex;
            $exprs[] = $expr->lte('u.lastLoginAt', $argName);
            $qb->setParameter($argIndex, $end);
            $argIndex++;
        }
        if (!empty($type)) {
            $argName = '?' . $argIndex;
            $exprs[] = $expr->in('u.userType', $argName);
            $qb->setParameter($argIndex, $type);
            $argIndex++;
        }

        if (!empty($exprs)) {
            $qb->where($expr->andX(...$exprs));
        }
        $qb->orderBy('u.id', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
