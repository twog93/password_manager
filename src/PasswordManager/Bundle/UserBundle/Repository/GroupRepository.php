<?php

namespace PasswordManager\Bundle\UserBundle\Repository;

/**
 * GroupRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;


class GroupRepository extends EntityRepository
{

    public function getGroupWithUser($pattern){

        return $this->createQueryBuilder('g')
            ->leftJoin('g.users', 'u')
        ->where('u.id = :pattern')
            ->setParameter('pattern', $pattern);
    }



}

