<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Javier
 * Date: 16/01/14
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class PersonaRepository extends EntityRepository
{
    /**
     * @param $_nombre
     * @param $_operarioTaller
     * @param $_unidades
     * @param null $start
     * @param null $limit
     * @param bool $count
     * @return array|mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarPersona($_nombre, $_operarioTaller, $_unidades, $start = null, $limit = null, $count = false)
    {
        $qb = $this->createQueryBuilder('persona');
        if ($count)
            $qb->select('count(persona)');

        $qb->innerJoin('persona.nunidadid', 'unidad');

        $qb->andWhere($qb->expr()->eq('persona.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_unidades !== null && $_unidades != '') {
            $qb->andWhere($qb->expr()->in('persona.nunidadid', $_unidades));
        }

        if ($_nombre !== null && $_nombre != '') {
            $qb->andWhere('lower(persona.nombre) like lower(:nombre)')
                ->setParameter('nombre', "%$_nombre%");
        }

        if ($_operarioTaller !== null && $_operarioTaller != '') {
            $qb->andWhere('persona.operarioTaller = :operarioTaller')
                ->setParameter('operarioTaller', $_operarioTaller);
        }

        if ($count) {
            return $qb->getQuery()->getSingleScalarResult();
        } else {
            return $qb->orderBy('unidad.nombre')
                ->addOrderBy('persona.nombre')
                ->setMaxResults($limit)
                ->setFirstResult($start)
                ->getQuery()->getResult();
        }
    }
    
    public function buscarPersonaCombo($_unidades)
    {
        $qb = $this->createQueryBuilder('persona');
        $qb->select('persona.id','persona.nombre','unidad.id as nunidadid');

        $qb->innerJoin('persona.nunidadid', 'unidad')
            ->where($qb->expr()->in('persona.nunidadid', ':unidades'))
            ->setParameter('unidades',$_unidades)
            ->andWhere($qb->expr()->eq('persona.visible', ':visible'))
            ->setParameter('visible', true);

        return $qb->orderBy('persona.nombre', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $_ci
     * @param string $id
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function buscarPersonaRepetido($_ci, $id = '')
    {
        $qb = $this->createQueryBuilder('persona');
        $qb->select('count(persona)');

        $qb->andWhere($qb->expr()->eq('persona.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_ci !== null && $_ci != '') {
            $qb->andWhere('lower(persona.ci) = lower(:ci)')
                ->setParameter('ci', $_ci);
        }

        if ($id !== null && $id != '') {
            $qb->andWhere('persona.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}