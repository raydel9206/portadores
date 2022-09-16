<?php

namespace Geocuba\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Geocuba\AdminBundle\Entity\{
    Notificacion, Usuario
};

/**
 * Class NotificacionRepository
 * @package Geocuba\AdminBundle\Repository
 */
class NotificacionRepository extends EntityRepository
{
    /**
     * @param boolean $only_session
     * @param Usuario|null|object $user
     * @param $limit
     * @param $offset
     * @return Notificacion[]
     */
    public function findAllBy($only_session, $user, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('notificacion');

        if ($only_session) {
            $qb->where(
                $qb->expr()->andX(
                    $user ? $qb->expr()->eq('notificacion.usuario', ':usuario') : $qb->expr()->isNull('notificacion.usuario'),
                    $qb->expr()->isNull('notificacion.fechaAceptacion')
                )
            );

            if ($user) {
                $qb->setParameter('usuario', $user);
            }
        }

        $qb->setMaxResults($limit)->setFirstResult($offset)
            ->orderBy('notificacion.fechaCreacion', 'DESC')->addOrderBy('notificacion.fechaAceptacion', 'DESC')->addOrderBy('notificacion.mensaje', 'DESC');

        return $qb->setCacheable(true)->getQuery()->getResult();
    }
}
