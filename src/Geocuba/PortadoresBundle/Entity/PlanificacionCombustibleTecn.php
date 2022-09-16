<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * PlanificacionCombustibleTecn
 *
 * @ORM\Table(name="datos.planificacion_combustible_tecn", indexes={@ORM\Index(name="IDX_824270B53E15A635", columns={"equipo_tecnologico_id"}), @ORM\Index(name="IDX_824270B59D01464C", columns={"unidad_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\PlanificacionCombustibleTecnRepository")
 */
class PlanificacionCombustibleTecn
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
     * @ORM\Column(name="anno", type="integer", nullable=false)
     */
    private $anno;

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
     * @var bool
     *
     * @ORM\Column(name="aprobada", type="boolean", nullable=false)
     */
    private $aprobada = false;

    /**
     * @var EquipoTecnologico
     *
     * @ORM\ManyToOne(targetEntity="EquipoTecnologico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="equipo_tecnologico_id", referencedColumnName="id")
     * })
     */
    private $equipoTecnologico;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    private $unidad;

    /**
     * @var TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible_id", referencedColumnName="id")
     * })
     */
    private $tipoCombustible;

    /** @OneToMany(targetEntity="PlanificacionCombustibleTecnDesglose", mappedBy="planificacionCombustibleTecn", orphanRemoval=true, cascade={"persist"}) */
    private $desglose;



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
     * Set anno.
     *
     * @param int $anno
     *
     * @return PlanificacionCombustibleTecn
     */
    public function setAnno($anno): PlanificacionCombustibleTecn
    {
        $this->anno = $anno;
    
        return $this;
    }

    /**
     * Get anno.
     *
     * @return int
     */
    public function getAnno(): int
    {
        return $this->anno;
    }

    /**
     * Set cantCombustible.
     *
     * @param float $cantCombustible
     *
     * @return PlanificacionCombustibleTecn
     */
    public function setCantCombustible($cantCombustible): PlanificacionCombustibleTecn
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
     * @return PlanificacionCombustibleTecn
     */
    public function setNivelActividad($nivelActividad): PlanificacionCombustibleTecn
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
     * Set aprobada.
     *
     * @param bool $aprobada
     *
     * @return PlanificacionCombustibleTecn
     */
    public function setAprobada($aprobada): PlanificacionCombustibleTecn
    {
        $this->aprobada = $aprobada;
    
        return $this;
    }

    /**
     * Get aprobada.
     *
     * @return bool
     */
    public function getAprobada(): bool
    {
        return $this->aprobada;
    }

    /**
     * Set equipoTecnologico.
     *
     * @param EquipoTecnologico $equipoTecnologico
     *
     * @return PlanificacionCombustibleTecn
     */
    public function setEquipoTecnologico(EquipoTecnologico $equipoTecnologico): PlanificacionCombustibleTecn
    {
        $this->equipoTecnologico = $equipoTecnologico;
    
        return $this;
    }

    /**
     * Get equipoTecnologico.
     *
     * @return EquipoTecnologico
     */
    public function getEquipoTecnologico(): EquipoTecnologico
    {
        return $this->equipoTecnologico;
    }

    /**
     * Set unidad.
     *
     * @param Unidad $unidad
     *
     * @return PlanificacionCombustibleTecn
     */
    public function setUnidad(Unidad $unidad): PlanificacionCombustibleTecn
    {
        $this->unidad = $unidad;
    
        return $this;
    }

    /**
     * Get unidad.
     *
     * @return Unidad
     */
    public function getUnidad(): Unidad
    {
        return $this->unidad;
    }

    /**
     * Set tipoCombustible.
     *
     * @param TipoCombustible $tipoCombustible
     *
     * @return PlanificacionCombustibleTecn
     */
    public function setTipoCombustible(TipoCombustible $tipoCombustible): PlanificacionCombustibleTecn
    {
        $this->tipoCombustible = $tipoCombustible;

        return $this;
    }

    /**
     * Get tipoCombustible.
     *
     * @return TipoCombustible
     */
    public function getTipoCombustible(): TipoCombustible
    {
        return $this->tipoCombustible;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->desglose = new ArrayCollection();
    }

    /**
     * Add desglose.
     *
     * @param PlanificacionCombustibleTecnDesglose $desglose
     * @return PlanificacionCombustibleTecn
     */
    public function addDesglose(PlanificacionCombustibleTecnDesglose $desglose): PlanificacionCombustibleTecn
    {
        $desglose->setPlanificacionCombustibleTecn($this);
        $this->desglose[] = $desglose;

        return $this;
    }

    /**
     * Remove desglose.
     *
     * @param PlanificacionCombustibleTecnDesglose $desglose
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMedicionAfore(PlanificacionCombustibleTecnDesglose $desglose): bool
    {
        return $this->desglose->removeElement($desglose);
    }

    /**
     * Get desglose.
     *
     * @return Collection
     */
    public function getDesglose(): Collection
    {
        return $this->desglose;
    }
}
