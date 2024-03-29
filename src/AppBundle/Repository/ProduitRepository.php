<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * ProduitRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProduitRepository extends EntityRepository
{
    public function findProduct($name)
    {
        $query=$this->getEntityManager()->createQuery("SELECT p FROM AppBundle:Produit p where p.nom= :name")
            ->setParameter('name', $name);
        return $query->getResult();}
    public function getAllProduct($promotion){
      $query=$this->getEntityManager()->createQuery( "select p.nom ,lp.id,lp.quantite
    FROM AppBundle:Produit as p INNER JOIN AppBundle:ligne_promotion as lp with lp.produit=p.id
    INNER JOIN AppBundle:promotion as x with lp.promotion=x.id
    WHERE lp.promotion= :promotion")
          ->setParameter('promotion',$promotion);
      return $query->getResult();
    }
}
