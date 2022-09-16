<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pfcadenas
 * Date: 15/12/2016
 * Time: 16:20
 * To change this template use File | Settings | File Templates.
 */

namespace Geocuba\PortadoresBundle\Repository;
use Doctrine\ORM\EntityRepository;

class MunicipioRepository extends EntityRepository
{
    /**
     * @param $_provincia
     * @param $_nombre
     * @param string $_codigo
     * @param string $id
     * @return mixed
     */
    public function buscarMunicipioRepetido($_provincia, $_nombre, $_codigo = '', $id = '')
    {
        $qb = $this->createQueryBuilder('municipio');
        $qb->select('count(municipio)');

        $qb->andWhere($qb->expr()->eq('municipio.visible', ':visible'))
            ->setParameter('visible', true);

        if (isset($_provincia) && $_provincia != '') {
            $qb->andWhere('municipio.provinciaid = :provincia')
                ->setParameter('provincia', $_provincia);
        }

        if (isset($_nombre) && $_nombre != '') {
            $qb->andWhere('lower(municipio.nombre) = lower(:nombre)')
                ->setParameter('nombre', $_nombre);
        }

        if (isset($_codigo) && $_codigo != '') {
            $qb->andWhere('municipio.codigo = :codigo')
                ->setParameter('codigo', $_codigo);
        }

        if (isset($id) && $id != '') {
            $qb->andWhere('municipio.id != :id')
                ->setParameter('id', $id);
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}