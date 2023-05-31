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

        $exprs = [];
        if ($active !== null) {
            $exprs[] = $qb->expr()->eq('u.isActive', $active);
        }
        if ($member !== null) {
            $exprs[] = $qb->expr()->eq('u.isMember', $member);
        }
        if ($begin !== null) {
            $exprs[] = $qb->expr()->gte('u.lastLoginAt', $begin);
        }
        if ($end !== null) {
            $exprs[] = $qb->expr()->lte('u.lastLoginAt', $end);
        }
        if (!empty($type)) {
            $exprs[] = $qb->expr()->in('u.userType', $type);
        }
        if (!empty($exprs)) {
            $qb->where($qb->expr()->andX(...$exprs));
        }

        $qb->orderBy('u.id', 'ASC');
        return $qb->getQuery()->getResult();
    }
}
