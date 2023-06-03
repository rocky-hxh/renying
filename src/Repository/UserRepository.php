<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User[]    findByCustom($options)
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

    /**
     * Query users via options.
     *
     * Filter Options:
     *  active: ['null', '0', '1']; 'null' means option do not added or assigned
     *  member: ['null', '0', '1'];
     *  begin:  ['null', '<datetime>']
     *  end:    ['null', '<datetime>']
     *  type:   ['null', '1', '2', '3', '1,2', '1,3', '2,3', '1,2,3'];
     *
     */
    public function findByCustom($options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'active' => null,
            'member' => null,
            'begin'  => null,
            'end'    => null,
            'type'   => null,
        ]);
        $resolver->setAllowedTypes('active', ['null', 'string']);
        $resolver->setAllowedValues('active', [null, '0', '1']);
        $resolver->setAllowedTypes('member', ['null', 'string']);
        $resolver->setAllowedValues('member', [null, '0', '1']);
        $resolver->setAllowedTypes('begin', ['null', 'string']);
        $resolver->setAllowedValues('begin', function ($value) {
            if (is_null($value)) {
                return true;
            }
            try {
                $t = new \DateTimeImmutable($value);
            } catch (\Exception $e) {
                return false;
            }
            return true;
        });
        $resolver->setAllowedValues('end', function ($value) {
            if (is_null($value)) {
                return true;
            }
            try {
                $t = new \DateTimeImmutable($value);
            } catch (\Exception $e) {
                return false;
            }
            return true;
        });
        $resolver->setAllowedTypes('type', ['null', 'string']);
        $resolver->setAllowedValues('type', function ($value) {
            if (is_null($value)) {
                return true;
            }
            $allows = ['1', '2', '3'];
            $inputs = explode(',', $value);
            $union = array_unique(array_merge($allows, $inputs));
            return count($allows) == count($union);
        });

        $options = $resolver->resolve($options);

        extract($options, EXTR_OVERWRITE);

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
