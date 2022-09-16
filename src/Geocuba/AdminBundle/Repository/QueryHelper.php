<?php

namespace Geocuba\AdminBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\{
    AbstractQuery, EntityManagerInterface, EntityRepository, NoResultException, Repository\RepositoryFactory
};
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class QueryHelper
 * @package Geocuba\AdminBundle\Repository
 */
abstract class QueryHelper
{
    /**
     * @param EntityRepository|ObjectRepository $repository
     * @param string $field_name
     * @param $field_value
     * @param bool $active_field_name
     * @param $active_field_value
     * @param string $id_field_name
     * @param $id_field_value
     * @param $type_string
     * @return array|mixed
     */
    public static function findByFieldValue($repository, $field_name, $field_value, $active_field_name, $active_field_value, $id_field_name, $id_field_value, $type_string = true)
    {
        $qb = $repository->createQueryBuilder('entity');

        $qb->where(
            $type_string ? $qb->expr()->eq('TRIM(LOWER(entity.' . $field_name . '))', ':value') : $qb->expr()->eq('(entity.' . $field_name . ')', ':value')
        )->setParameter('value', $type_string ? mb_strtolower(trim($field_value), 'UTF-8') : $field_value);

        if ($active_field_name && is_bool($active_field_value)) {
            $qb->andWhere($qb->expr()->eq('entity.' . $active_field_name, $qb->expr()->literal($active_field_value)));
        }

        if ($id_field_name && !is_null($id_field_value)) {
            $qb->andWhere($qb->expr()->neq('entity.' . $id_field_name, $id_field_value));
        }

        // error_log(print_r($qb->getQuery()->getSQL(), true));

        return $qb->setCacheable(true)->getQuery()->getArrayResult();
    }

    /**
     * Devuelve la partición de la entidad según el límite y el criterio de ordenamiento especificados.
     *
     * @param EntityManagerInterface $manager
     * @param EntityRepository $repository
     * @param string $id_field_name
     * @param $id_field_value
     * @param int $limit
     * @param array|string $order_by
     * @return int
     */
    public static function findPartition($manager, $repository, $id_field_name, $id_field_value, $limit, $order_by)
    {
        try {
            $metadata = $manager->getClassMetadata($repository->getClassName());
            $schema = $metadata->getSchemaName();
            $table = $metadata->getTableName();

//            if (is_array($order_by)) {
//                $order_by = array_reduce(array_keys($order_by), function ($carry, $item) use ($order_by) {
//                    return $carry . ($carry === '' ? '' : ', ') . ($item . ' ' . $order_by[$item]);
//                }, '');
//            }

            $sql = sprintf('SELECT row FROM (SELECT %1$s,row_number() OVER (ORDER BY e.%2$s) AS row FROM %3$s%4$s e) AS x WHERE %1$s = :id', $id_field_name, $order_by, $schema ? $schema . '.' : '', $table);
            $rsm = (new ResultSetMapping())->addScalarResult('row', 'row', 'integer');

            $offset = $manager
                ->createNativeQuery($sql, $rsm)
                ->setParameter('id', $id_field_value)
                ->setCacheable(true)
                ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

            return intval($limit) > 0 ? (intval(intval($offset) / intval($limit)) + 1) : 1;
        } catch (NoResultException $e) {
            return 1;
        }
    }

    /**
     * Devuelve la partición de la entidad según el límite y el criterio de ordenamiento especificados.
     *
     * @param EntityManagerInterface $manager
     * @param EntityRepository $repository
     * @param string $id_field_name
     * @param $id_field_value
     * @param int $limit
     * @param array|string $order_by
     * @return int
     */
    //TODO boolean a string se pone en 1
    public static function findPartitionbyCriteria($manager, $repository, $criteria = [], $id_field_name, $id_field_value, $limit, $order_by){
        try {
            $metadata = $manager->getClassMetadata($repository->getClassName());
            $schema = $metadata->getSchemaName();
            $table = $metadata->getTableName();

//            if (is_array($order_by)) {
//                $order_by = array_reduce(array_keys($order_by), function ($carry, $item) use ($order_by) {
//                    return $carry . ($carry === '' ? '' : ', ') . ($item . ' ' . $order_by[$item]);
//                }, '');
//            }

            if($criteria){
                $where = 'WHERE';
                $i=0;
                foreach ($criteria as $atributo=>$valor){
                    if($i == 0){
                        $where .= ' e.'.$atributo.' = '.$valor;
                    }
                    else {
                        $where .= ' and e.' . $atributo . ' = ' .$valor;
                    }
                    $i++;
                }
            }

            $sql = sprintf('SELECT row FROM (SELECT %1$s,row_number() OVER (ORDER BY e.%2$s) AS row FROM %3$s%4$s e %5$s) AS x WHERE %1$s = :id', $id_field_name, $order_by, $schema ? $schema . '.' : '', $table ,$where);
            $rsm = (new ResultSetMapping())->addScalarResult('row', 'row', 'integer');

            $offset = $manager
                ->createNativeQuery($sql, $rsm)
                ->setParameter('id', $id_field_value)
                ->setCacheable(true)
                ->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

            return intval($limit) > 0 ? (intval(intval($offset) / intval($limit)) + 1) : 1;
        } catch (NoResultException $e) {
            return 1;
        }
    }

    /**
     * @param EntityManagerInterface $em
     * @param string $entity_name
     * @param array $criteria
     * @param int|null $limit
     * @param int|null $start
     * @param array $order_by
     * @return array
     * @internal param Request $request
     */
    public static function findBy($em, $entity_name, $criteria = [], $limit = null, $start = null, $order_by = []){
        $entity_er = $em->getRepository($entity_name);

        $limited_result = $entity_er->findBy($criteria, $order_by, $limit, $start);
        $result = $limit ? $entity_er->findBy($criteria, $order_by) : $limited_result;

        $rows = [];
        foreach ($limited_result as $obj) {
            /** @var object $obj */
            $rows[] = $obj->toArray();
            $em->detach($obj);
        }

        return [$rows, count($result)];
    }

    /**
     * @param EntityManagerInterface $em
     * @param object $entity
     * @param int|null $limit
     * @param array|null $order_by
     * @return int
     * @throws \Exception
     */
    public static function persist($em, $entity, $limit = null, $order_by = [])    {
        try {
            $em->transactional(function () use ($entity) {
                /** @var EntityManagerInterface $em */
                $em = func_get_arg(0);

                $em->persist($entity);
            });

            $em->clear();
        } catch (\Exception $e) {
            $em->clear();
            throw $e;
        }

        return $limit ? self::findPartition($em, $em->getRepository(get_class($entity)), 'id', $entity->getId(), $limit, $order_by) : 1;
    }

    //TODO
//    public static function countFindBy($repository, $criteria){
//        $qb = $repository->createQueryBuilder('entity');
//        $qb->select('count(entity)');
//        foreach ($criteria as $atributo=>$valor){
//            $qb->andWhere('entity.'.$atributo.' = :'.$atributo)
//                ->setParameter($atributo, $valor);
//        }
//        return $qb->setCacheable(true)->getQuery()->getSingleScalarResult();
//    }


}