<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlanificacionCombustibleTecnDesglose
 *
 * @ORM\Table(name="datos.planificacion_combustible_tecn_desglose", indexes={@ORM\Index(name="IDX_45973DBD6B45D31A", columns={"planificacion_combustible_tecn_id"})})
 * @ORM\Entity
 */
class PlanificacionCombustibleTecnDesglose
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="mes", type="integer", nullable=false)
     */
    private $mes;

    /**
     * @var float
     *
     * @ORM\Column(name="cant_combustible", type="float", precision=10, scale=0, nullable=false)
     */
    private $cantCombustible = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_actividad", type="float", precision=10, scale=0, nullable=false)
     */
    private $nivelActividad = '0';

    /**
     * @var PlanificacionCombustibleTecn
     *
     * @ORM\ManyToOne(targetEntity="PlanificacionCombustibleTecn")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="planificacion_combustible_tecn_id", referencedColumnName="id")
     * })
     */
    private $planificacionCombustibleTecn;



    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set mes.
     *
     * @param int $mes
     *
     * @return PlanificacionCombustibleTecnDesglose
     */
    public function setMes($mes): PlanificacionCombustibleTecnDesglose
    {
        $this->mes = $mes;
    
        return $this;
    }

    /**
     * Get mes.
     *
     * @return int
     */
    public function getMes(): int
    {
        return $this->mes;
    }

    /**
     * Set cantCombustible.
     *
     * @param float $cantCombustible
     *
     * @return PlanificacionCombustibleTecnDesglose
     */
    public function setCantCombustible($cantCombustible): PlanificacionCombustibleTecnDesglose
    {
        $this->cantCombustible = $cantCombustible;
    
        return $this;
    }

    /**
     * Get cantCombustible.
     *
     * @return float
     */
    public function getCantCombustible(): float
    {
        return $this->cantCombustible;
    }

    /**
     * Set nivelActividad.
     *
     * @param float $nivelActividad
     *
     * @return PlanificacionCombustibleTecnDesglose
     */
    public function setNivelActividad($nivelActividad): PlanificacionCombustibleTecnDesglose
    {
        $this->nivelActividad = $nivelActividad;
    
        return $this;
    }

    /**
     * Get nivelActividad.
     *
     * @return float
     */
    public function getNivelActividad(): float
    {
        return $this->nivelActividad;
    }

    /**
     * Set planificacionCombustibleTecn.
     *
     * @param PlanificacionCombustibleTecn $planificacionCombustibleTecn
     *
     * @return PlanificacionCombustibleTecnDesglose
     */
    public function setPlanificacionCombustibleTecn(PlanificacionCombustibleTecn $planificacionCombustibleTecn = null): PlanificacionCombustibleTecnDesglose
    {
        $this->planificacionCombustibleTecn = $planificacionCombustibleTecn;
    
        return $this;
    }

    /**
     * Get planificacionCombustibleTecn.
     *
     * @return PlanificacionCombustibleTecn
     */
    public function getPlanificacionCombustibleTecn(): PlanificacionCombustibleTecn
    {
        return $this->planificacionCombustibleTecn;
    }
}
