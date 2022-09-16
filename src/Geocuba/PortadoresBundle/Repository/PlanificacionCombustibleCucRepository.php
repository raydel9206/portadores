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

class PlanificacionCombustibleCucRepository extends EntityRepository {

    public function FindListPlanificacion($start = null, $limit = null)
    {
        $consulta = $this->getEntityManager()->createQuery('Select a FROM PortadoresBundle:PlanificacionCombustibleCuc a
        where a.visible = true Order By a.id ASC');

        if($limit != -1)
        {
            $consulta->setMaxResults($limit);
            $consulta->setFirstResult($start);
        }

        return $consulta->getResult();

    }

    /**
     * @param $_vehiculo
     * @param $mes
     * @param $_anno
     * @return float|int
     */
    public function planificacionVehiculo($_vehiculo, $mes, $_anno)
    {
        $qb = $this->createQueryBuilder('PlanificacionCombustible');

        $qb->innerJoin('PlanificacionCombustible.vehiculoid', 'vehiculo')
            ->where($qb->expr()->eq('PlanificacionCombustible.aprobada', ':aprobada'))
            ->setParameter('aprobada', true)
            ->andWhere($qb->expr()->eq('PlanificacionCombustible.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_vehiculo !== null && $_vehiculo != '') {
            $qb->andWhere($qb->expr()->eq('PlanificacionCombustible.vehiculoid', ':vehilulo'))
                ->setParameter('vehilulo', $_vehiculo);
        }

        if ($_anno !== null && $_anno != '') {
            $qb->andWhere($qb->expr()->eq('PlanificacionCombustible.anno', ':anno'))
                ->setParameter('anno', $_anno);
        }

        $planificacion = $qb->getQuery()->getResult();

        if (\count($planificacion) == 0) {
            return 0;
        }
        else {
            switch ($mes) {
                case 1:
                    return $planificacion[0]->getCombustibleLitrosEne();
                    break;
                case 2:
                    return $planificacion[0]->getCombustibleLitrosFeb();
                    break;
                case 3:
                    return $planificacion[0]->getCombustibleLitrosMar();
                    break;
                case 4:
                    return $planificacion[0]->getCombustibleLitrosAbr();
                    break;
                case 5:
                    return $planificacion[0]->getCombustibleLitrosMay();
                    break;
                case 6:
                    return $planificacion[0]->getCombustibleLitrosJun();
                    break;
                case 7:
                    return $planificacion[0]->getCombustibleLitrosJul();
                    break;
                case 8:
                    return $planificacion[0]->getCombustibleLitrosAgo();
                    break;
                case 9:
                    return $planificacion[0]->getCombustibleLitrosSep();
                    break;
                case 10:
                    return $planificacion[0]->getCombustibleLitrosOct();
                    break;
                case 11:
                    return $planificacion[0]->getCombustibleLitrosNov();
                    break;
				case 13:
                    return $planificacion[0]->getCombustibleLitrosTotal();
                    break;
                default:
                    return $planificacion[0]->getCombustibleLitrosDic();
            }
        }
    }

    public function planificacionLubricanteVehiculo($_vehiculo, $mes, $_anno)
    {
        $qb = $this->createQueryBuilder('PlanificacionCombustible');

        $qb->innerJoin('PlanificacionCombustible.vehiculoid', 'vehiculo')
            ->where($qb->expr()->eq('PlanificacionCombustible.aprobada', ':aprobada'))
            ->setParameter('aprobada', true)
            ->andWhere($qb->expr()->eq('PlanificacionCombustible.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_vehiculo !== null && $_vehiculo != '') {
            $qb->andWhere($qb->expr()->eq('PlanificacionCombustible.vehiculoid', ':vehilulo'))
                ->setParameter('vehilulo', $_vehiculo);
        }

        if ($_anno !== null && $_anno != '') {
            $qb->andWhere($qb->expr()->eq('PlanificacionCombustible.anno', ':anno'))
                ->setParameter('anno', $_anno);
        }

        $planificacion = $qb->getQuery()->getResult();

        if (\count($planificacion) == 0) {
            return 0;
        }
        else {
            switch ($mes) {
                case 1:
                    return $planificacion[0]->getLubricanteEne();
                    break;
                case 2:
                    return $planificacion[0]->getLubricanteFeb();
                    break;
                case 3:
                    return $planificacion[0]->getLubricanteMar();
                    break;
                case 4:
                    return $planificacion[0]->getLubricanteAbr();
                    break;
                case 5:
                    return $planificacion[0]->getLubricanteMay();
                    break;
                case 6:
                    return $planificacion[0]->getLubricanteJun();
                    break;
                case 7:
                    return $planificacion[0]->getLubricanteJul();
                    break;
                case 8:
                    return $planificacion[0]->getLubricanteAgo();
                    break;
                case 9:
                    return $planificacion[0]->getLubricanteSep();
                    break;
                case 10:
                    return $planificacion[0]->getLubricanteOct();
                    break;
                case 11:
                    return $planificacion[0]->getLubricanteNov();
                    break;
                default:
                    return $planificacion[0]->getLubricanteDic();
            }
        }
    }

    public function planificacionMotorrecursoVehiculo($_vehiculo, $mes, $_anno)
    {
        $qb = $this->createQueryBuilder('PlanificacionCombustible');

        $qb->innerJoin('PlanificacionCombustible.vehiculoid', 'vehiculo')
            ->where($qb->expr()->eq('PlanificacionCombustible.aprobada', ':aprobada'))
            ->setParameter('aprobada', true)
            ->andWhere($qb->expr()->eq('PlanificacionCombustible.visible', ':visible'))
            ->setParameter('visible', true);

        if ($_vehiculo !== null && $_vehiculo != '') {
            $qb->andWhere($qb->expr()->eq('PlanificacionCombustible.vehiculoid', ':vehilulo'))
                ->setParameter('vehilulo', $_vehiculo);
        }

        if ($_anno !== null && $_anno != '') {
            $qb->andWhere($qb->expr()->eq('PlanificacionCombustible.anno', ':anno'))
                ->setParameter('anno', $_anno);
        }

        $planificacion = $qb->getQuery()->getResult();

        if (\count($planificacion) == 0) {
            return 0;
        }
        else {
            switch ($mes) {
                case 1:
                    return $planificacion[0]->getNivelActKmsEne();
                    break;
                case 2:
                    return $planificacion[0]->getNivelActKmsFeb();
                    break;
                case 3:
                    return $planificacion[0]->getNivelActKmsMar();
                    break;
                case 4:
                    return $planificacion[0]->getNivelActKmsAbr();
                    break;
                case 5:
                    return $planificacion[0]->getNivelActKmsMay();
                    break;
                case 6:
                    return $planificacion[0]->getNivelActKmsJun();
                    break;
                case 7:
                    return $planificacion[0]->getNivelActKmsJul();
                    break;
                case 8:
                    return $planificacion[0]->getNivelActKmsAgo();
                    break;
                case 9:
                    return $planificacion[0]->getNivelActKmsSep();
                    break;
                case 10:
                    return $planificacion[0]->getNivelActKmsOct();
                    break;
                case 11:
                    return $planificacion[0]->getNivelActKmsNov();
                    break;
				case 13:
                    return $planificacion[0]->getNivelActKmsTotal();
                    break;
                default:
                    return $planificacion[0]->getNivelActKmsDic();
            }
        }
    }
}