<?php
/**
 * Created by JetBrains PhpStorm.
 * User: orlando
 * Date: 16/01/14
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class ChequeFincimexRepository extends EntityRepository
{
    /**
     * @param $_no_cheque
     * @param $_unidad
     * @param $_unidades
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     */
    public function buscarChequeFincimex($_no_cheque, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('chequeFincimex');
        if ($count)
            $qb->select('count(chequeFincimex)');

       // $qb->innerJoin('chequeFincimex.nunidadid', 'unidad');

        $qb->Where($qb->expr()->in('chequeFincimex.nunidadid', $_unidades))
            ->andWhere($qb->expr()->eq('chequeFincimex.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_no_cheque) && $_no_cheque != '') {
            $qb->andWhere('clearstr(chequeFincimex.noCheque) like clearstr(:noCheque)')
                ->setParameter('noCheque', "%$_no_cheque%");
        }

//        if (isset($_unidad) && $_unidad != '') {
//            $qb->andWhere('chequeFincimex.nunidadid = :unidad')
//                ->setParameter('unidad', "$_unidad");
//        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('chequeFincimex.fechaRegistro', 'DESC')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }

    /**
     * @param $_no_cheque
     * @param $_unidad
     * @param string $id
     * @return mixed
     */
    public function buscarChequeFincimexRepetido($_no_cheque, $_unidad, $id = '')
    {
        $qb = $this->createQueryBuilder('chequeFincimex');
        $qb->select('count(chequeFincimex)');

        $qb->andWhere($qb->expr()->eq('chequeFincimex.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_no_cheque) && ($_no_cheque != '')) {
            $qb->andWhere('clearstr(chequeFincimex.noCheque) = clearstr(:noCheque)')
                ->setParameter('noCheque', $_no_cheque);
        }

        if (isset($_unidad) && $_unidad != '') {
            $qb->andWhere('chequeFincimex.nunidadid = :unidad')
                ->setParameter('unidad', $_unidad);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('chequeFincimex.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }



}