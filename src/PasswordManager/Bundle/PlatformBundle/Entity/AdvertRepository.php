<?php

namespace PasswordManager\Bundle\PlatformBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class AdvertRepository extends EntityRepository{
  
  public function getAdvertWithCategories(array $categoryNames)
  {
    $qb = $this->createQueryBuilder('a');

    // On fait une jointure avec l'entité Category avec pour alias « c »
    $qb
      ->join('a.categories', 'c')
      ->addSelect('c')
    ;

    // Puis on filtre sur le nom des catégories à l'aide d'un IN
    $qb->where($qb->expr()->in('c.name', $categoryNames));
    // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

    // Enfin, on retourne le résultat
    return $qb
      ->getQuery()
      ->getResult()
    ;
  }
    public function getAdvertWithCategoriesByAuthor($user_id, array $categoryNames)
    {
        $qb = $this->createQueryBuilder('a');

        // On fait une jointure avec l'entité Category avec pour alias « c »
        $qb
            ->join('a.categories', 'c')
            ->addSelect('c')
        ;

        // Puis on filtre sur le nom des catégories à l'aide d'un IN
        $qb->where($qb->expr()->in('c.name', $categoryNames))->andWhere('a.user = :user_id')->setParameter('user_id', $user_id);
        // Enfin, on retourne le résultat
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }


   public function getAdverts($page, $nbPerPage)
  {
     $query = $this->createQueryBuilder('a')
      // Jointure sur l'attribut image
      ->leftJoin('a.image', 'i')
      ->addSelect('i')
      // Jointure sur l'attribut categories
      ->leftJoin('a.categories', 'c')
      ->addSelect('c')
      ->orderBy('a.date', 'DESC')
      ->getQuery();
      
    $query->setFirstResult(($page-1) * $nbPerPage)->setMaxResults($nbPerPage);

    return new Paginator($query, true);

  }
    public function myFindUserId($user_id)
    {

        $qb = $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->setParameter('user', $user_id)
        ;

        return $qb
            ->getQuery()
            ->getResult();
        
    }

    public function myFindGroup($groupNames)
    {


        $qb = $this->createQueryBuilder('a');
        $qb
            ->join('a.fos_group', 'g')
            ->addSelect('g');

        $qb->where($qb->expr()->in('g.name', $groupNames));

        return $qb
            ->getQuery()
            ->getResult();

    }

    public function getAdvertWithGroupByAuthor(array $groupNames)
    {
        $qb = $this->createQueryBuilder('g');

        // jointure avec l'entité Group avec pour alias « g »
        $qb
            ->join('g.groups', 'a')
            ->addSelect('a')
        ;

        // Puis on filtre sur le nom des groups à l'aide d'un IN
        $qb->where($qb->expr()->in('a.name', $groupNames));
        // Enfin, on retourne le résultat
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    /*public function getAdvertWithGroupByAuthor(array $groupNames)
    {
        $qb = $this->createQueryBuilder('a');

        // jointure avec l'entité Group avec pour alias « g »
        $qb
            ->join('a.groups', 'g')
            ->addSelect('g')
        ;

        // Puis on filtre sur le nom des groups à l'aide d'un IN
        $qb->where($qb->expr()->in('g.name', $groupNames));
        // Enfin, on retourne le résultat
        return $qb
            ->getQuery()
            ->getResult()
            ;
    }*/



}
