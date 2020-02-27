<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CommentaireRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentaireRepository extends EntityRepository
{

    public function getPostComments($id){
        return $this->getEntityManager()
            ->createQuery(
                "SELECT c, u.nom
       FROM AppBundle:Commentaire c
       JOIN c.user u
       WHERE c.commentaire = :id"
            )
            ->setParameter('id', $id)
            ->getArrayResult();
    }

}
